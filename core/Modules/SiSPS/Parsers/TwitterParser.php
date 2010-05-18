<?php
namespace Swiftriver\Core\Modules\SiSPS\Parsers;
class TwitterParser implements IParser {
    /**
     * This method returns a string array with the names of all
     * the source types this parser is designed to parse. For example
     * the RSSParser may return array("Blogs", "News Feeds");
     *
     * @return string[]
     */
    public function ListSubTypes() {
        return array(
            "Search",
            "Follow User"
        );
    }

    /**
     * This method returns a string describing the type of sources
     * it can parse. For example, the RSSParser returns "Feeds".
     *
     * @return string type of sources parsed
     */
    public function ReturnType() {
        return "Twitter";
    }

    /**
     * This method returns an array of the required paramters that
     * are nessesary to run this parser. The Array should be in the
     * following format:
     * array(
     *  "SubType" => array ( ConfigurationElements )
     * )
     *
     * @return array()
     */
    public function ReturnRequiredParameters(){
        return array(
            "Search" => array (
                new \Swiftriver\Core\ObjectModel\ConfigurationElement(
                        "SearchKeyword",
                        "string",
                        "The keyword(s) to search for"
                )
            ),
            "Follow User" => array(
                new \Swiftriver\Core\ObjectModel\ConfigurationElement(
                        "TwitterAccount",
                        "string",
                        "The account name of the Twitter user"
                )
            )
        );
    }

    /**
     * Given a set of parameters, this method should
     * fetch content from a channel and parse each
     * content into the Swiftriver object model :
     * Content Item. The $lastsucess datetime is passed
     * to the function to ensure that content that has
     * already been parsed is not duplicated.
     *
     * @param \Swiftriver\Core\ObjectModel\Channel $channel
     * @return Swiftriver\Core\ObjectModel\Content[] contentItems
     */
    public function GetAndParse($channel) {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetAndParse [Method invoked]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetAndParse [START: Switching processing based on subType]", \PEAR_LOG_DEBUG);

        $content = array();

        switch ($channel->subType) {
            case "Search" : $content = $this->GetForTwitterSearch($channel); break;
            case "Follow User" : $content = $this->GetForTwitterAccount($channel); break;
            default : $content = array();
        }

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetAndParse [END: Switching processing based on subType]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetAndParse [Method invoked]", \PEAR_LOG_DEBUG);

        return $content;
    }

    /**
     * Uses the twitter search api to return content from 
     * twitter.
     * 
     * @param \Swiftriver\Core\ObjectModel\Channel $channel
     * @return \Swiftriver\Core\ObjectModel\Content[]
     */
    private function GetForTwitterSearch($channel) {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [Method invoked]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [START: Extracting required parameters]", \PEAR_LOG_DEBUG);

        //Extract the required variables
        $SearchKeyword = $channel->parameters["SearchKeyword"];
        if(!isset($SearchKeyword) || ($SearchKeyword == "")) {
            $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [the parapeter 'SearchKeyword' was not supplued. Returning null]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [Method finished]", \PEAR_LOG_DEBUG);
            return null;
        }

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [END: Extracting required parameters]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [START: Including the SimplePie module]", \PEAR_LOG_DEBUG);

        //Include the Simple Pie Framework to get and parse feeds
        $config = \Swiftriver\Core\Setup::Configuration();
        include_once $config->ModulesDirectory."/SimplePie/simplepie.inc";

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [END: Including the SimplePie module]", \PEAR_LOG_DEBUG);

        //Construct a new SimplePie Parsaer
        $feed = new \SimplePie();

        //Get the cach directory
        $cacheDirectory = $config->CachingDirectory;

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [Setting the caching directory to $cacheDirectory]", \PEAR_LOG_DEBUG);

        //Set the caching directory
        $feed->set_cache_location($cacheDirectory);

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [Setting the feed url to $feedUrl]", \PEAR_LOG_DEBUG);

        //Twitter url combined with the account name passed to this feed.
        $TwitterUrl = "http://search.twitter.com/search.atom?rpp=50&q=".urlencode($SearchKeyword);

        //if there is a last sucess then set it
        if(isset($channel->lastSucess) && $channel->lastSucess != null) {
            $since = date("Y-m-d", $channel->lastSucess);
            $TwitterUrl .= "&since=$since";
        }
        
        //Pass the feed URL to the SImplePie object
        $feed->set_feed_url($TwitterUrl);

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [Initilising the feed]", \PEAR_LOG_DEBUG);

        //Run the SimplePie
        $feed->init();

        //Create the Content array
        $contentItems = array();

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [START: Parsing feed items]", \PEAR_LOG_DEBUG);

        $tweets = $feed->get_items();

        if(!$tweets || $tweets == null || !is_array($tweets) || count($tweets) < 1) {
            $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [No feeditems recovered from the feed]", \PEAR_LOG_DEBUG);
        }

        //Loop throught the Feed Items
        foreach($tweets as $tweet) {
            //Extract the date of the content
            $contentdate = strtotime($tweet->get_date());
            if(isset($channel->lastSucess) && is_numeric($channel->lastSucess) && isset($contentdate) && is_numeric($contentdate)) {
                if($contentdate < $channel->lastSucess) {
                    $textContentDate = date("c", $contentdate);
                    $textLastSucess = date("c", $channel->lastSucess);
                    $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [Skipped feed item as date $textContentDate less than last sucessful run ($textLastSucess)]", \PEAR_LOG_DEBUG);
                    continue;
                }
            }

            $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [Adding feed item]", \PEAR_LOG_DEBUG);

            //Extract all the relevant feedItem info
            $item = $this->ParseTweetFromATOMItem($tweet, $channel);

            //Add the item to the Content array
            $contentItems[] = $item;
        }

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [END: Parsing feed items]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [Method finished]", \PEAR_LOG_DEBUG);

        //return the content array
        return $contentItems;
        
    }

    /**
     * User the twitter RSS call to follow the tweets of a given
     * twitter user.
     * 
     * @param \Swiftriver\Core\ObjectModel\Source $source 
     * @return \Swiftriver\Core\ObjectModel\Content[]
     */
    private function GetForTwitterAccount($source) {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [Method invoked]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [START: Extracting required parameters]", \PEAR_LOG_DEBUG);

        //Extract the required variables
        $TwitterAccount = $source->parameters["TwitterAccount"];
        if(!isset($TwitterAccount) || ($TwitterAccount == "")) {
            $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [the parapeter 'TwitterAccount' was not supplued. Returning null]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [Method finished]", \PEAR_LOG_DEBUG);
            return null;
        }

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [END: Extracting required parameters]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [START: Including the SimplePie module]", \PEAR_LOG_DEBUG);

        //Include the Simple Pie Framework to get and parse feeds
        $config = \Swiftriver\Core\Setup::Configuration();
        include_once $config->ModulesDirectory."/SimplePie/simplepie.inc";

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [END: Including the SimplePie module]", \PEAR_LOG_DEBUG);

        //Construct a new SimplePie Parsaer
        $feed = new \SimplePie();

        //Get the cach directory
        $cacheDirectory = $config->CachingDirectory;

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [Setting the caching directory to $cacheDirectory]", \PEAR_LOG_DEBUG);

        //Set the caching directory
        $feed->set_cache_location($cacheDirectory);

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [Setting the feed url to $feedUrl]", \PEAR_LOG_DEBUG);

        //Twitter url combined with the account name passed to this feed.
        $TwitterUrl = "http://twitter.com/statuses/user_timeline/".$TwitterAccount.".rss";

        //Pass the feed URL to the SImplePie object
        $feed->set_feed_url($TwitterUrl);

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [Initilising the feed]", \PEAR_LOG_DEBUG);

        //Run the SimplePie
        $feed->init();

        //Create the Content array
        $contentItems = array();

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [START: Parsing feed items]", \PEAR_LOG_DEBUG);

        $tweets = $feed->get_items();

        if(!$tweets || $tweets == null || !is_array($tweets) || count($tweets) < 1) {
            $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [No feeditems recovered from the feed]", \PEAR_LOG_DEBUG);
        }

        //Loop throught the Feed Items
        foreach($tweets as $tweet) {
            //Extract the date of the content
            $contentdate = strtotime($tweet->get_date());
            if(isset($source->lastSucess) && is_numeric($source->lastSucess) && isset($contentdate) && is_numeric($contentdate)) {
                if($contentdate < $source->lastSucess) {
                    $textContentDate = date("c", $contentdate);
                    $textLastSucess = date("c", $source->lastSucess);
                    $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [Skipped feed item as date $textContentDate less than last sucessful run ($textLastSucess)]", \PEAR_LOG_DEBUG);
                    continue;
                }
            }

            $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [Adding feed item]", \PEAR_LOG_DEBUG);

            $item = $this->ParseTweetFromATOMItem($tweet, $source);

            //Add the item to the Content array
            $contentItems[] = $item;
        }

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [END: Parsing feed items]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [Method finished]", \PEAR_LOG_DEBUG);

        //return the content array
        return $contentItems;
    }

    /**
     * Method for parsing the json returned from the curl oppertation
     * to content items.
     *
     * @param string json $data
     * @param \Swiftriver\Core\ObjectModel\Source $source
     * @return \Swiftriver\Core\ObjectModel\Content[]
     */
    private function ParseTweetsFromJSON($data, $source){
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::ParseTweetsFromJSON [Method invoked]", \PEAR_LOG_DEBUG);

        $tweets = json_decode($data, false);

        if(!$tweets || $tweets == null || !is_array($tweets) || count($tweets) < 1) {
            $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::ParseTweetsFromJSON [No feeditems recovered from the feed]", \PEAR_LOG_DEBUG);
            return array();
        }

        if (array_key_exists('results', $tweets)) {
            $tweets = $tweets->{'results'};
        }

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::ParseTweetsFromJSON [START: Looping through tweets]", \PEAR_LOG_DEBUG);

        $content = array();

        foreach($tweets as $tweet)
        {
            //Extract the date of the content
            $contentdate = strtotime($tweet->{'created_at'});
            if(isset($source->lastSucess) && is_numeric($source->lastSucess) && isset($contentdate) && is_numeric($contentdate)) {
                if($contentdate < $source->lastSucess) {
                    $textContentDate = date("c", $contentdate);
                    $textLastSucess = date("c", $source->lastSucess);
                    $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::ParseTweetsFromJSON [Skipped feed item as date $textContentDate less than last sucessful run ($textLastSucess)]", \PEAR_LOG_DEBUG);
                    continue;
                }
            }

            $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::ParseTweetsFromJSON [START: Adding feed item]", \PEAR_LOG_DEBUG);

            //Setup the variables to be used in the content item.
            $title = $tweet->{'text'};
            $contentLink = $tweet->{'source'};
            $date = $tweet->{'created_at'};
            $tweet_user_id = $tweet->{'from_user_id'};
            $tweet_id = $tweet->{'id'};
            $langcode = $tweet->{'iso_language_code'};

            //Create a new Content item
            $item = \Swiftriver\Core\ObjectModel\ObjectFactories\ContentFactory::CreateContent($source);

            //Fill the Content Item
            $item->text[] = new \Swiftriver\Core\ObjectModel\LanguageSpecificText(
                    $langcode,
                    $title,
                    array());
            $item->link = $contentLink;
            $item->date = strtotime($date);

            //Add the item to the Content array
            $content[] = $item;

            $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::ParseTweetsFromJSON [END: Adding feed item]", \PEAR_LOG_DEBUG);
        }

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::ParseTweetsFromJSON [END: Looping through tweets]", \PEAR_LOG_DEBUG);

        return $content;
    }

    /**
     * Parses the simplepie item to a content item
     * @param \SimplePie_Item $tweet
     * @param \Swiftriver\Core\ObjectModel\Source
     * @return \Swiftriver\Core\ObjectModel\Content
     */
    private function ParseTweetFromATOMItem($tweet, $channel)
    {
        //Extract all the relevant feedItem info
        $title = $tweet->get_title();
        //$description = $tweet->get_description();
        $contentLink = $tweet->get_permalink();
        $date = $tweet->get_date();
        
        //Create the source
        $source_name = $tweet->get_author()->get_name();
        $source = \Swiftriver\Core\ObjectModel\ObjectFactories\SourceFactory::CreateSourceFromIdentifier($source_name);
        $source->name = $source_name;
        $source->email = $tweet->get_author()->get_email();
        $source->link = $tweet->get_author()->get_link();
        $source->parent = $channel->id;
        $source->type = $channel->type;
        $source->subType = $channel->subType;
        
        //Create a new Content item
        $item = \Swiftriver\Core\ObjectModel\ObjectFactories\ContentFactory::CreateContent($source);

        //Fill the Content Item
        $item->text[] = new \Swiftriver\Core\ObjectModel\LanguageSpecificText(
                null, //here we set null as we dont know the language yet
                $title,
                array());
        $item->link = $contentLink;
        $item->date = strtotime($date);
        return $item;
    }
}
?>

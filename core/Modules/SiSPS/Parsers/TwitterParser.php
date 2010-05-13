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
     * Given a set of parameters, this method should
     * fetch content from a channel and parse each
     * content into the Swiftriver object model :
     * Content Item. The $lastsucess datetime is passed
     * to the function to ensure that content that has
     * already been parsed is not duplicated.
     *
     * @param \Swiftriver\Core\ObjectModel\Source $source
     * @return Swiftriver\Core\ObjectModel\Content[] contentItems
     */
    public function GetAndParse($source) {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetAndParse [Method invoked]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetAndParse [START: Switching processing based on subType]", \PEAR_LOG_DEBUG);

        $content = array();

        switch ($source->subType) {
            case "Search" : $content = $this->GetForTwitterSearch($source); break;
            case "Follow User" : $content = $this->GetForTwitterAccount($source); break;
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
     * @param \Swiftriver\Core\ObjectModel\Source $source 
     * @return \Swiftriver\Core\ObjectModel\Content[]
     */
    private function GetForTwitterSearch($source) {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [Method invoked]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [START: Extracting required parameters]", \PEAR_LOG_DEBUG);

        //Extract the required variables
        $SearchKeyword = $source->parameters["SearchKeyword"];
        if(!isset($SearchKeyword) || ($SearchKeyword == "")) {
            $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [the parapeter 'SearchKeyword' was not supplued. Returning null]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [Method finished]", \PEAR_LOG_DEBUG);
            return null;
        }

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [END: Extracting required parameters]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [START: calling twitter API to get feeds.]", \PEAR_LOG_DEBUG);

        $content = array();

        $page = 1;
        $have_results = TRUE; //just starting us off as true, although there may be no results
        while($have_results == TRUE && $page <= 5)
        {
            //This loop is for pagination of rss results
            $hashtag = trim(str_replace('#','',$SearchKeyword));
            $twitter_url = 'http://search.twitter.com/search.json?';
            $twitter_postfields = 'q=%23'.$hashtag.'&page='.$page;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_URL,$twitter_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $twitter_postfields);
            $buffer=curl_exec($ch);
            
            $innerContent = $this->ParseTweetsFromJSON($buffer, $source);
            foreach($innerContent as $item) {
                $content[] = $item;
            }

            $page++;
        }

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [END: Parsing feed items]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [Method finished]", \PEAR_LOG_DEBUG);

        //return the content array
        return $content;
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
                if($contentdate < $lastsucess) {
                    $textContentDate = date("c", $contentdate);
                    $textLastSucess = date("c", $source->lastSucess);
                    $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [Skipped feed item as date $textContentDate less than last sucessful run ($textLastSucess)]", \PEAR_LOG_DEBUG);
                    continue;
                }
            }

            $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [Adding feed item]", \PEAR_LOG_DEBUG);

            //Extract all the relevant feedItem info
            $title = $tweet->get_title();
            $description = $tweet->get_description();
            $contentLink = $tweet->get_permalink();
            $date = $tweet->get_date();

            //Create a new Content item
            $item = \Swiftriver\Core\ObjectModel\ObjectFactories\ContentFactory::CreateContent($source);

            //Fill the Content Item
            $item->text[] = new \Swiftriver\Core\ObjectModel\LanguageSpecificText(
                    null, //here we set null as we dont know the language yet
                    $title,
                    array($description));
            $item->link = $contentLink;
            $item->date = strtotime($date);

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
}
?>

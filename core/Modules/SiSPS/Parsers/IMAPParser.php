<?php
namespace Swiftriver\Core\Modules\SiSPS\Parsers;
class IMAPParser implements IParser {
    /**
     * Gets IMAP content
     *
     * @param string $imapHost
     * @param string $imapUser
     * @param string $imapPassword
     * @param \Swiftriver\Core\ObjectModel\Channel $channel
     *
     * @return $contentItems[]
     */
    private function GetIMAPContent($imapHost, $imapUser, $imapPassword, $channel) {
        $imapResource = imap_open("{".$imapHost."}INBOX", $imapUser, $imapPassword);
        //Open up unseen messages
        $imapEmails = imap_search($imapResource, strtoupper($channel->subType));

        $contentItems = array();

        if($imapEmails) {
            //Put newest emails on top
            rsort($imapEmails);

            foreach($imapEmails as $Email) {
                //Loop through each email and return the content
                $email_overview = imap_fetch_overview($imapResource, $Email, 0);
                $email_message = imap_fetchbody($imapResource, $Email, 2);

                $source_name = \reset($email_overview)->from;
                $source = \Swiftriver\Core\ObjectModel\ObjectFactories\SourceFactory::CreateSourceFromIdentifier($source_name);

                $source->name = $source_name;
                $source->parent = $channel->id;
                $source->type = $channel->type;
                $source->subType = $channel->subType;

                $item = \Swiftriver\Core\ObjectModel\ObjectFactories\ContentFactory::CreateContent($source);

                $item->text[] = new \Swiftriver\Core\ObjectModel\LanguageSpecificText(
                        null, //here we set null as we dont know the language yet
                        $email_overview[0]->subject, //email subject
                        array($email_message)); //the message

                $item->link = null;
                $item->date = $email_overview[0]->date;

                $contentItems[] = $item;
            }

            imap_close($imapResource);

            return $contentItems;
        }

        imap_close($imapResource);
        
        return null;
    }

    /**
     * Implementation of IParser::GetAndParse
     * @param \Swiftriver\Core\ObjectModel\Channel $channel
     * @param datetime $lassucess
     */
    public function GetAndParse($channel) {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::SiSPS::Parsers::IMAPParser::GetAndParse [Method invoked]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::SiSPS::Parsers::IMAPParser::GetAndParse [START: Extracting required parameters]", \PEAR_LOG_DEBUG);

        //Extract the IMAP parameters

        $imapHostName = $channel->parameters["IMAPHostName"];
        $imapUserName = $channel->parameters["IMAPUserName"];
        $imapPassword = $channel->parameters["IMAPPassword"];

        if(!isset($imapHostName) || ($imapHostName == "")) {
            $logger->log("Core::Modules::SiSPS::Parsers::IMAPParser::GetAndParse [the parameter 'IMAPHostName' was not supplied. Returning null]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Modules::SiSPS::Parsers::IMAPParser::GetAndParse [Method finished]", \PEAR_LOG_DEBUG);
            return null;
        }

        if(!isset($imapUserName) || ($imapUserName == "")) {
            $logger->log("Core::Modules::SiSPS::Parsers::IMAPParser::GetAndParse [the parameter 'IMAPUserName' was not supplied. Returning null]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Modules::SiSPS::Parsers::IMAPParser::GetAndParse [Method finished]", \PEAR_LOG_DEBUG);
            return null;
        }

        if(!isset($imapPassword) || ($imapPassword == "")) {
            $logger->log("Core::Modules::SiSPS::Parsers::IMAPParser::GetAndParse [the parameter 'IMAPPassword' was not supplied. Returning null]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Modules::SiSPS::Parsers::IMAPParser::GetAndParse [Method finished]", \PEAR_LOG_DEBUG);
            return null;
        }

        $logger->log("Core::Modules::SiSPS::Parsers::IMAPParser::GetAndParse [END: Extracting required parameters]", \PEAR_LOG_DEBUG);

        //Create the Content array
        $contentItems = array();

        $logger->log("Core::Modules::SiSPS::Parsers::IMAPParser::GetAndParse [START: Parsing IMAP items]", \PEAR_LOG_DEBUG);

        //Get information regarding the source

        $contentItems = $this->GetIMAPContent($imapHostName, $imapUserName, $imapPassword, $channel);

        $logger->log("Core::Modules::SiSPS::Parsers::IMAPParser::GetAndParse [END: Parsing IMAP items]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::SiSPS::Parsers::IMAPParser::GetAndParse [Method finished]", \PEAR_LOG_DEBUG);

        //return the content array
        return $contentItems;
    }

    /**
     * This method returns a string array with the names of all
     * the source types this parser is designed to parse.
     *
     * @return string[]
     */
    public function ListSubTypes() {
        return array(
            "Unseen",
            "Seen",
            "Recent",
            "Answered",
            "All",
        );
    }

    /**
     * This method returns a string describing the type of sources
     * it can parse. For example, the FeedsParser returns "Feeds".
     *
     * @return string type of sources parsed
     */
    public function ReturnType() {
        return "Email";
    }

    /**
     * This method returns an array of the required parameters that
     * are necessary to run this parser. The Array should be in the
     * following format:
     * array(
     *  "SubType" => array ( ConfigurationElements )
     * )
     *
     * @return array()
     */
    public function ReturnRequiredParameters(){
        $return = array();
        foreach($this->ListSubTypes() as $subType){
            $return[$subType] = array(
                new \Swiftriver\Core\ObjectModel\ConfigurationElement(
                    "IMAPHostName",
                    "string",
                    "Host URL (you may need to look this up, for example, GMail's URL is: imap.gmail.com:993/imap/ssl/novalidate-cert - not so intuitive, right)"
                ),
                new \Swiftriver\Core\ObjectModel\ConfigurationElement(
                    "IMAPUserName",
                    "string",
                    "IMAP login user name"
                ),
                new \Swiftriver\Core\ObjectModel\ConfigurationElement(
                    "IMAPPassword",
                    "string",
                    "IMAP login password"
                )
            );
        }
        return $return;
    }
}
?>
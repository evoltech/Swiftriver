<?php
namespace Swiftriver\Core\Modules\DataContext\MySql_V2;
/**
 * @author mg[at]swiftly[dot]org
 */
class DataContext implements
     \Swiftriver\Core\DAL\DataContextInterfaces\IAPIKeyDataContext,
     \Swiftriver\Core\DAL\DataContextInterfaces\IChannelDataContext,
     \Swiftriver\Core\DAL\DataContextInterfaces\IContentDataContext,
     \Swiftriver\Core\DAL\DataContextInterfaces\ISourceDataContext,
     \Swiftriver\Core\DAL\DataContextInterfaces\ITrustLogDataContext
{
    /**
     * Generic function used to gain a new PDO connection to
     * the database.
     *
     * @return \PDO
     */
    public static function PDOConnection()
    {
        $databaseUrl = (string) Setup::$Configuration->DataBaseUrl;

        $databaseName = (string) Setup::$Configuration->Database;

        $connectionString = "mysql:host=$databaseUrl;dbname=$databaseName";

        $username = (string) Setup::$Configuration->UserName;

        $password = (string) Setup::$Configuration->Password;

        $pdo = new \PDO($connectionString, $username, $password);

        return $pdo;
    }

    /**
     * Checks that the given API Key is registed for this
     * Core install
     * @param string $key
     * @return bool
     */
    public static function IsRegisterdCoreAPIKey($key)
    {

    }

    /**
     * Given a new APIKey, this method adds it to the
     * data store or registered API keys.
     * Returns true on sucess
     *
     * @param string $key
     * @return bool
     */
    public static function AddRegisteredCoreAPIKey($key)
    {

    }

    /**
     * Given an APIKey, this method will remove it from the
     * data store of registered API Keys
     * Returns true on sucess
     *
     * @param string key
     * @return bool
     */
    public static function RemoveRegisteredCoreAPIKey($key)
    {

    }

    /**
     * Given the IDs of Channels, this method
     * gets them from the underlying data store
     *
     * @param string[] $ids
     * @return \Swiftriver\Core\ObjectModel\Channel[]
     */
    public static function GetChannelsById($ids)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [Method Invoked]", \PEAR_LOG_DEBUG);

        $channels = array();

        if(!\is_array($ids) || count($ids) < 1)
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [No ids supplied]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [Method finished]", \PEAR_LOG_DEBUG);

            return $channels;
        }

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [START: Building queries]", \PEAR_LOG_DEBUG);

        $sql = "CALL SC_GetChannelByChannelIds ( :ids )";

        $idsArray = "(";

        foreach($ids as $id)
            $idsArray .= "'$id',";

        $idsArray = \rtrim($idsArray, ",") . ")";

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [END: Building queries]", \PEAR_LOG_DEBUG);

        try
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [START: Connecting via PDO]", \PEAR_LOG_DEBUG);

            $db = self::PDOConnection();

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [END: Connecting via PDO]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [START: Preparing PDO statement]", \PEAR_LOG_DEBUG);
            
            $statement = $db->prepare($sql);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [END: Preparing PDO statement]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [START: Executing PDO statement]", \PEAR_LOG_DEBUG);

            $result = $statement->execute(array(":ids" => $idsArray));

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [END: Executing PDO statement]", \PEAR_LOG_DEBUG);

            if(isset($result) && $result != null && $result !== 0)
            {
                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [START: Looping over results]", \PEAR_LOG_DEBUG);

                foreach($statement->fetchAll() as $row)
                {
                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [START: Constructing Channel Object from json]", \PEAR_LOG_DEBUG);

                    $json = $row['json'];

                    $channel = \Swiftriver\Core\ObjectModel\ObjectFactories\ChannelFactory::CreateChannelFromJSON($json);

                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [END: Constructing Channel Object from json]", \PEAR_LOG_DEBUG);

                    $channels[] = $channel;
                }

                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [END: Looping over results]", \PEAR_LOG_DEBUG);
            }

            $db = null;
        }
        catch (\PDOException $e)
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [An Exception was thrown:]", \PEAR_LOG_ERR);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [$e]", \PEAR_LOG_ERR);
        }

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [Method Finished]", \PEAR_LOG_DEBUG);

        return $channels;
    }

    /**
     * Adds a list of new Channels to the data store
     *
     * @param \Swiftriver\Core\ObjectModel\Channel[] $Channels
     */
    public static function SaveChannels($channels)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [Method Invoked]", \PEAR_LOG_DEBUG);

        if(!\is_array($channels) || count($channels) < 1)
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [No channels supplied]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [Method finished]", \PEAR_LOG_DEBUG);

            return;
        }

        $sql = "CALL SC_SaveChannel ( :id, :type, :subType, :active, :inProcess, :nextRun, :json)";

        try
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [START: Connecting to db via PDO]", \PEAR_LOG_DEBUG);

            $db = self::PDOConnection();

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [END: Connecting to db via PDO]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [START: Preparing PDO statment]", \PEAR_LOG_DEBUG);

            $statement = $db->prepare($sql);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [END: Preparing PDO statment]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [START: Looping through channels]", \PEAR_LOG_DEBUG);

            foreach($channels as $channel)
            {
                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [START: Executing PDO statement for channel]", \PEAR_LOG_DEBUG);

                $parameters = array (
                    "id" => $channel->id,
                    "type" => $channel->type,
                    "subType" => $channel->subType,
                    "active" => $channel->active,
                    "inProcess" => $channel->inprocess,
                    "nextRun" => $channel->nextrun,
                    "json" => \json_encode($channel));

                $statement->execute($parameters);

                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [END: Executing PDO statement for channel]", \PEAR_LOG_DEBUG);
            }

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [END: Looping through channels]", \PEAR_LOG_DEBUG);
        }
        catch(\PDOException $e)
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [An exception was thrown]", \PEAR_LOG_ERR);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [$e]", \PEAR_LOG_ERR);
        }

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [Method Finished]", \PEAR_LOG_DEBUG);
    }

    /**
     * Given a list of IDs this method removes the Channels from
     * the data store.
     *
     * @param string[] $ids
     */
    public static function RemoveChannels($ids)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [Method Invoked]", \PEAR_LOG_DEBUG);

        if(!\is_array($ids) || count($ids) < 1)
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [No ids supplied]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [Method finished]", \PEAR_LOG_DEBUG);

            return;
        }

        $sql = "CALL SC_DeleteChannel ( :id )";

        try
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [START: Connecting to db via PDO]", \PEAR_LOG_DEBUG);

            $db = self::PDOConnection();

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [END: Connecting to db via PDO]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [START: Preparing PDO statment]", \PEAR_LOG_DEBUG);

            $statement = $db->prepare($sql);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [END: Preparing PDO statment]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [START: Looping through ids]", \PEAR_LOG_DEBUG);

            foreach($ids as $id)
            {
                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [START: Executing PDO statement for channel]", \PEAR_LOG_DEBUG);

                $statement->execute(array("id" => $id));

                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [END: Executing PDO statement for channel]", \PEAR_LOG_DEBUG);
            }

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [END: Looping through ids]", \PEAR_LOG_DEBUG);
        }
        catch(\PDOException $e)
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [An exception was thrown]", \PEAR_LOG_ERR);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [$e]", \PEAR_LOG_ERR);
        }

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [Method Finished]", \PEAR_LOG_DEBUG);
    }

    /**
     * Given a date time, this function returns the next due
     * Channel.
     *
     * @param DateTime $time
     * @return \Swiftriver\Core\ObjectModel\Channel
     */
    public static function SelectNextDueChannel($time)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [Method Invoked]", \PEAR_LOG_DEBUG);

        $channel = null;

        if(!isset($time) || $time == null)
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [No time supplied, setting time to now]", \PEAR_LOG_DEBUG);

            $time = time();
        }

        $sql = "CALL SC_SelectNextDueChannel ( :time )";

        try
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [START: Connecting to db via PDO]", \PEAR_LOG_DEBUG);

            $db = self::PDOConnection();

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [END: Connecting to db via PDO]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [START: Preparing PDO statment]", \PEAR_LOG_DEBUG);

            $statement = $db->prepare($sql);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [END: Preparing PDO statment]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [START: Executing PDO statment]", \PEAR_LOG_DEBUG);

            $result = $statement->execute(array("time" => $time));

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [END: Executing PDO statment]", \PEAR_LOG_DEBUG);

            if(isset($result) && $result != null && $result !== 0)
            {
                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [START: Looping over results]", \PEAR_LOG_DEBUG);

                foreach($statement->fetchAll() as $row)
                {
                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [START: Constructing Channel Object from json]", \PEAR_LOG_DEBUG);

                    $json = $row['json'];

                    $channel = \Swiftriver\Core\ObjectModel\ObjectFactories\ChannelFactory::CreateChannelFromJSON($json);

                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [END: Constructing Channel Object from json]", \PEAR_LOG_DEBUG);
                }

                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [END: Looping over results]", \PEAR_LOG_DEBUG);
            }
        }
        catch(\PDOException $e)
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [An exception was thrown]", \PEAR_LOG_ERR);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [$e]", \PEAR_LOG_ERR);
        }

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [Method Finished]", \PEAR_LOG_DEBUG);

        return $channel;
    }

    /**
     * Lists all the current Channel in the core
     * @return \Swiftriver\Core\ObjectModel\Channel[]
     */
    public static function ListAllChannels()
    {

    }

    /**
     * Given a set of content items, this method will persist
     * them to the data store, if they already exists then this
     * method should update the values in the data store.
     *
     * @param \Swiftriver\Core\ObjectModel\Content[] $content
     */
    public static function SaveContent($content)
    {

    }

    /**
     * Given an array of content is's, this function will
     * fetch the content objects from the data store.
     *
     * @param string[] $ids
     * @return \Swiftriver\Core\ObjectModel\Content[]
     */
    public static function GetContent($ids, $orderby = null)
    {

    }

    /**
     *
     * @param string[] $parameters
     */
    public static function GetContentList($parameters)
    {

    }

    /**
     * Given an array of content items, this method removes them
     * from the data store.
     * @param \Swiftriver\Core\ObjectModel\Content[] $content
     */
    public static function DeleteContent($content)
    {

    }

    /**
     * Given the IDs of Sources, this method
     * gets them from the underlying data store
     *
     * @param string[] $ids
     * @return \Swiftriver\Core\ObjectModel\Source[]
     */
    public static function GetSourcesById($ids)
    {

    }

    /**
     * Lists all the current Source in the core
     * @return \Swiftriver\Core\ObjectModel\Source[]
     */
    public static function ListAllSources()
    {

    }

    /**
     * This method redords the fact that a marker (sweeper) has changed the score
     * of a source by marking a content items as either 'acurate', 'chatter' or
     * 'inacurate'
     *
     * @param string $sourceId
     * @param string $markerId
     * @param string|null $reason
     * @param int $change
     */
    public static function RecordSourceScoreChange($sourceId, $markerId, $change, $reason = null)
    {
        
    }
}
?>

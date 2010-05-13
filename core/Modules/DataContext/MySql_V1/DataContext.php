<?php
namespace Swiftriver\Core\Modules\DataContext\MySql_V1;
class DataContext implements \Swiftriver\Core\DAL\DataContextInterfaces\IDataContext {
    /**
     * Checks that the given API Key is registed for this
     * Core install
     *
     * Inhereted from IAPIKeyDataContext
     *
     * @param string $key
     * @return bool
     */
    public static function IsRegisterdCoreAPIKey($key) {
        if(!isset($key) || $key == "")
            return null;
        $query = "SELECT COUNT(*) FROM coreapikeys WHERE apikey = '".$key."';";
        $result = self::RunQuery($query);
        if(!isset($result) || $result == false)
            return null;
        $count = mysql_result($result, 0);
        return $count > 0;
    }

    /**
     * Given a new APIKey, this method adds it to the
     * data store or registered API keys.
     * @param string $key
     * @return bool
     */
    public static function AddRegisteredCoreAPIKey($key) {
        if(!isset($key) || $key == "")
            return false;
        if(self::IsRegisterdCoreAPIKey($key))
            return true;
        $query = "INSERT INTO coreapikeys VALUES('".$key."');";
        $result = self::RunQuery($query);
        return $result;
    }

    /**
     * Given an APIKey, this method will remove it from the
     * data store of registered API Keys
     * Returns true on sucess
     *
     * @param string key
     * @return bool
     */
    public static function RemoveRegisteredCoreAPIKey($key) {
        if(!isset($key) || $key == "")
            return true;
        if(!self::IsRegisterdCoreAPIKey($key))
            return true;
        $query = "DELETE FROM coreapikeys WHERE apikey = '".$key."';";
        $result = self::RunQuery($query);
        return $result;
    }

    /**
     * Given the IDs of Sources, this method
     * gets them from the underlying data store
     *
     * @param string[] $ids
     * @return \Swiftriver\Core\ObjectModel\Source[]
     */
    public static function GetSourcesById($ids){
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetSourcesById [Method invoked]", \PEAR_LOG_DEBUG);

        if(!isset($ids) || $ids == null || !is_array($ids) || count($ids) < 1) {
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetSourcesById [No IDs supplied]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetSourcesById [Method finished]", \PEAR_LOG_DEBUG);
            return array();
        }

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetSourcesById [START: Getting the sources from the data store]", \PEAR_LOG_DEBUG);

        $rb = RedBeanController::RedBean();

        $idsQuery = "(";
        foreach($ids as $id)
            $idsQuery .= "'$id',";
        $idsQuery = rtrim($idsQuery, ",").")";

        $dbResults = RedBeanController::Finder()->where("source", "textId in :id", array(":id" => $idsQuery));

        if(!$dbResults || $dbResults == null || !is_array($dbResults) || count($dbResults) < 1) {
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetSourcesById [No sourcse found matching any of the IDs supplied]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetSourcesById [Method finished]", \PEAR_LOG_DEBUG);
            return array();
        }

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetSourcesById [END: Getting the sources from the data store]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetSourcesById [START: Parsing the data store objects]", \PEAR_LOG_DEBUG);

        $sources = array();

        foreach($dbResults as $dbResult) {

            $json = $dbResult->json;

            try {
                $sources[] = \Swiftriver\Core\ObjectModel\ObjectFactories\SourceFactory::CreateSourceFromJSON($json);
            }
            catch (\Exception $e) {
                $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetSourcesById [An exception was thrown: $e]", \PEAR_LOG_ERR);
                $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetSourcesById [No source could be constructed from the DB content]", \PEAR_LOG_DEBUG);
                continue;
            }
        }

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetSourcesById [END: Parsing the data store objects]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetSourcesById [Method finished]", \PEAR_LOG_DEBUG);

        return $sources;
    }

    /**
     * Adds a list of new Sources to the data store
     *
     * @param \Swiftriver\Core\ObjectModel\Source[] $sources
     */
    public static function SaveSources($sources){
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SaveSources [Method invoked]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SaveSources [START: Looping throught sources]", \PEAR_LOG_DEBUG);

        foreach($sources as $source) {

            //get the source from the db
            $s = reset(RedBeanController::Finder()->where("source", "textId = :id", array(":id" => $source->id)));

            //If the source does not exists, create it.
            if(!$s || $s == null) {
                //create the new data store object for the source
                $s = $rb->dispense("source");
            }

            //Add properties we want specifically in the table
            $s = DataContext::AddPropertiesToDataSoreItem(
                    $s,
                    $source,
                    array(
                        "textId" => "id",
                        "score" => "score",
                        "type" => "type",
                        "subType" => "subType",
                        "active" => "active",
                        "inprocess" => "inprocess",
                        "nextrun" => "nextrun"
                    ));

            //add the encoded source object to the data sotre object
            $s->json = json_encode($source);

            //save the source
            $rb->store($s);
        }

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SaveSources [START: Looping throught sources]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SaveSources [Method finished]", \PEAR_LOG_DEBUG);
    }

    /**
     * Given a list of IDs this method removes the sources from
     * the data store.
     *
     * @param string[] $ids
     */
    public static function RemoveSources($ids){
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::RemoveSources [Method invoked]", \PEAR_LOG_DEBUG);

        if(!isset($ids) || $ids == null || !is_array($ids) || count($ids) < 1) {
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::RemoveSources [No IDs supplied]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::RemoveSources [Method finished]", \PEAR_LOG_DEBUG);
            return array();
        }

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::RemoveSources [START: Looping through IDs ]", \PEAR_LOG_DEBUG);

        $rb = RedBeanController::RedBean();

        foreach($ids as $id) {
            //get the source from the db
            $s = reset(RedBeanController::Finder()->where("source", "textId = :id", array(":id" => $source->id)));

            //If the source does not exists, skip it.
            if(!$s || $s == null) {
                continue;
            }

            $rb->trash($s);
        }

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::RemoveSources [END: Looping through IDs ]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::RemoveSources [Method finished]", \PEAR_LOG_DEBUG);
    }

    /**
     * Given a date time, this function returns the next due
     * Source.
     *
     * @param DateTime $time
     * @return \Swiftriver\Core\ObjectModel\Source
     */
    public static function SelectNextDueSource($time){
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SelectNextDueSource [Method invoked]", \PEAR_LOG_DEBUG);

        //if the time is not set, set it to now
        if(!isset($time) || $time == null || !is_numeric($time)) {
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SelectNextDueSource [The time variable was not set, setting to now]", \PEAR_LOG_DEBUG);
            $time = time();
        }

        //Get the redbean
        $rb = RedBeanController::RedBean();

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SelectNextDueSource [START: Selecting data object from the data store]", \PEAR_LOG_DEBUG);

        //select the next due processing job
        $s = reset(RedBeanController::Finder()->where(
                "source",
                "active = 1 and inprocess = 0 order by nextrun asc limit 1"
                ));

        //check if there is anythign to return
        if(!isset($s) || $s == null) {
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SelectNextDueSource [Nothing returned from the data store]", \PEAR_LOG_DEBUG);
            return null;
        }

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SelectNextDueSource [END: Selecting data object from the data store]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SelectNextDueSource [START: Constructing a source object fro mthe data object]", \PEAR_LOG_DEBUG);

        //Get the json from that data object
        $json = $s->json;

        try {
            //Build a new source from the json
            $source = \Swiftriver\Core\ObjectModel\ObjectFactories\SourceFactory::CreateSourceFromJSON($json);
        }
        catch(\Exception $e) {
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SelectNextDueSource [An exception was thrown: $e]", \PEAR_LOG_ERR);
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SelectNextDueSource [The data object could not be converted into a source object]", \PEAR_LOG_DEBUG);
            return null;
        }

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SelectNextDueSource [END: Constructing a source object fro mthe data object]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SelectNextDueSource [START: Setting properties to identify the source as in progress]", \PEAR_LOG_DEBUG);

        //set the nextrun
        $nextrun = time() + ($source->updatePeriod * 60);

        //update the proerties that show the channel has been run
        $source->nextrun = $nextrun;
        $source->timesrun = $source->timesrun + 1;
        $source->lastrun = time();
        $source->inprocess = true;

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SelectNextDueSource [END: Setting properties to identify the source as in progress]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SelectNextDueSource [START: Saving the source state]", \PEAR_LOG_DEBUG);

        DataContext::SaveSources(array($source));

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SelectNextDueSource [END: Saving the source state]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SelectNextDueSource [Method finished]", \PEAR_LOG_DEBUG);

        //return the source
        return $source;
    }

    /**
     * Lists all the current Source in the core
     * @return \Swiftriver\Core\ObjectModel\Source[]
     */
    public static function ListAllSources(){
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllSources [Method invoked]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllSources [START: Querying the data store]", \PEAR_LOG_DEBUG);

        $dbResults = RedBeanController::Finder()->where("source");

        if(!$dbResults || $dbResults == null || !is_array($dbResults) || count($dbResults) < 1) {
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllSources [No sourcse found]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllSources [Method finished]", \PEAR_LOG_DEBUG);
            return array();
        }

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllSources [END: Querying the data store]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllSources [START: Creating source objects]", \PEAR_LOG_DEBUG);

        $sources = array();

        foreach($dbResults as $dbResult) {

            $json = $dbResult->json;

            try {
                $sources[] = \Swiftriver\Core\ObjectModel\ObjectFactories\SourceFactory::CreateSourceFromJSON($json);
            }
            catch (\Exception $e) {
                $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllSources [An exception was thrown: $e]", \PEAR_LOG_ERR);
                $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllSources [No source could be constructed from the DB content]", \PEAR_LOG_DEBUG);
                continue;
            }
        }

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllSources [END: Creating source objects]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllSources [Method finished]", \PEAR_LOG_DEBUG);

        return $sources;
    }

    /**
     * Given a set of content items, this method will persist
     * them to the data store, if they already exists then this
     * method should update the values in the data store.
     *
     * @param \Swiftriver\Core\ObjectModel\Content[] $content
     */
    public static function SaveContent($content){
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SaveContent [Method invoked]", \PEAR_LOG_DEBUG);

        //initiate the redbean dal contoller
        $rb = RedBeanController::RedBean();

        //loop throught each item of content
        foreach($content as $item) {
            $i = reset(RedBeanController::Finder()->where("content", "textId = :id", array(":id" => $item->id)));

            if(!$i || $i == null) {
                //Create a new data store object
                $i = $rb->dispense("content");
            }

            //Add any properties we wish to be un encoded to the data store object
            $i = DataContext::AddPropertiesToDataSoreItem(
                    $i,
                    $item,
                    array(
                        "textId" => "id",
                        "state" => "state",
                        "date" => "date",
                    ));

            //Add the encoded content item to the data store object
            $i->json = json_encode($item);

            //Save the data store object
            $rb->store($i);

            //get the source from the content
            $source = $item->source;

            DataContext::SaveSources(array($source));

            //get the source from the db
            $s = reset(RedBeanController::Finder()->where("source", "textId = :id", array(":id" => $source->id)));

            //create the association between content and source
            RedBeanController::Associate($i, $s);
        }
    }

    /**
     * Given an array of content is's, this function will
     * fetch the content objects from the data store.
     *
     * @param string[] $ids
     * @return \Swiftriver\Core\ObjectModel\Content[]
     */
    public static function GetContent($ids, $orderby = null) {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetContent [Method invoked]", \PEAR_LOG_DEBUG);

        //if no $orderby is sent
        if(!$orderby || $orderby == null) {
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetContent [No Order By clause set, setting to 'date desc']", \PEAR_LOG_DEBUG);
            //Set it to the default - date DESC
            $orderby = "date desc";
        }

        //set up the return array
        $content = array();

        //If the $ids array is blank or empty, return the empty array
        if(!isset($ids) || !is_array($ids) || count($ids) < 1) {
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetContent [No IDs sent to method]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetContent [Method finsiehd]", \PEAR_LOG_DEBUG);
            return $content;
        }

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetContent [START: Building the RedBean Query]", \PEAR_LOG_DEBUG);

        //set up the array to hold the ids
        $queryIds = array();

        //start to build the sql
        $query = "textId in (";

        /*//for each content item, add to the query and the ids array
        for($i=0; $i<count($ids); $i++) {
            $query .= ":id$i,";
            $queryIds[":id$i"] = $ids[$i];
        }*/

        $counter = 0;
        foreach($ids as $id) {
            $query .= ":id$counter,";
            $queryIds[":id$counter"] = $id;
            $counter++;
        }

        //tidy up the query
        $query = rtrim($query, ",").") order by ".$orderby;

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetContent [END: Building the RedBean Query]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetContent [START: Running RedBean Query]", \PEAR_LOG_DEBUG);

        //Get the finder
        $finder = RedBeanController::Finder();

        //Find the content
        $dbContent = $finder->where("content", $query, $queryIds);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetContent [FINISHED: Running RedBean Query]", \PEAR_LOG_DEBUG);

        //set up the return array
        $content = array();

        //set up the red bean
        $rb = RedBeanController::RedBean();

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetContent [START: Constructing Content and Source items]", \PEAR_LOG_DEBUG);

        //loop through the db content
        foreach($dbContent as $key => $dbItem) {
            //get the associated source
            $s = reset($rb->batch("source", RedBeanController::GetRelatedBeans($dbItem, "source")));

            //Create the source from the db json
            $source = \Swiftriver\Core\ObjectModel\ObjectFactories\SourceFactory::CreateSourceFromJSON($s->json);

            //get the json for the content
            $json = $dbItem->json;

            //create the content
            $item = \Swiftriver\Core\ObjectModel\ObjectFactories\ContentFactory::CreateContent($source, $json);

            //add it to the array
            $content[] = $item;
        }

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetContent [END: Constructing Content and Source items]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetContent [Method finished]", \PEAR_LOG_DEBUG);

        //return the content
        return $content;
    }

    /**
     * Given an array of content items, this method removes them
     * from the data store.
     * @param \Swiftriver\Core\ObjectModel\Content[] $content
     */
    public static function DeleteContent($content) {
        //Get the database name


        //initiate the redbean dal contoller
        $rb = RedBeanController::RedBean();

        //set up the array to hold the ids
        $ids = array();
        
        //start to build the sql
        $query = "delete from content where textId in (";
        
        //for each content item, add to the query and the ids array
        for($i=0; $i<count($content); $i++) {
            $query .= ":id$i,";
            $ids[":id$i"] = $content[$i]->id;
        }
        
        //tidy up the query
        $query = rtrim($query, ",").")";
        
        //Get the RB database adapter
        $db = RedBeanController::DataBaseAdapter();
        
        //execute the sql
        $db->exec($query, $ids);
        
        /*
        //loop throught each item of content
        foreach($content as $item) {
            $potentials = RedBeanController::Finder()->where("content", "textid = :id limit 1", array(":id" => $item->id));
            if(!isset($potentials) || !is_array($potentials) || count($potentials) == 0) {
                continue;
            }

            //get the content
            $i = reset($potentials);

            //First remove all existing text
            $textToRemove = $rb->batch("content_text", RedBeanController::GetRelatedBeans($i, "content_text"));
            if(isset($textToRemove) && is_array($textToRemove) && count($textToRemove) > 0) {
                foreach($textToRemove as $ttr) {
                    $rb->trash($ttr);
                }
            }

            //first remove the existing tags
            $tagsToRemove = $rb->batch("content_tags", RedBeanController::GetRelatedBeans($i, "content_tags"));
            if(isset($tagsToRemove) && is_array($tagsToRemove) && count($tagsToRemove) > 0) {
                foreach($tagsToRemove as $ttr) {
                    $rb->trash($ttr);
                }
            }

            //remove all existing difcollection and their difs
            $difCollectionsToRemove = $rb->batch("dif_collections", RedBeanController::GetRelatedBeans($i, "dif_collections"));
            if(isset($difCollectionsToRemove) && is_array($difCollectionsToRemove) && count($difCollectionsToRemove) > 0) {
                foreach($difCollectionsToRemove as $dctr) {
                    $difstoremove = $rb->batch("difs", RedBeanController::GetRelatedBeans($dctr, "difs"));
                    if(isset($difstoremove) && is_array($difstoremove) && count($difstoremove) > 0) {
                        foreach($difstoremove as $diftoremove) {
                            $rb->trash($diftoremove);
                        }
                    }
                    $rb->trash($dctr);
                }
            }

            //Remove the content
            $rb->trash($i);
         
        }
        */
    }

    /**
     * Given a state, pagesize, page start index and possibly
     * an order by calse, this method will return a page of content.
     *
     * @param int $state
     * @param int $pagesize
     * @param int $pagestart
     * @param string $orderby
     * @return array("totalCount" => int, "contentItems" => Content[])
     */
    public static function GetPagedContentByState($state, $pagesize, $pagestart, $orderby = null) {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetPagedContentByState [Method invoked]", \PEAR_LOG_DEBUG);

        //if no $orderby is sent
        if(!$orderby || $orderby == null) {
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetPagedContentByState [No Order By clause set, setting to 'date desc']", \PEAR_LOG_DEBUG);
            //Set it to the default - date DESC
            $orderby = "date desc";
        }

        //initilise the red bean controller
        $rb = RedBeanController::RedBean();

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetPagedContentByState [START: Get total record count for state: $state]", \PEAR_LOG_DEBUG);

        try {
            //get the total count to return
            $totalCount = RedBeanController::DataBaseAdapter()->getCell(
                    "select count(id) from content where state = :state",
                    array(":state" => $state));
        }
        catch (\Exception $e) {
            //no content defined yet
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetPagedContentByState [No content to return]", \PEAR_LOG_DEBUG);
            return array();
        }
        //set the return as an int
        $totalCount = (int) $totalCount;

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetPagedContentByState [Total record count = $totalCount]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetPagedContentByState [END: Get total record count for state: $state]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetPagedContentByState [START: Get the id's of the content that should be returned]", \PEAR_LOG_DEBUG);

        //set the SQL
        $sql = "select textId from content where state = '$state' order by $orderby limit $pagestart , $pagesize";

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetPagedContentByState [Getting ID's with query: $sql]", \PEAR_LOG_DEBUG);

        //Get the page of IDs
        $ids = RedBeanController::DataBaseAdapter()->getCol($sql);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetPagedContentByState [END: Get the id's of the content that should be returned]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetPagedContentByState [START: Getting the content for the ids]", \PEAR_LOG_DEBUG);

        //Get the content items
        $content = self::GetContent($ids, $orderby);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetPagedContentByState [END: Getting the content for the ids]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetPagedContentByState [Method finished]", \PEAR_LOG_DEBUG);

        return array ("totalCount" => $totalCount, "contentItems" => $content);
    }

    /**
     * Given the correct parameters, this method will reatun a page of content
     * in the correct state for whome the source of that content has a veracity
     * score in between the $minVeracity and $maxVeracity supplied.
     *
     * @param int $state
     * @param int $pagesize
     * @param int $pagestart
     * @param int $minVeracity 0 - 100
     * @param int $maxVeracity 0 - 100
     * @param string $orderby
     * @return array("totalCount" => int, "contentItems" => Content[])
     */
    public static function GetPagedContentByStateAndSourceVeracity($state, $pagesize, $pagestart, $minVeracity, $maxVeracity, $orderby = null) {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetPagedContentByStateAndSourceVeracity [Method invoked]", \PEAR_LOG_DEBUG);

        //if no $orderby is sent
        if(!$orderby || $orderby == null) {
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetPagedContentByStateAndSourceVeracity [No Order By clause set, setting to 'date desc']", \PEAR_LOG_DEBUG);
            //Set it to the default - date DESC
            $orderby = "date desc";
        }

        //initilise the red bean controller
        $rb = RedBeanController::RedBean();

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetPagedContentByStateAndSourceVeracity [START: Get total record count for state: $state]", \PEAR_LOG_DEBUG);

        try {
            //get the total count to return
            $sql =
                    "select count(content.id) from content left join content_source ".
                    "on content.id = content_source.content_id left join source ".
                    "on content_source.source_id = source.id where state = :state ".
                    "and ((source.score > :min and source.score < :max) or source.score ".
                    ($minVeracity == 0 ? "is" : "is not")." null)";
            $totalCount = RedBeanController::DataBaseAdapter()->getCell(
                    $sql,
                    array(
                        ":state" => $state,
                        ":min" => $minVeracity,
                        ":max" => $maxVeracity,
                    ));
        }
        catch (\Exception $e) {
            //no content defined yet
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetPagedContentByStateAndSourceVeracity [No content to return]", \PEAR_LOG_DEBUG);
            return array();
        }
        //set the return as an int
        $totalCount = (int) $totalCount;

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetPagedContentByStateAndSourceVeracity [Total record count = $totalCount]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetPagedContentByStateAndSourceVeracity [END: Get total record count for state: $state]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetPagedContentByStateAndSourceVeracity [START: Get the id's of the content that should be returned]", \PEAR_LOG_DEBUG);

        //set the SQL
        $isNullCondition = $minVeracity == 0 ? "is" : "is not";
        $sql = 
            "select content.textId from content left join content_source ".
            "on content.id = content_source.content_id left join source ".
            "on content_source.source_id = source.id where state = '$state' ".
            "and ((source.score >= $minVeracity and source.score <= $maxVeracity) ".
            "or source.score $isNullCondition null)";

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetPagedContentByStateAndSourceVeracity [Getting ID's with query: $sql]", \PEAR_LOG_DEBUG);

        //Get the page of IDs
        $ids = RedBeanController::DataBaseAdapter()->getCol($sql);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetPagedContentByStateAndSourceVeracity [END: Get the id's of the content that should be returned]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetPagedContentByStateAndSourceVeracity [START: Getting the content for the ids]", \PEAR_LOG_DEBUG);

        //Get the content items
        $content = self::GetContent($ids, $orderby);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetPagedContentByStateAndSourceVeracity [END: Getting the content for the ids]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetPagedContentByStateAndSourceVeracity [Method finished]", \PEAR_LOG_DEBUG);

        return array ("totalCount" => $totalCount, "contentItems" => $content);
    }

    /**
     * This method redords the fact that a marker (sweeper) has changed the score
     * of a source by marking a content items as either 'acurate', 'chatter' or
     * 'inacurate'
     *
     * @param string $sourceId
     * @param string $markerId
     * @param int $change
     */
    public static function RecordSourceScoreChange($sourceId, $markerId, $change, $reason = null) {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::RecordSourceScoreChange [Method invoked]", \PEAR_LOG_DEBUG);

        //get the red bean
        $rb = RedBeanController::RedBean();

        //create a new entry
        $entry = $rb->dispense("trustlog_sourcescorechange");

        //add the properties
        $entry->sourceId = $sourceId;
        $entry->markerId = $markerId;
        $entry->change = $change;
        if($reason != null) {
            $entry->reason = $reason;
        }

        //save the entry
        $rb->store($entry);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::RecordSourceScoreChange [Method finished]", \PEAR_LOG_DEBUG);
    }

    public static function RunQuery($query) {
        //TODO: Logging
        $url = (string)Setup::$Configuration->DataBaseUrl;
        $username = (string)Setup::$Configuration->UserName;
        $password = (string)Setup::$Configuration->Password;

        //Open a connection to the DB server
        $mysql = mysql_connect($url, $username, $password);

        //Select the databse
        $database = (string)Setup::$Configuration->Database;

        $return = mysql_select_db($database, $mysql);
        $error = mysql_error($mysql);

        $return = mysql_query($query, $mysql);
        $error = mysql_error($mysql);

        mysql_close($mysql);

        return $return;
    }

    private static function AddPropertiesToDataSoreItem($dataStoreItem, $sourceItem, $propertiesArray) {
        foreach($propertiesArray as $key => $value) {
            $dataStoreItem->$key = $sourceItem->$value;
        }
        return $dataStoreItem;
    }
}
?>
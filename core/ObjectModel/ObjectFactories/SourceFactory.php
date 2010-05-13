<?php
namespace Swiftriver\Core\ObjectModel\ObjectFactories;
class SourceFactory {
    public static function CreateSourceFromJSON($json) {
        //decode the json
        $object = json_decode($json);

        //If there is an error in the JSON
        if(!$object || $object == null) {
            //throw an exception
            throw new \Exception("There was an error in the JSON passed in to the SourceFactory.");
        }

        //create a new source
        $source = new \Swiftriver\Core\ObjectModel\Source();

        //set the basic properties
        $source->id =               isset($object->id) ? $object->id : md5(uniqid(rand(), true));
        $source->score =            isset($object->score) ? $object->score : null;
        $source->name =             isset($object->name) ? $object->name : null;
        $source->type =             isset($object->type) ? $object->type : null;
        $source->subType =          isset($object->subType) ? $object->subType : null;
        $source->updatePeriod =     isset($data->updatePeriod) ? $data->updatePeriod : 30;
        $source->nextrun =          isset($data->nextrun) ? $data->nextrun : strtotime("+ ".$source->updatePeriod." minutes");
        $source->active =           isset($data->active) ? $data->active : true;
        $source->lastSucess =       isset($data->lastSucess) ? $data->lastSucess : null;
        $source->inprocess =        isset($data->inprocess) ? $data->inprocess : false;
        $source->timesrun =         isset($data->timesrun) ? $data->timesrun : 0;

        if(isset($data->parameters)) {
            $params = array();
            foreach($data->parameters as $key => $value) {
                $params[$key] = $value;
            }
            $source->parameters = $params;
        }
        else {
            $source->parameters = array();
        }

        //return the source
        return $source;
    }
}
?>

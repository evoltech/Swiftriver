<?php
namespace Swiftriver\Core\ObjectModel\ObjectFactories;
class ChannelFactory {
    public static function CreateChannelFromIdentifier($identifier) {
        $channel = new \Swiftriver\Core\ObjectModel\Channel();
        $channel->id = md5($identifier, true);
        return $channel;
    }

    public static function CreateChannelFromJSON($json) {
        //decode the json
        $object = json_decode($json);

        //If there is an error in the JSON
        if(!$object || $object == null) {
            //throw an exception
            throw new \Exception("There was an error in the JSON passed in to the ChannelFactory.");
        }

        //create a new Channel
        $channel = new \Swiftriver\Core\ObjectModel\Channel();

        //set the basic properties
        $channel->id =               isset($object->id) ? $object->id : md5(uniqid(rand(), true));
        $channel->name =             isset($object->name) ? $object->name : null;
        $channel->type =             isset($object->type) ? $object->type : null;
        $channel->subType =          isset($object->subType) ? $object->subType : null;
        $channel->updatePeriod =     isset($object->updatePeriod) ? $object->updatePeriod : 30;
        $channel->nextrun =          isset($object->nextrun) ? $object->nextrun : strtotime("+ ".$channel->updatePeriod." minutes");
        $channel->active =           isset($object->active) ? $object->active : true;
        $channel->lastSucess =       isset($object->lastSucess) ? $object->lastSucess : null;
        $channel->inprocess =        isset($object->inprocess) ? $object->inprocess : false;
        $channel->timesrun =         isset($object->timesrun) ? $object->timesrun : 0;
        $channel->deleted =          isset($object->deleted) ? $object->deleted : false;

        if(isset($object->parameters)) {
            $params = array();
            foreach($object->parameters as $key => $value) {
                $params[$key] = $value;
            }
            $channel->parameters = $params;
        }
        else {
            $channel->parameters = array();
        }

        //return the Channel
        return $channel;
    }
}
?>

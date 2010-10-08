<?php
namespace Swiftriver\Core\ObjectModel\ObjectFactories;
/**
 * Factory object used to build source objects
 * @author mg[at]swiftly[dot]org
 */
class SourceFactory
{
    /**
     * Creats a new Source object from a unique id
     *
     * @param string $identifier
     * @return \Swiftriver\Core\ObjectModel\Source
     */
    public static function CreateSourceFromIdentifier($identifier)
    {
        $source = new \Swiftriver\Core\ObjectModel\Source();
        $source->id = md5($identifier);
        return $source;
    }

    /**
     * Returns a new Source object from the JSON encoded string
     * of a Source obejct
     * 
     * @param JSON $json
     * @return \Swiftriver\Core\ObjectModel\Source 
     */
    public static function CreateSourceFromJSON($json)
    {
        //decode the json
        $object = json_decode($json);

        //If there is an error in the JSON
        if(!$object || $object == null)
            throw new \Exception("There was an error in the JSON passed in to the SourceFactory.");

        //create a new source
        $source = new \Swiftriver\Core\ObjectModel\Source();

        //set the basic properties
        $source->id =               isset($object->id) ? $object->id : md5(uniqid(rand(), true));
        $source->score =            isset($object->score) ? $object->score : null;
        $source->name =             isset($object->name) ? $object->name : null;
        $source->type =             isset($object->type) ? $object->type : null;
        $source->subType =          isset($object->subType) ? $object->subType : null;
        $source->email =            isset($object->email) ? $object->email : null;
        $source->link =             isset($object->link) ? $object->link : null;
        $source->parent =           isset($object->parent) ? $object->parent : null;

        //return the source
        return $source;
    }
}
?>

<?php
namespace Swiftriver\Core\DAL\DataContextInterfaces;
interface ISourceDataContext {
    /**
     * Given the IDs of Sources, this method
     * gets them from the underlying data store
     *
     * @param string[] $ids
     * @return \Swiftriver\Core\ObjectModel\Source[]
     */
    public static function GetSourcesById($ids);

    /**
     * Adds a list of new Sources to the data store
     *
     * @param \Swiftriver\Core\ObjectModel\Source[] $sources
     */
    public static function SaveSources($sources);

    /**
     * Given a list of IDs this method removes the sources from
     * the data store.
     *
     * @param string[] $ids
     */
    public static function RemoveSources($id);

    /**
     * Given a date time, this function returns the next due
     * Source.
     *
     * @param DateTime $time
     * @return \Swiftriver\Core\ObjectModel\Source
     */
    public static function SelectNextDueSource($time);

    /**
     * Lists all the current Source in the core
     * @return \Swiftriver\Core\ObjectModel\Source[]
     */
    public static function ListAllSources();
}
?>

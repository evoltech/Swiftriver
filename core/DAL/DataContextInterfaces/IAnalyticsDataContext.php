<?php
namespace Swiftriver\Core\DAL\DataContextInterfaces;
/**
 * @author mg[at]swiftly[dot]org
 */
interface IAnalyticsDataContext
{
    /**
     * Given a set of parameters, this function will calculate the
     * current activity over time per source.
     *
     * Acceptable parameters are:
     *
     * STARTDATE => the date() to act as the initial time constraint
     * ENDDATE => the date() to act as the final time constraint
     * PERIOD => HOURS|DAYS|WEEKS the perod to split the data set by
     *
     * @return \Swiftriver\Core\ObjectModel\AnalyticsDataSets\SourceActivityOverTimeDataSet
     */
    public static function GetSourceActivityOverTime($parameters);
}
?>

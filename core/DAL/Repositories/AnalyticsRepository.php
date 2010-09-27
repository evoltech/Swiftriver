<?php
namespace Swiftriver\Core\DAL\Repositories;
/**
 * @author mg[at]swiftly[dot]org
 */
class AnalyticsRepository
{
    /**
     * The fully qualified type of the IAnalyticsDataContext implemting
     * data context for this repository
     * @var \Swiftriver\Core\DAL\DataContextInterfaces\IDataContext
     */
    private $dataContext;

    /**
     * The constructor for this repository
     * Accepts the fully qulaified type of the IDataContext implemting
     * data context for this repository
     *
     * @param string $dataContext
     */
    public function __construct($dataContext = null)
    {
        if(!isset($dataContext))
            $dataContext = \Swiftriver\Core\Setup::DALConfiguration()->DataContextType;

        $classType = (string) $dataContext;

        $this->dataContext = new $classType();
    }

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
    public static function GetSourceActivityOverTime($parameters)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::DAL::Repositories::AnalyticsRepository::GetSourceActivityOverTime [Method invoked]", \PEAR_LOG_DEBUG);

        $dc = $this->dataContext;

        $return = $dc::GetSourceActivityOverTime($parameters);

        $logger->log("Core::DAL::Repositories::AnalyticsRepository::GetSourceActivityOverTime [Method finsihed]", \PEAR_LOG_DEBUG);

        return $return;
    }
}
?>

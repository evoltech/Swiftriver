<?php
namespace Swiftriver\Core\DAL\Repositories;
class SourceRepository {
    /**
     * The fully qualified type of the ISourceDataContext implemting
     * data context for this repository
     * @var \Swiftriver\Core\DAL\DataContextInterfaces\IDataContext
     */
    private $dataContext;

    /**
     * The constructor for this repository
     * Accepts the fully qulaified type of the IAPIKeyDataContext implemting
     * data context for this repository
     *
     * @param string $dataContext
     */
    public function __construct($dataContext = null) {
        if(!isset($dataContext))
            $dataContext = \Swiftriver\Core\Setup::DALConfiguration()->DataContextType;
        $classType = (string) $dataContext;
        $this->dataContext = new $classType();
    }

    /**
     * Given the IDs of Sources, this method
     * gets them from the underlying data store
     *
     * @param string[] $ids
     * @return \Swiftriver\Core\ObjectModel\Source[]
     */
    public function GetSourcesById($ids) {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::DAL::Repositories::SourceRepository::GetSourcesById [Method invoked]", \PEAR_LOG_DEBUG);
        $dc = $this->dataContext;
        $sources = $dc::GetSourcesById($id);
        $logger->log("Core::DAL::Repositories::SourceRepository::GetSourcesById [Method Finished]", \PEAR_LOG_DEBUG);
        return $sources;
    }

    /**
     * Adds a list of new Sources to the data store
     *
     * @param \Swiftriver\Core\ObjectModel\Source[] $sources
     */
    public function SaveSources($sources){
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::DAL::Repositories::SourceRepository::SaveSources [Method invoked]", \PEAR_LOG_DEBUG);
        $dc = $this->dataContext;
        $dc::SaveSources($sources);
        $logger->log("Core::DAL::Repositories::SourceRepository::SaveSources [Method Finished]", \PEAR_LOG_DEBUG);
    }

    /**
     * Given a list of IDs this method removes the sources from
     * the data store.
     *
     * @param string[] $ids
     */
    public function RemoveSources($ids){
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::DAL::Repositories::SourceRepository::RemoveSources [Method invoked]", \PEAR_LOG_DEBUG);
        $dc = $this->dataContext;
        $dc::RemoveSources($ids);
        $logger->log("Core::DAL::Repositories::SourceRepository::RemoveSources [Method Finished]", \PEAR_LOG_DEBUG);
    }

    /**
     * Given a date time, this function returns the next due
     * Source.
     *
     * @param DateTime $time
     * @return \Swiftriver\Core\ObjectModel\Source
     */
    public function SelectNextDueSource($time){
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::DAL::Repositories::SourceRepository::SelectNextDueSource [Method invoked]", \PEAR_LOG_DEBUG);
        $dc = $this->dataContext;
        $source = $dc::SelectNextDueSource($time);
        $logger->log("Core::DAL::Repositories::SourceRepository::SelectNextDueSource [Method Finished]", \PEAR_LOG_DEBUG);
        return $source;
    }

    /**
     * Lists all the current Source in the core
     * @return \Swiftriver\Core\ObjectModel\Source[]
     */
    public function ListAllSources(){
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::DAL::Repositories::SourceRepository::ListAllSources [Method invoked]", \PEAR_LOG_DEBUG);
        $dc = $this->dataContext;
        $sources = $dc::ListAllSources();
        $logger->log("Core::DAL::Repositories::SourceRepository::ListAllSources [Method Finished]", \PEAR_LOG_DEBUG);
        return $sources;
    }
}
?>

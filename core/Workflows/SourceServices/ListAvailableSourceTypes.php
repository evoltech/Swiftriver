<?php
namespace Swiftriver\Core\Workflows\SourceServices;
class ListAvailableSourceTypes extends SourceServicesBase {
    /**
     * List all the Available types of sources that can be configured in
     * the core
     *
     * @param string $key
     * @return string $json
     */
    public function RunWorkflow($key) {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ListAvailableSourceTypes::RunWorkflow [Method invoked]", \PEAR_LOG_INFO);
        
        $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ListAvailableSourceTypes::RunWorkflow [START: Constructing SiSPS]", \PEAR_LOG_DEBUG);
        
        $service = new \Swiftriver\Core\Modules\SiSPS\SwiftriverSourceParsingService();
        
        $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ListAvailableSourceTypes::RunWorkflow [END: Constructing SiSPS]", \PEAR_LOG_DEBUG);
        
        $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ListAvailableSourceTypes::RunWorkflow [START: Getting the list of available parsers]", \PEAR_LOG_DEBUG);
        
        $parsers = $service->ListAvailableParsers();
        
        $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ListAvailableSourceTypes::RunWorkflow [END: Getting the list of available parsers]", \PEAR_LOG_DEBUG);
        
        $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ListAvailableSourceTypes::RunWorkflow [START: Parsing to return JSON]", \PEAR_LOG_DEBUG);
        
        $json = parent::ParseParsersToJSON($parsers);
        
        $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ListAvailableSourceTypes::RunWorkflow [END: Parsing to return JSON]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ListAvailableSourceTypes::RunWorkflow [Method finished]", \PEAR_LOG_INFO);

        return parent::FormatReturn($json);
    }
}
?>

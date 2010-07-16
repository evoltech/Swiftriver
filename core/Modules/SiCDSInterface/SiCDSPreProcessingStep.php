<?php
namespace Swiftriver\PreProcessingSteps;
class SiCDSPreProcessingStep implements \Swiftriver\Core\PreProcessing\IPreProcessingStep {
    /**
     * The short name for this pre processing step, should be no longer
     * than 50 chars
     *
     * @return string
     */
    public function Name() {
        return "SiCDS";
    }

    /**
     * The description of this step
     *
     * @return string
     */
    public function Description() {
        return "This is the impulse turbine for the Swiftriver Content De-Duplication ".
               "server. If activated, all content will be scanned by the SiCDS service and " .
               "attempats will be made to prevent any duplicates from reaching you.";
    }

    /**
     * This method returns an array of the required paramters that
     * are nessesary to run this step.
     *
     * @return \Swiftriver\Core\ObjectModel\ConfigurationElement[]
     */
    public function ReturnRequiredParameters() {
        return array(
            new \Swiftriver\Core\ObjectModel\ConfigurationElement(
                    "Service Url",
                    "string",
                    "The Url of the cloud or locally hosted instsnce of the SiCDS service"
            ),
            new \Swiftriver\Core\ObjectModel\ConfigurationElement(
                    "API Key",
                    "string",
                    "The api key you will need to communicate with the SiCDS service"
            ),
        );
    }

    /**
     * Interface method that all PrePorcessing Steps must implement
     *
     * @param \Swiftriver\Core\ObjectModel\Content[] $contentItems
     * @param \Swiftriver\Core\Configuration\ConfigurationHandlers\CoreConfigurationHandler $configuration
     * @param \Log $logger
     * @return \Swiftriver\Core\ObjectModel\Content[]
     */
    public function Process($contentItems, $configuration, $logger) {
        try {
            $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [Method invoked]", \PEAR_LOG_DEBUG);

            $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [START: Loading module configuration]", \PEAR_LOG_DEBUG);

            $config = \Swiftriver\Core\Setup::DynamicModuleConfiguration()->Configuration;

            if(!key_exists($this->Name(), $config)) {
                $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [The SiCDS Pre Processing Step was called but no configuration exists for this module]", \PEAR_LOG_ERR);
                $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [Method finished]", \PEAR_LOG_DEBUG);
                return;
            }

            $config = $config[$this->Name()];

            foreach($this->ReturnRequiredParameters() as $requiredParam) {
                if(!key_exists($requiredParam->name, $config)) {
                    $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [The SiCDS Pre Processing Step was called but all the required configuration properties could not be loaded]", \PEAR_LOG_ERR);
                    $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [Method finished]", \PEAR_LOG_DEBUG);
                    return;
                }
            }

            $apiKey = (string) $config["API Key"]->value;

            $serviceUrl = (string) $config["Service Url"]->value;

            $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [END: Loading module configuration]", \PEAR_LOG_DEBUG);

            $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [START: Parsing the content items into JSON]", \PEAR_LOG_DEBUG);

            $parser = new \Swiftriver\SiCDSInterface\Parser();

            $jsonForService = $parser->ParseToRequestJson($contentItems, $apiKey);

            $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [END: Parsing the content items into JSON]", \PEAR_LOG_DEBUG);

            $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [START: Interfacing with the SiCDS]", \PEAR_LOG_DEBUG);

            $serviceInterface = new \Swiftriver\SiCDSInterface\ServiceInterface();

            $jsonFromService = $serviceInterface->InterafceWithService(
                    $serviceUrl,
                    $jsonForService,
                    $configuration
            );

            $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [END: Interfacing with the SiCDS]", \PEAR_LOG_DEBUG);

            $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [START: Parsing the return from the SiCDS]", \PEAR_LOG_DEBUG);

            $uniqueIds = $parser->ParseResponseFromJsonToUniqueIds($jsonFromService);

            $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [END: Parsing the return from the SiCDS]", \PEAR_LOG_DEBUG);

            $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [START: removing duplicates from the content items array]", \PEAR_LOG_DEBUG);

            $uniqueContentItems = array();

            foreach($contentItems as $item) {
                foreach($uniqueIds as $id) {
                    if($item->id == $id) {
                        $uniqueContentItems[] = $item;
                    }
                }
            }

            $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [END: removing duplicates from the content items array]", \PEAR_LOG_DEBUG);

            $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [Method finished]", \PEAR_LOG_DEBUG);

            return $uniqueContentItems;
        }
        catch (\Exception $e) {
            $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [An exception was thrown]", \PEAR_LOG_ERR);
            $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [$e]", \PEAR_LOG_ERR);
            $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [Method finished]", \PEAR_LOG_DEBUG);
            return $contentItems;
        }
    }
}
?>
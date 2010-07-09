<?php
namespace Swiftriver\Core\Configuration\ConfigurationHandlers;
class EventDistributionConfigurationHandler extends BaseConfigurationHandler {

    /**
     * The file path of the associated configuration file
     * @var string
     */
    private $configurationFilePath;

    /**
     * The ordered collection of pre preocessing steps
     * @var \Swiftriver\Core\ObjectModel\PreProcessingStepEntry[]
     */
    public $EventHandlers;

    public function __construct($configurationFilePath) {
        $this->configurationFilePath = $configurationFilePath;
        $xml = parent::SaveOpenConfigurationFile($configurationFilePath, "eventHandlers");
        $this->EventHandlers = array();
        foreach($xml->eventHandlers->handler as $handler) {
            $this->EventHandlers[] =
                    new \Swiftriver\Core\ObjectModel\EventHandlerEntry(
                        (string) $handler["name"],
                        (string) $handler["className"],
                        (string) $handler["filePath"]);
        }
    }

    public function Save(){
        $root = new \SimpleXMLElement("<configuration></configuration>");
        $collection = $root->addChild("eventHandlers");
        foreach($this->EventHandlers as $step) {
            if($step == null)
                continue;
            $element = $collection->addChild("handler");
            $element->addAttribute("name", $step->name);
            $element->addAttribute("className", $step->className);
            $element->addAttribute("filePath", $step->filePath);
        }
        $root->asXML($this->configurationFilePath);
    }
}
?>

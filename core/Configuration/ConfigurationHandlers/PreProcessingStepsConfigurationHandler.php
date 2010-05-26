<?php
namespace Swiftriver\Core\Configuration\ConfigurationHandlers;
class PreProcessingStepsConfigurationHandler extends BaseConfigurationHandler {

    private $configurationFilePath;

    /**
     * @var simpleXMLElement
     */
    public $xml;


    /**
     * The ordered collection of pre preocessing steps
     * @var \Swiftriver\Core\ObjectModel\PreProcessingStepEntry[]
     */
    public $PreProcessingSteps;

    public function __construct($configurationFilePath) {
        $this->configurationFilePath = $configurationFilePath;
        $xml = simplexml_load_file($configurationFilePath);
        $this->xml = $xml;
        $this->PreProcessingSteps = array();
        foreach($xml->preProcessingSteps->step as $step) {
            $this->PreProcessingSteps[] =
                    new \Swiftriver\Core\ObjectModel\PreProcessingStepEntry(
                        (string) $step["name"],
                        (string) $step["className"],
                        (string) $step["filePath"]);
        }
    }

    public function Save(){
        $root = new \SimpleXMLElement("<configuration></configuration>");
        $collection = $root->addChild("preProcessingSteps");
        foreach($this->PreProcessingSteps as $step) {
            if($step == null)
                continue;
            $element = $collection->addChild("step");
            $element->addAttribute("name", $step->name);
            $element->addAttribute("className", $step->className);
            $element->addAttribute("filePath", $step->filePath);
        }
        $root->asXML($this->configurationFilePath);
    }
}
?>
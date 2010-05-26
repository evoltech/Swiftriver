<?php
namespace Swiftriver\PreProcessingSteps;
class SiLCCPreProcessingStep implements \Swiftriver\Core\PreProcessing\IPreProcessingStep {

    public function Description(){
        return "When using this river turbine, all content will be sent to the Swift Web Service: " .
               "Swithriver Language Computational Core (or SiLCC as we like to call it). This service " .
               "will atempt to apply natural language tagging to all content items.";
    }
    public function Name(){
        return "SiLCC";
    }
    public function Process($contentItems, $configuration, $logger) {
        return $contentItems;
    }
    public function ReturnRequiredParameters() {
        return array();
    }
}
?>

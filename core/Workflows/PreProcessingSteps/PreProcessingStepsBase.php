<?php
namespace Swiftriver\Core\Workflows\PreProcessingSteps;
class PreProcessingStepsBase extends \Swiftriver\Core\Workflows\WorkflowBase {
    public function ParseStepsToJson($steps) {
        $return;
        $return->steps = array();
        foreach($steps as $step) {
            $s;
            $s->name = $step->Name();
            $s->description = $step->Description();
            $s->configurationProperties = $step->ReturnRequiredParameters();
            $s->active = isset($step->active);
            $return->steps[] = $s;
            unset($s);
        }
        return json_encode($return);
    }

    public function ParseJsonToPreProcessingStepName($json) {
        $result = json_decode($json);

        if(!$result || $result == null) {
            throw new \InvalidArgumentException("The json was malformed");
        }

        if(!isset($result->name) || !is_string($result->name)) {
            throw new \InvalidArgumentException("The JSON did not contain the required 'name' string");
        }

        return $result->name;
    }
}
?>

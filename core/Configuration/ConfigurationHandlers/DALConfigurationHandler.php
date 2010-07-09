<?php
namespace Swiftriver\Core\Configuration\ConfigurationHandlers;
class DALConfigurationHandler extends BaseConfigurationHandler {

    public $DataContextType;

    public $DataContextDirectory;

    public function __construct($configurationFilePath) {
        $xml = parent::SaveOpenConfigurationFile($configurationFilePath, "properties");
        foreach($xml->properties->property as $property) {
            switch((string) $property["name"]) {
                case "DataContextType" :
                    $this->DataContextType = $property["value"];
                    break;
                case "DataContextPath" :
                    $this->DataContextDirectory = $property["value"];
                    break;
            }
        }
    }
}
?>

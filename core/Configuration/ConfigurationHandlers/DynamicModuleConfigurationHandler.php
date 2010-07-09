<?php
namespace Swiftriver\Core\Configuration\ConfigurationHandlers;
class DynamicModuleConfigurationHandler extends BaseConfigurationHandler {

    /**
     * @var string
     */
    private $configurationFilePath;

    /**
     * @var Associative Array
     */
    public $Configuration = array();

    public function __construct($configurationFilePath){
        $this->configurationFilePath = $configurationFilePath;
        $xml = parent::SaveOpenConfigurationFile($configurationFilePath, "modules");
        foreach($xml->modules->module as $module) {
            $moduleName = (string) $module["name"];
            $configuration = array();
            foreach($module->properties->property as $property) {
                $name = (string) $property["name"];
                $type = (string) $property["type"];
                $description = (string) $property["description"];
                $value = (string) $property["value"];
                $configEelement = new \Swiftriver\Core\ObjectModel\ConfigurationElement(
                        $name,
                        $type,
                        $description,
                        $value);
                $configuration[$name] = $configEelement;
            }
            $this->Configuration[$moduleName] = $configuration;
        }
    }

    public function Save() {
        $root = new \SimpleXMLElement("<configuration></configuration>");
        $modulesCollection = $root->addChild("modules");
        foreach($this->Configuration as $key => $value) {
            $module = $modulesCollection->addChild("module");
            $module->addAttribute("name", $key);
            $properties = $module->addChild("properties");
            foreach($value as $configurationElement) {
                $property = $properties->addChild("property");
                $property->addAttribute("name", $configurationElement->name);
                $property->addAttribute("type", $configurationElement->type);
                $property->addAttribute("description", $configurationElement->description);
                $property->addAttribute("value", $configurationElement->value);
            }
        }
        $root->asXML($this->configurationFilePath);
    }
}
?>
<?php
namespace Swiftriver\Core\ObjectModel;
class PreProcessingStepEntry {
    
    /**
     * The name of the Pre Processor
     * @var string
     */
    public $name;

    /**
     * The class name of the pre processing step
     * @var string
     */
    public $className;

    /**
     * The file path to the pre processing step relative to the
     * modules directory of the core install
     * @var string
     */
    public $filePath;
    
    public function __construct($name, $className, $filePath) {
        $this->name = $name;
        $this->className = $className;
        $this->filePath = $filePath;
    }
}
?>

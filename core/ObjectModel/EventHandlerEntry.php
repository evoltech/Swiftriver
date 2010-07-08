<?php
namespace Swiftriver\Core\ObjectModel;
class EventHandlerEntry {
    /**
     * The name of this event handler
     * @var string
     */
    public $name;

    /**
     * The class name of the event handler
     * @var string
     */
    public $className;

    /**
     * The file path to the event handler relative to the
     * modules directory of the core install
     * @var string
     */
    public $filePath;

    /**
     * Constructor for the EventHandlerEntry config element
     * @param string $name
     * @param string $className
     * @param string $filePath
     */
    public function __construct($name, $className, $filePath) {
        $this->name = $name;
        $this->className = $className;
        $this->filePath = $filePath;
    }
}
?>

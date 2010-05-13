<?php
namespace Swiftriver\Core;

require_once 'PHPUnit/Framework.php';

class ListAvailableSourceTypesTest extends \PHPUnit_Framework_TestCase  {
    private $object;

    protected function setUp() {
        include_once(dirname(__FILE__)."/../../../Setup.php");
        $this->object = new Workflows\SourceServices\ListAvailableSourceTypes();

    }

    public function test() {
        $this->object->RunWorkflow("swiftriver_dev");
    }
}
?>

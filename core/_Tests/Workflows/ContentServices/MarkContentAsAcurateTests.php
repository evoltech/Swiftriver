<?php
namespace Swiftriver\Core;

require_once 'PHPUnit/Framework.php';

class MarkContentAsAcurateTest extends \PHPUnit_Framework_TestCase  {
    public function test() {
        include_once(dirname(__FILE__)."/../../../Setup.php");
        $workflow = new Workflows\ContentServices\MarkContentAsAcurate();
        $message = $workflow->RunWorkflow('{"id":"1dd941a0395606d0691d1ce0772a5213","markerId":"someotherid"}', "somekey");
    }
}
?>

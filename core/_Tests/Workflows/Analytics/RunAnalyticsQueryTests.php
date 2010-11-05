<?php
namespace Swiftriver\Core;
require_once 'PHPUnit/Framework.php';
class RunAnalyticsTests extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        include_once(\dirname(__FILE__)."/../../../Setup.php");
    }

    public function test()
    {
        $workflow = new Workflows\Analytics\RunAnalyticsQuery();

        $key = "swiftriver_dev";

        $json = '{"RequestType":"ContentByChannelOverTimeAnalyticsProvider","Parameters":{"TimeLimit":7}}';

        $result = $workflow->RunQuery($json, $key);
    }
}
?>

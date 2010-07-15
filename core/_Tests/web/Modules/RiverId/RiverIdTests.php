<?php
namespace Swiftriver\Core;
require_once 'PHPUnit/Framework.php';
class RiverIdTests extends \PHPUnit_Framework_TestCase {
    public function test() {
        include_once(dirname(__FILE__)."/../../../../../web/modules/riverid/classes/RiverId.php");
        include_once(dirname(__FILE__)."/../../../../../web/modules/riverid/classes/RiverIdConfig.php");
        \RiverId::log_in("matt", "password");
    }
}
?>

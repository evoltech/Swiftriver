<?php
namespace Swiftriver\Core;
require_once 'PHPUnit/Framework.php';
class IAMPParserTest extends \PHPUnit_Framework_TestCase {
    /**
     * Include the SiSPS Setup
     */
    protected function setUp() {
        include_once(dirname(__FILE__)."/../../../../Setup.php");
    }

    public function test() {
        $channel->lastSuccess = 1000;
        $channel->subType = "all";
        $channel->parameters = array (
            "IMAPHostName" => "imap.gmail.com:993/imap/ssl/novalidate-cert",
            "IMAPUserName" => "mrmatthewgriffiths@gmail.com",
            "IMAPPassword" => "hellit");
        $parser = new Modules\SiSPS\Parsers\IMAPParser();
        $content = $parser->GetAndParse($channel);
    }
}
?>

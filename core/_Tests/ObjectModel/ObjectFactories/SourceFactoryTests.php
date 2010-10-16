<?php
namespace Swiftriver\Core;

require_once 'PHPUnit/Framework.php';

class SourceFacoryTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        include_once(dirname(__FILE__)."/../../../Setup.php");
    }

    public function testCreateSourceFromJson()
    {
        $id = "testId";
        $score = 10;
        $name = "testName";
        $email = "testEmail";
        $link = "http://test.link";
        $parent = "testParent";
        $type = "testType";
        $subType = "testSubType";
        $gisData = array(
            new ObjectModel\GisData(1.1, 1, "test one"),
            new ObjectModel\GisData(2.2, 2, "test two"));

        $source = new ObjectModel\Source();
        $source->id = $id;
        $source->score = $score;
        $source->name = $name;
        $source->email = $email;
        $source->link = $link;
        $source->parent = $parent;
        $source->type = $type;
        $source->subType = $subType;
        $source->gisData = $gisData;

        $json = \json_encode($source);

        $factory = new ObjectModel\ObjectFactories\SourceFactory();

        $s = $factory->CreateSourceFromJSON($json);

        $this->assertEquals($s->id, $id);
        $this->assertEquals($s->name, $name);
        $this->assertEquals($s->score, $score);
        $this->assertEquals($s->email, $email);
        $this->assertEquals($s->link, $link);
        $this->assertEquals($s->parent, $parent);
        $this->assertEquals($s->type, $type);
        $this->assertEquals($s->subType, $subType);
        $this->assertTrue(\is_array($s->gisData));
        $this->assertEquals(2, \count($s->gisData));
        $gis0 = $s->gisData[0];
        $this->assertEquals(1.1, $gis0->longitude);
        $this->assertEquals(1, $gis0->latitude);
        $this->assertEquals("test one", $gis0->name);
        $gis1 = $s->gisData[1];
        $this->assertEquals(2.2, $gis1->longitude);
        $this->assertEquals(2, $gis1->latitude);
        $this->assertEquals("test two", $gis1->name);
    }
}
?>

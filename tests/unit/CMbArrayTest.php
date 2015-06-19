<?php
/**
 * $Id: CMbArrayTest.php pbriton $
 *
 * @package    Mediboard
 * @subpackage tests
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

require_once __DIR__."/autoload.php";
CMbTestAutoloader::register();

/**
 * Class CMbArrayTest
 */
class CMbArrayTest extends PHPUnit_Framework_TestCase {

  /** @var CMbArray $stub */
  protected $stub;

  protected function setUp() {
    parent::setUp();
    $this->stub = $this->getMockForAbstractClass("CMbArray");
  }


  public function testCompareKeysHasRightReturn() {
    $array1 = array("key1" => "val1", "key2" => "val2");
    $array2 = array("key1" => "val1", "key2" => "val2");
    $this->assertEmpty($this->stub->compareKeys($array1, $array2));

    $array1 = array("key1" => "val", "key2" => "val2");
    $this->assertContains("different_values",$this->stub->compareKeys($array1, $array2));

    $array1 = array("key2" => "val2");
    $this->assertContains("absent_from_array1",$this->stub->compareKeys($array1, $array2));

    $array1 = array("key1" => "val1", "key2" => "val2");
    $array2 = array("key1" => "val1");
    $this->assertContains("absent_from_array2",$this->stub->compareKeys($array1, $array2));
  }


  public function testDiffRecursiveHasRightReturn() {
    $this->assertFalse($this->stub->diffRecursive(array(),array()));
    $this->assertFalse($this->stub->diffRecursive(array(1),array(1)));
    $this->assertFalse($this->stub->diffRecursive(array(array(),array(2)),array(array(),array(2))));

    $array1 = array(
      "key1" => "val1",
      "key2" => "val2",
      array(
        "key3" => "val3",
        "key4" => "val4",
        array(
          "key5" => "valDiff"
        )
      ),
      array(
        "keyDiff" => "val6"
      )
    );

    $array2 = array(
      "key1" => "val1",
      "key2" => "val2",
      array(
        "key3" => "val3",
        "key4" => "val4",
        array(
          "key5" => "val5"
        )
      ),
      array(
        "key6" => "val6"
      )
    );

    $resArray = array(
      array(
        array(
          "key5" => "valDiff"
        )
      ),
      array(
        "keyDiff" => "val6"
      )
    );
    $this->assertEquals($resArray,$this->stub->diffRecursive($array1,$array2));

    $array1 = array("key" => "value", array("key" => "value"));
    $this->assertEquals($array1,$this->stub->diffRecursive($array1,null));
    $this->assertEquals($array1,$this->stub->diffRecursive($array1,array("key")));
    $this->assertEquals(array(2 => null),$this->stub->diffRecursive(array(1 => 1,2 => null),array(1 => 1)));
  }


  public function testRemoveValueHasRightReturn() {
    $array = array();
    $this->assertEquals(0,$this->stub->removeValue(0,$array));
    $array = array(
      "key1" => "value1",
      "key2" => "value2",
      "key3" => "value3",
    );
    $this->assertEquals(1,$this->stub->removeValue("value2",$array));
    $array = array(
      "key1" => "value1",
      "key2" => "value1",
      "key3" => "value3",
    );
    $this->assertEquals(2,$this->stub->removeValue("value1",$array));
  }


  public function testGetPrevNextKeysHasRightReturn() {
    $resArray = array(
      "prev" => null,
      "next" => "key2"
    );
    $this->assertEquals($resArray,$this->stub->getPrevNextKeys(array("key1" => "val1","key2" => "val2"),"key1"));

    $resArray = array(
      "prev" => "key1",
      "next" => "key3"
    );
    $this->assertEquals($resArray,$this->stub->getPrevNextKeys(array(
      "key1" => "val1",
      "key2" => "val2",
      "key3" => "val3"),"key2"));

    $resArray = array(
      "prev" => "key1",
      "next" => null
    );
    $this->assertEquals($resArray,$this->stub->getPrevNextKeys(array(
      "key1" => "val1",
      "key2" => "val2"),"key2"));
  }


  public function testMergeRecursive() {
    $this->assertNull($this->stub->mergeRecursive(null,null));
    $this->assertNull($this->stub->mergeRecursive(array(1),null));

    $array1 = array(0 => "val1");
    $array2 = array(1 => "val2");
    $this->assertNotEmpty($this->stub->mergeRecursive($array1,$array2));
    $this->assertEquals(array(0 => "val1", 1 => "val2"),$this->stub->mergeRecursive($array1,$array2));

    $array1 = array(0 => "val1");
    $array2 = array(
      1 => "val2",
      array(
        2 => "val3",
        array(
          3 => "val4"
        )
      )
    );
    $resArray = array(
      0 => "val1",
      1 => "val2",
      array(
        2 => "val3",
        array(
          3 => "val4"
        )
      )
    );
    $this->assertEquals($resArray,$this->stub->mergeRecursive($array1,$array2));
  }


  public function testMergeKeys() {
    $this->assertNotEmpty($this->stub->mergeKeys(array(1),array(1)));
    $this->assertEquals(array(1=>1,2=>1),$this->stub->mergeKeys(array(1=>1),array(2=>1)));
    $this->assertEquals(array(1=>1,2=>1,3=>1),$this->stub->mergeKeys(array(1=>1),array(2=>1),array(3=>1)));
  }


  public function testGet() {
    $this->assertNull($this->stub->get(null,null));
    $this->assertEquals("val",$this->stub->get(array("key"=>"val"),"key"));
  }


  public function testFirst() {
    $this->assertEquals("val1",
      $this->stub->first(
        array("key1" => "val1", "key2" => "val2", "key3" => "val3"),
        array("key1","key2")
      ));

    $this->assertNull($this->stub->first(array("val"),array("notAkey")));
  }


  /**
   * @expectedException         PHPUnit_Framework_Error_Warning
   */
  public function testExtractThrowException() {
    $array = array("key" => "val");
    $this->stub->extract($array, "notAkey", null, true);
  }


  public function testExtract() {
    $array = array(
      "key" => "val",
      "key2" => "val2"
    );
    $this->assertEquals("val",$this->stub->extract($array, "key"));
    $this->assertNull($this->stub->extract($array, "notAkey"));
  }


  public function testDefaultValue() {
    $array = array("key" => "val");
    $this->stub->defaultValue($array, "key2", "val2");
    $this->assertArrayHasKey("key2",$array);
    $this->assertEquals("val2",$array["key2"]);
  }


  public function testMakeXmlAttributesHasRightReturn() {
    $array = array("key" => "val");
    $this->assertEquals('',$this->stub->makeXmlAttributes(array()));
    $this->assertEquals("key=\"val\" ",$this->stub->makeXmlAttributes($array));
  }


  /**
   * @expectedException         PHPUnit_Framework_Error_Warning
   * @dataProvider              pluckArray
   */
  public function testPluckThrowException($array) {
    $this->stub->pluck($array,"notAprop");
  }

  public function pluckArray() {
    return array(
      array(
        array("key" => new stdClass())
      ),
      array(
        array("key" => "val")
      ),
      array(
        array("key" => array("key2" => "val2"))
      )
    );
  }


  public function testPluck() {
    $this->assertNull($this->stub->pluck(null,""));

    $array = array(
      "key" => array(
        "key2" => "val",
        "key3" => array(
          "key4" => "val"
        )
      ));
    $this->assertEquals(array("key" => "val"),$this->stub->pluck($array, "key2"));

    $array =  array(
      'key' => (object) array("property" => 1),
      'key2' => (object) array("property" => 2)
    );
    $this->assertEquals(array("key" => 1, "key2" => 2),$this->stub->pluck($array, "property"));
  }


  public function testFilterPrefix() {
    $this->assertArrayNotHasKey(
      "filtered",
      $this->stub->filterPrefix(array("key" => "val", "key2" => "val2", 'filtered' => "val3"),"key")
    );
  }


  public function testTranspose() {
    $array = array(
      array("val1","val2","val3"),
      array("val1","val2","val3")
    );
    $res = array(
      array("val1","val1"),
      array("val2", "val2"),
      array("val3", "val3")
    );
    $this->assertEquals($res,$this->stub->transpose($array));
  }


  public function testInsertAfterKey() {
    $array = array(
      "key" => "val",
      "key2" => "val2"
    );
    $this->stub->insertAfterKey($array,"key","newKey","newValue");
    $this->assertArrayHasKey("newKey",$array);
    $this->assertEquals($array["newKey"],"newValue");
  }


  public function testAverage() {
    $this->assertEquals(10,$this->stub->average(array(5,10,15)));
  }


  public function testVariance() {
    $this->assertNull($this->stub->variance('notAnArray'));
    $this->assertEquals(42.050234508528, $this->stub->variance(array(0,33,101)));
  }


  public function testIn() {
    $this->assertTrue($this->stub->in("val1",array("key1" => "val1", "key2" => "val2")));
    $this->assertFalse($this->stub->in("notAval",array("key1" => "val1", "key2" => "val2")));
    $this->assertTrue($this->stub->in("val2",array("key1" => "val1", "key2" => "val2"),true));
    $this->assertFalse($this->stub->in("2",array("key1" => 1, "key2" => 2),true));
    $this->assertTrue($this->stub->in("val2","val1 val2 val3",true));

  }


  public function testFlip() {
    $this->assertEquals(array("val" => array("key")), $this->stub->flip(array("key" => "val")));
    $this->assertEquals(
      array("val" => array("key","key2")),
      $this->stub->flip(array("key" => "val","key2" => "val")));
  }


  public function testCountLeafs() {
    $this->assertEquals(1, $this->stub->countLeafs(null));
    $this->assertEquals(3, $this->stub->countLeafs(array("val", array("val2", array("val3")))));
  }


  public function testKsortByArray() {
    $array = array(
      "key1" => "val1",
      "key2" => "val2",
      "key3" => "val3"
    );
    $order = array("key3","key1","key2");
    $res = array(
      "key3" => "val3",
      "key1" => "val1",
      "key2" => "val2"
    );
    $this->assertEquals($res, $this->stub->ksortByArray($array,$order));
  }


  public function testKsortByProp() {
    $obj1 = new stdClass();
    $obj1->foo = "bar1";

    $obj2 = new stdClass();
    $obj2->foo = "bar2";

    $objects = array($obj2,$obj1);
    $this->assertTrue($this->stub->ksortByProp($objects,"foo"));
    $this->assertEquals(array($obj1,$obj2),$objects);

    $obj1 = new stdClass();
    $obj1->foo = "bar";
    $obj1->baz = "bar1";

    $obj2 = new stdClass();
    $obj2->foo = "bar";
    $obj2->baz = "bar2";
    $objects = array($obj2,$obj1);
    $this->assertTrue($this->stub->ksortByProp($objects,"foo","baz"));
    $this->assertEquals(array($obj1,$obj2),$objects);
  }


  public function testSearchRecursive() {
    $array = array("key1" => "val1",array("key2" => "val2",array("key3" => "val3")));
    $this->assertEquals(array("key1"),$this->stub->searchRecursive("val1",$array));
    $this->assertEquals(array(array(array("key3"))),$this->stub->searchRecursive("val3",$array));
  }


  public function testReadFromPath() {
    $arr = array(
      "key1" => "val1",
      "key2" => "val2"
    );
    $this->assertNull($this->stub->readFromPath(null,null));
    $this->assertEquals("val1",$this->stub->readFromPath($arr,"key1"));
  }


  public function testCountValues() {
    $array = array(
      "key1" => "val",
      "key2" => "val",
      "key3" => "val3",
      "key4" => "val4"
    );
    $this->assertEquals(2,$this->stub->countValues("val",$array));
  }

}

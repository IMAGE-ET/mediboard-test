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
 * Class CMbPathTest
 */
class CMbPathTest extends PHPUnit_Framework_TestCase {

  /** @var CMbPath $stub */
  protected $stub;

  protected function setUp() {
    parent::setUp();
    $this->stub = $this->getMockForAbstractClass("CMbPath");
  }


  /**
   * @expectedException         PHPUnit_Framework_Error_Warning
   * @expectedExceptionMessage  Directory is null
   */
  public function testForceDirExceptionHasRightMessage() {
    $this->stub->forceDir("");
  }


  public function testForceDirHasRightReturn() {
    // To test the return values, disable conversion of errors into exceptions
    $warningEnabledOrig                       = PHPUnit_Framework_Error_Warning::$enabled;
    PHPUnit_Framework_Error_Warning::$enabled = false;

    $this->assertTrue($this->stub->forceDir("/"));
    $this->assertTrue($this->stub->forceDir(__DIR__."/dir"));
    $this->assertFileExists(__DIR__."/dir");
    @rmdir(__DIR__."/dir");
    $this->assertFalse($this->stub->forceDir(null));

    PHPUnit_Framework_Error_Warning::$enabled = $warningEnabledOrig;
  }


  /**
   * @expectedException         PHPUnit_Framework_Error_Warning
   * @expectedExceptionMessage  Passed argument is not a valid directory or couldn't be opened'
   */
  public function testIsEmptyDirExceptionHasRightMessage() {
    $this->stub->isEmptyDir(null);
  }


  public function testIsEmptyDirReturn() {
    // To test the return values, disable conversion of errors into exceptions
    $warningEnabledOrig                       = PHPUnit_Framework_Error_Warning::$enabled;
    PHPUnit_Framework_Error_Warning::$enabled = false;

    $this->assertFalse($this->stub->isEmptyDir(null));
    mkdir(__DIR__."/emptydir");
    $this->assertTrue($this->stub->isEmptyDir(__DIR__."/emptydir"));
    @rmdir(__DIR__."/emptydir");

    PHPUnit_Framework_Error_Warning::$enabled = $warningEnabledOrig;
  }


  /**
   * @expectedException         PHPUnit_Framework_Error_Warning
   * @expectedExceptionMessage  Passed argument is not a valid directory or couldn't be opened'
   */
  public function testPurgeEmptySubdirsHasRightMessage() {
    $this->stub->purgeEmptySubdirs(null);
  }


  public function testPurgeEmptySubdirsHasRightReturn() {
    // To test the return values, disable conversion of errors into exceptions
    $warningEnabledOrig                       = PHPUnit_Framework_Error_Warning::$enabled;
    PHPUnit_Framework_Error_Warning::$enabled = false;

    $this->assertEquals(0, $this->stub->purgeEmptySubdirs(null));

    mkdir(__DIR__."/testPurgeEmptySubdirs");
    mkdir(__DIR__."/testPurgeEmptySubdirs/emptysub1");
    mkdir(__DIR__."/testPurgeEmptySubdirs/emptysub2");
    $this->assertEquals(3, $this->stub->purgeEmptySubdirs(__DIR__."/testPurgeEmptySubdirs"));

    PHPUnit_Framework_Error_Warning::$enabled = $warningEnabledOrig;
  }


  public function testGetExtensionHasRightReturn() {
    $this->assertEquals("jpg", $this->stub->getExtension("test.jpg"));
    $this->assertNull($this->stub->getExtension(""));
    $this->assertNull($this->stub->getExtension("test"));
  }


  public function testGuessMimeTypeHasRightReturn() {
    $this->assertEquals("unknown/", $this->stub->guessMimeType("test"));

    $this->assertEquals("application/x-javascript", $this->stub->guessMimeType("test.js"));

    $this->assertEquals("application/json", $this->stub->guessMimeType("test.json"));

    $this->assertEquals("image/jpg", $this->stub->guessMimeType("test.jpg"));
    $this->assertEquals("image/jpg", $this->stub->guessMimeType("test.jpeg"));
    $this->assertEquals("image/jpg", $this->stub->guessMimeType("test.jpe"));

    $this->assertEquals("image/png", $this->stub->guessMimeType("test.png"));
    $this->assertEquals("image/gif", $this->stub->guessMimeType("test.gif"));
    $this->assertEquals("image/bmp", $this->stub->guessMimeType("test.bmp"));
    $this->assertEquals("image/tiff", $this->stub->guessMimeType("test.tiff"));
    $this->assertEquals("image/tif", $this->stub->guessMimeType("test.tif"));

    $this->assertEquals("text/css", $this->stub->guessMimeType("test.css"));

    $this->assertEquals("application/xml", $this->stub->guessMimeType("test.xml"));

    $this->assertEquals("application/msword", $this->stub->guessMimeType("test.doc"));
    $this->assertEquals("application/msword", $this->stub->guessMimeType("test.docx"));
    $this->assertEquals("application/msword", $this->stub->guessMimeType("test.dot"));

    $this->assertEquals("application/vnd.ms-excel", $this->stub->guessMimeType("test.xls"));
    $this->assertEquals("application/vnd.ms-excel", $this->stub->guessMimeType("test.xlt"));
    $this->assertEquals("application/vnd.ms-excel", $this->stub->guessMimeType("test.xlm"));
    $this->assertEquals("application/vnd.ms-excel", $this->stub->guessMimeType("test.xld"));
    $this->assertEquals("application/vnd.ms-excel", $this->stub->guessMimeType("test.xla"));
    $this->assertEquals("application/vnd.ms-excel", $this->stub->guessMimeType("test.xlc"));
    $this->assertEquals("application/vnd.ms-excel", $this->stub->guessMimeType("test.xlw"));
    $this->assertEquals("application/vnd.ms-excel", $this->stub->guessMimeType("test.xll"));

    $this->assertEquals("application/vnd.oasis.opendocument.text", $this->stub->guessMimeType("test.odt"));

    $this->assertEquals("application/vnd.ms-powerpoint", $this->stub->guessMimeType("test.ppt"));
    $this->assertEquals("application/vnd.ms-powerpoint", $this->stub->guessMimeType("test.pps"));

    $this->assertEquals("application/rtf", $this->stub->guessMimeType("test.rtf"));

    $this->assertEquals("application/pdf", $this->stub->guessMimeType("test.pdf"));

    $this->assertEquals("text/html", $this->stub->guessMimeType("test.html"));
    $this->assertEquals("text/html", $this->stub->guessMimeType("test.htm"));
    $this->assertEquals("text/html", $this->stub->guessMimeType("test.php"));

    $this->assertEquals("text/plain", $this->stub->guessMimeType("test.txt"));
    $this->assertEquals("text/plain", $this->stub->guessMimeType("test.ini"));

    $this->assertEquals("video/mpeg", $this->stub->guessMimeType("test.mpeg"));
    $this->assertEquals("video/mpeg", $this->stub->guessMimeType("test.mpg"));
    $this->assertEquals("video/mpeg", $this->stub->guessMimeType("test.mpe"));

    $this->assertEquals("audio/mpeg3", $this->stub->guessMimeType("test.mp3"));

    $this->assertEquals("audio/wav", $this->stub->guessMimeType("test.wav"));

    $this->assertEquals("audio/aiff", $this->stub->guessMimeType("test.aiff"));
    $this->assertEquals("audio/aiff", $this->stub->guessMimeType("test.aif"));

    $this->assertEquals("video/msvideo", $this->stub->guessMimeType("test.avi"));

    $this->assertEquals("video/x-ms-wmv", $this->stub->guessMimeType("test.wmv"));

    $this->assertEquals("video/quicktime", $this->stub->guessMimeType("test.mov"));

    $this->assertEquals("application/zip", $this->stub->guessMimeType("test.zip"));

    $this->assertEquals("application/x-tar", $this->stub->guessMimeType("test.tar"));

    $this->assertEquals("application/x-shockwave-flash", $this->stub->guessMimeType("test.swf"));

    $this->assertEquals("application/vnd.lotus-notes", $this->stub->guessMimeType("test.nfs"));

    $this->assertEquals("application/vnd.sante400", $this->stub->guessMimeType("test.spl"));
    $this->assertEquals("application/vnd.sante400", $this->stub->guessMimeType("test.rlb"));

    $this->assertEquals("image/svg+xml", $this->stub->guessMimeType("test.svg"));
  }


  /**
   * @expectedException         PHPUnit_Framework_Error_Warning
   * @expectedExceptionMessage  Archive could not be found
   */
  public function testExtractHasRightMessageArchiveNotFound() {
    $this->stub->extract(__DIR__."/notAnArchive", "./");
  }


  public function testExtractHasRightReturn() {
    // To test the return values, disable conversion of errors into exceptions
    $warningEnabledOrig                       = PHPUnit_Framework_Error_Warning::$enabled;
    PHPUnit_Framework_Error_Warning::$enabled = false;

    $this->assertFalse($this->stub->extract(__DIR__."/notAnArchive", __DIR__."/"));

    $file = fopen(__DIR__."/archiveTest.tar.gz", "w");
    fclose($file);
    $this->assertEquals(0, $this->stub->extract(__DIR__."/archiveTest.tar.gz", __DIR__."/"));
    @unlink(__DIR__."/archiveTest.tar.gz");

    $file = fopen(__DIR__."/archiveTest.zip", "w");
    fclose($file);
    $this->assertEquals(0, $this->stub->extract(__DIR__."/archiveTest.zip", __DIR__."/"));
    @unlink(__DIR__."/archiveTest.zip");

    PHPUnit_Framework_Error_Warning::$enabled = $warningEnabledOrig;
  }


  public function testEmptyDirHasRightReturn() {

    $this->assertFalse($this->stub->emptyDir(null));
    $this->assertFalse($this->stub->emptyDir(""));

    mkdir(__DIR__."/test");
    mkdir(__DIR__."/test/test");

    $this->assertTrue($this->stub->emptyDir(__DIR__."/test"));

    @rmdir(__DIR__."/test");
  }


  /**
   * @expectedException         PHPUnit_Framework_Error_Warning
   * @expectedExceptionMessage  Path undefined
   */
  public function testRemoveHasRightMessage() {
    $this->stub->remove(null);
  }

  public function testRemoveHasRightReturn() {
    // To test the return values, disable conversion of errors into exceptions
    $warningEnabledOrig                       = PHPUnit_Framework_Error_Warning::$enabled;
    PHPUnit_Framework_Error_Warning::$enabled = false;

    mkdir(__DIR__."/test");
    $this->assertTrue($this->stub->remove(__DIR__."/test"));
    $this->assertFalse($this->stub->remove("test"));

    touch(__DIR__."/test");
    $this->assertTrue($this->stub->remove(__DIR__."/test"));

    PHPUnit_Framework_Error_Warning::$enabled = $warningEnabledOrig;
  }


  public function testSanitizeBaseNameHasRightReturn() {
    $this->assertEquals("test_test", $this->stub->sanitizeBaseName("test:test"));
    $this->assertEquals("test__test", $this->stub->sanitizeBaseName("test: test"));
    $this->assertEquals("test_test", $this->stub->sanitizeBaseName("test test"));
  }


  public function testReduceHasRightReturn() {
    $this->assertEquals("test", $this->stub->reduce("test/../test"));
  }


  public function testCountFilesHasRightReturn() {
    mkdir(__DIR__."/test");
    touch(__DIR__."/test/file1");
    touch(__DIR__."/test/file2");

    $this->assertEquals(2, $this->stub->countFiles(__DIR__."/test"));

    @unlink(__DIR__."/test/file2");
    @unlink(__DIR__."/test/file1");
    @rmdir(__DIR__."/test");
  }


  public function testCmpFilesHasRightReturn() {

    $this->assertEquals("-1", $this->stub->cmpFiles(__DIR__."/test/a", __DIR__."/test/b"));
    $this->assertEquals("0", $this->stub->cmpFiles(__DIR__."/test/a", __DIR__."/test/a"));
    $this->assertEquals("1", $this->stub->cmpFiles(__DIR__."/test/b", __DIR__."/test/a"));
  }


  public function testGetPathThreeUnderHasRightReturn() {

    $this->assertNull($this->stub->getPathTreeUnder("php", array(), array()));
    $this->assertTrue($this->stub->getPathTreeUnder("test"));

    $extensions = array("php");
    $ignored = array(
      "classes/",
      "/preferences.php",
    );

    mkdir(__DIR__."/test");
    mkdir(__DIR__."/test/test1");
    touch(__DIR__."/test/file1");
    touch(__DIR__."/test/test1/file2");

    $arr1 = array(
      "test1" => array(
        "file2" => true
      ),
      "file1" => true
    );
    $arr2 = $this->stub->getPathTreeUnder(__DIR__."/test");
    $this->assertEquals($arr1, $arr2);

    $arr2 = $this->stub->getPathTreeUnder(__DIR__."/test",$ignored,$extensions);

    @unlink(__DIR__."/test/test1/file2");
    @unlink(__DIR__."/test/file1");
    @rmdir(__DIR__."/test/test1");
    @rmdir(__DIR__."/test");
  }


  public function testGetFilesHasRightReturn() {

    mkdir(__DIR__."/test");
    touch(__DIR__."/test/file1");
    touch(__DIR__."/test/file2");

    $this->assertContains(__DIR__."/test/file1",$this->stub->getFiles(__DIR__."/test"));
    $this->assertContains(__DIR__."/test/file2",$this->stub->getFiles(__DIR__."/test"));

    @unlink(__DIR__."/test/file1");
    @unlink(__DIR__."/test/file2");
    @rmdir(__DIR__."/test");
  }


  public function testGetTempFile() {
    $this->assertTrue(is_resource($this->stub->getTempFile()));
  }


  public function testZipHasRightReturn() {
    $this->assertFalse($this->stub->zip("notAFolder",__DIR__."/notAzip"));

    $this->assertTrue($this->stub->zip(__DIR__,__DIR__."/test.zip"));
    $this->assertFileExists(__DIR__."/test.zip");

    @unlink(__DIR__."/test.zip");
  }


  public function testGetTreeHasRightReturn() {
    $this->assertTrue(is_array($this->stub->getTree(__DIR__."/")));
  }

}

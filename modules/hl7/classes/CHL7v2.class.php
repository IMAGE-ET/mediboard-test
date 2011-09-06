<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hl7", "CHL7v2");

abstract class CHL7v2 {
  static $debug = false;
  
  const LIB_HL7 = "lib/hl7";
  const PREFIX_MESSAGE_NAME   = "message";
  const PREFIX_SEGMENT_NAME   = "segment";
  const PREFIX_COMPOSITE_NAME = "composite";
  
  static $versions = array(
    "2.1",
    "2.2",
    "2.3",
    "2.3.1",
    "2.4",
    "2.5"
  );
  
  static $keep_original = array("MSH.2", "NTE.3", "OBX.5");
  
  static $schemas = array();
  
  /**
   * When explode() is passed an empty $string, it returns a one element array
   *  
   * @param object $delimiter
   * @param object $data
   * @return array
   */
  static function split($delimiter, $data, $dont_split = false) {
    if ($data === "") return array();
    return ($dont_split ? array($data) : explode($delimiter, $data));
  }
  
  static function keep($field_name) {
    return in_array($field_name, self::$keep_original);
  }
  
  abstract function getSpecs();
  
  abstract function getVersion();
  
  function getSchema($type, $name) {
    /*if (empty(self::$schemas)) {
      self::$schemas = SHM::get("hl7-v2-schemas");
    }*/
    
    $version = $this->getVersion();
    
    if (isset(self::$schemas[$version][$type][$name])) {
      return self::$schemas[$version][$type][$name];
    }
    
    if (!in_array($version, self::$versions)) {
      throw new CHL7v2Exception(CHL7v2Exception::VERSION_UNKOWN, $version_dir);
    }
    
    $version_dir = "hl7v".preg_replace("/[^0-9]/", "_", $version);
    $name_dir = preg_replace("/[^A-Z0-9_]/", "", $name);
    
    $this->spec_filename = self::LIB_HL7."/$version_dir/$type$name_dir.xml";
    
    if (!file_exists($this->spec_filename)) {
      throw new CHL7v2Exception(CHL7v2Exception::SPECS_FILE_MISSING, $this->spec_filename);
    }

    $schema = simplexml_load_file($this->spec_filename, "CHL7v2SimpleXMLElement");
    
    self::$schemas[$version][$type][$name] = $schema;
    
    //SHM::put("hl7-v2-schemas", self::$schemas);
    
    return $this->specs = $schema;
  }
  
  /**
   * Debug output
   * 
   * @param string $str
   * @return void
   */
  static function d($str) {
    if (!self::$debug) return;
    mbTrace($str);
  }
}

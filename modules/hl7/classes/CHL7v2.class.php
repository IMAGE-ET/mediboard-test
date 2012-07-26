<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Root class of all the HL7v2 entities
 */
abstract class CHL7v2 {
  static $debug = false;
  
  const LIB_HL7               = "modules/hl7/resources";
  const PREFIX_MESSAGE_NAME   = "message";
  const PREFIX_SEGMENT_NAME   = "segment";
  const PREFIX_COMPOSITE_NAME = "composite";
  
  static $versions = array(
    // International
    "2.1",
    "2.2",
    "2.3",
    "2.3.1",
    "2.4",
    "2.5",
    
    // Extension française
    "FR_2.1", 
    "FR_2.2", 
    "FR_2.3"
  );
  
  static $keep_original = array("MSH.1", "MSH.2", "NTE.3", "OBX.5");
  
  static $schemas = array();
  
  static $ds = false;
  
  /**
   * When explode() is passed an empty $string, it returns a one element array
   *  
   * @param string  $delimiter  The delimiter
   * @param string  $data       The data to split
   * @param boolean $dont_split Return a single element array with all the data
   * 
   * @return array The splitted data
   */
  static function split($delimiter, $data, $dont_split = false) {
    if ($data === "" || $data === null) {
      return array();
    }
    
    return ($dont_split ? array($data) : explode($delimiter, $data));
  }
  
  /**
   * Tells whether to keep the entity as is or not
   * 
   * @return boolean Whether to keep the entity as is or not
   */
  function keep() {
    return in_array($this->name, self::$keep_original);
  }
  
  /**
   * Get the HL7v2 mapping table values
   * 
   * @param string  $table   The table to get
   * @param boolean $from_mb Get the reversed table (MB => HL7)
   * 
   * @return array The table
   */
  static function getTable($table, $from_mb = true){
    if (self::$ds === null) {
      return;
    }
    
    if (self::$ds === false) {
      self::$ds = CSQLDataSource::get("hl7v2");
    }
    
    static $tables = array();
    
    if (isset($tables[$table][$from_mb])) {
      return $tables[$table][$from_mb];
    }
    
    $where = array(
      "number" => self::$ds->prepare("=%", $table)
    );
    
    $cols = array("code_hl7_from", "code_mb_to", "description");
    if ($from_mb) {
      $cols = array("code_mb_from", "code_hl7_to", "description");
    }
    
    $req = new CRequest;
    $req->addSelect($cols);
    $req->addTable("table_entry");
    $req->addWhere($where);
    
    return $tables[$table][$from_mb] = self::$ds->loadHashList($req->getRequest());
  }
  
  /**
   * Return the Mediboard value from an HL7 value
   * 
   * @param array  $table The table to get the value from
   * @param string $value The HL7 value
   * 
   * @return string The Mediboard value
   */
  static function getTableMbValue($table, $value) {
    $data = self::getTable($table, false);
    
    if (empty($data)) {
      return null;
    }
    
    return CValue::read($data, $value, false);
  }
  
  /**
   * Return the HL7 value from a Mediboard value
   * 
   * @param array  $table The table to get the value from
   * @param string $value The Mediboard value
   * 
   * @return string The HL7 value
   */
  static function getTableHL7Value($table, $value) {
    $data = self::getTable($table, true);

    if (empty($data)) {
      return null;
    }
    
    return CValue::read($data, $value, false);
  }
  
  /**
   * Returns a structured HL7 version
   * 
   * @param string $version The version string
   * 
   * @return array The structure
   */
  static function prepareHL7Version($version) {
    if (preg_match("/([A-Z]{2})_(.*)/", $version, $matches)) {
      return array(
        array (
          "2.5",
          // Internationalization Code
          $matches[1],
          // International Version ID
          $matches[2],
        )
      );
    }
    
    return $version;
  }
  
  /**
   * Get the specs of the entity
   * 
   * @return CHL7v2SimpleXMLElement The spec
   */
  abstract function getSpecs();
  
  /**
   * Get the version of the entity
   * 
   * @return string The version
   */
  abstract function getVersion();
  
  /**
   * Get the schema of the entity
   * 
   * @param string $type      The type of schema to get
   * @param string $name      The name of the entity to get the schema of
   * @param string $extension The name of the extension (none, fr, us, ...)
   *
   * @return CHL7v2SimpleXMLElement The schema
   */
  function getSchema($type, $name, $extension = "none") {
    /*if (empty(self::$schemas)) {
      self::$schemas = SHM::get("hl7-v2-schemas");
    }*/
    
    $version = $this->getVersion();
    
    if (isset(self::$schemas[$version][$type][$name][$extension])) {
      return self::$schemas[$version][$type][$name][$extension];
    }

    if (!in_array($version, self::$versions)) {
      $this->error(CHL7v2Exception::VERSION_UNKNOWN, $version);
    }
    
    if ($extension && $extension !== "none" && preg_match("/([A-Z]{2})_(.*)/", $extension, $matches)) {
      $lang = strtolower($matches[1]);
      $v    = "v".str_replace(".", "_", $matches[2]);
      $version_dir = "extensions/$lang/$v";
    }
    else {
      $version_dir = "hl7v".preg_replace("/[^0-9]/", "_", $version);
    }
    
    $name_dir = preg_replace("/[^A-Z0-9_]/", "", $name);
    
    $this->spec_filename = self::LIB_HL7."/$version_dir/$type$name_dir.xml";
    
    if (!file_exists($this->spec_filename)) {
      $this->error(CHL7v2Exception::SPECS_FILE_MISSING, $this->spec_filename);
    }

    $schema = @simplexml_load_file($this->spec_filename, "CHL7v2SimpleXMLElement");
    
    self::$schemas[$version][$type][$name][$extension] = $schema;
    
    //SHM::put("hl7-v2-schemas", self::$schemas);
    
    return $this->specs = $schema;
  }
  
  /**
   * Debug output
   * 
   * @param string $str   The debugging message
   * @param string $color The color of the message
   * 
   * @return void
   */
  static function d($str, $color = null) {
    if (!self::$debug) {
      return;
    }
    
    echo "<pre style='color:$color;'>$str</pre>";
  }
}

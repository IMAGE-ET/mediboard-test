<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/** 
 * Structure d'un message HL7
 * 
 * Message
 * |- Segment              \n
 *   |- Field              |
 *     |- FieldItem        ~
 *       |- Component      ^
 *         |- Subcomponent &
 */


abstract class CHL7v2 {  
  const LIB_HL7 = "lib/hl7";
  const PREFIX_MESSAGE_NAME   = "message";
  const PREFIX_SEGMENT_NAME   = "segment";
  const PREFIX_COMPOSITE_NAME = "composite";
  
  static $versions = array(
    "1",
    "2",
    "3",
    "3_1",
    "4",
    "5"
  );
  
  static $schemas = array();
  
  var $minOccurs     = null;
  var $maxOccurs     = null;
  var $spec_filename = null;
	var $specs         = null;
	var $data          = null;
  
  static function isDate($value) {
    return preg_match("/^\d{8}$/", $value);
  }
  
  static function getDate($value) {
    
  }
  
  static function isTime($value) {
    return preg_match("/^\d{6}$/", $value);
  }
  
  static function isDateTime($value) {
    return preg_match("/^\d{14}$/", $value);
  }
  
  static function isDouble($value) {
    return is_numeric($value) && is_double(floatval($value));
  }
  
  static function isInteger($value) {
    return preg_match("/^\d+$/", $value);
  }
  
  static function isString($value) {
    return is_string($value);
  }
  
  function parse($data) {
    $this->data = $data;
  }
  
  function getFields() {
    return CHL7v2XPath::queryMultipleNodes($this->getSpecs(), "elements");
  }
  
  function getFieldsCount() {
    return CHL7v2XPath::queryCountNode($this->getSpecs(), "elements/field");
  }
  
  function getDescription() {
    return CHL7v2XPath::queryTextNode($this->getSpecs(), "description");
  }
  
  function getFieldDatatype(SimpleXMLElement $spec_field) {    
    return CHL7v2XPath::queryTextNode($spec_field, "datatype");
  }
  
  function getMinOccurs(SimpleXMLElement $spec_field) {
    $this->minOccurs = CHL7v2XPath::queryAttributNode($spec_field, "elements/field", "minOccurs");
  }
  
  abstract function validate();
	
	abstract function getVersion();
  
  abstract function getSpecs();
	
	function getSchema($type, $name) {
		$version = $this->getVersion();
		
		if (isset(self::$schemas[$version][$type][$name])) {
			return self::$schemas[$version][$type][$name];
		}
		
		$version_dir = "hl7v".preg_replace("/[^0-9]/", "_", $version);
		$name_dir = preg_replace("/[^A-Z0-9]/", "", $name);
		
    $this->spec_filename = self::LIB_HL7."/$version_dir/$type$name_dir.xml";
    
    if (!file_exists($this->spec_filename)) {
      throw new CHL7v2Exception($this->spec_filename, CHL7v2Exception::SPECS_FILE_MISSING);
    }

    self::$schemas[$version][$type][$name] = simplexml_load_file($this->spec_filename, "CHL7v2SimpleXMLElement");
    return $this->specs = self::$schemas[$version][$type][$name];
	}
	
	function getMessage(){
		return $this;
	}
}

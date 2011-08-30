<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHL7v2DataType {
  const RE_HL7_DATE = '(?P<year>19|20\d{2})(?:(?P<month>0[1-9]|1[012])(?P<day>0[1-9]|[12][0-9]|3[01])?)?';
  const RE_HL7_TIME = '(?P<hour>[01][1-9]|2[0123])(?:(?P<minute>[0-5][0-9])(?P<second>[0-5][0-9])?)?(?:\.\d{1,4})?';
	
  const RE_MB_DATE = '(?P<year>(?:19|20)\d{2})-(?P<month>0[0-9]|1[012])-(?P<day>0[0-9]|[12][0-9]|3[01])';
  const RE_MB_TIME = '(?P<hour>[01][1-9]|2[0123]):(?P<minute>[0-5][0-9]):(?P<second>[0-5][0-9])';
	
  static $typesBase = array(
    "Date",
    "DateTime",
    "Time",
    "Double", 
    "Integer",
    "String",
  );
  
  static $re_hl7 = array();
  static $re_mb  = array();
	
	protected $type;
	
	static function init(){
    self::$re_hl7 = array(
      "Date"     => '/^'.self::RE_HL7_DATE.'$/',
      "DateTime" => '/^'.self::RE_HL7_DATE.'(?:'.self::RE_HL7_TIME.')?$/',
      "Time"     => '/^'.self::RE_HL7_TIME.'$/',
      "Double"   => '/^\d+(?:\.\d+)?$/', 
      "Integer"  => '/^\d+$/',
      "String"   => '/.*/',
    );
		
    self::$re_mb = array(
      "Date"     => '/^'.self::RE_MB_DATE.'$/',
      "DateTime" => '/^'.self::RE_MB_DATE."[ T]".self::RE_MB_TIME.'$/',
      "Time"     => '/^'.self::RE_MB_TIME.'$/',
      "Double"   => '/^\d+(?:\.\d+)?$/', 
      "Integer"  => '/^\d+$/',
      "String"   => '/.*/',
    );
	}
  
  static function load($type) {
    if (in_array($type, self::$typesBase)) {
      $class = "CHL7v2DataType$type";
      return new $class;
    }
  }
	
	function getRegExpMB(){
		return self::$re_mb[$this->type];
	}
  
  function getRegExpHL7(){
    return self::$re_hl7[$this->type];
  }
  
  function validateHL7($value){
    return preg_match($this->getRegExpHL7(), $value);
	}
  
  function validateMB($value){
    return preg_match($this->getRegExpMB(), $value);
  }
  
  function parseHL7($value) {
    if (!preg_match($this->getRegExpHL7(), $value, $matches)) {
    	throw new CHL7v2Exception(CHL7v2Exception::INVALID_DATA_FORMAT, "HL7", $this->type, $value);
    }
		
    return $matches;
  }
  
  function parseMB($value) {
    if (!preg_match($this->getRegExpMB(), $value, $matches)) {
      throw new CHL7v2Exception(CHL7v2Exception::INVALID_DATA_FORMAT, "MB", $this->type, $value);
    }
		
    return $matches;
  }
  
  function toMB($value){
  	if ($this->validateHL7($value)) {
  		return $value;
  	}
  }
  
  function toHL7($value) {
    if ($this->validateMB($value)) {
      return $value;
    }
  }
}

CHL7v2DataType::init();

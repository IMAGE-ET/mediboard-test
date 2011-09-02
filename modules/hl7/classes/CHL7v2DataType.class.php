<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/**
 * http://www.med.mun.ca/tedhoekman/medinfo/hl7/ch200020.htm
 * 
 * DT  = Date       : YYYY[MM[DD]]
 * TM  = Time       : HH[MM[SS[.S[S[S[S]]]]]][+/-ZZZZ]
 * DTM = DateTime   : YYYY[MM[DD[HHMM[SS[.S[S[S[S]]]]]]]][+/-ZZZZ]
 * TS  = Time stamp : YYYY[MM[DD[HHMM[SS[.S[S[S[S]]]]]]]][+/-ZZZZ] ^ <degree of precision>
 */

/**
 * From Messaging workbench <http://gforge.hl7.org/gf/project/mwb/>
 * @todo Use these regexps
 * 
 * DT=^\d{4}((0\d)|(1[0-2]))((([0-2]\d)|(3[0-1])))?$
 * ST=^((?>\w+)|(?>\s+)|([?[:punct:]]))*$
 * FT=^((?>\w+)|(?>\s+)|([?[:punct:]]))*$
 * TX=^((?>\w+)|(?>\s+)|([?[:punct:]]))*$
 * GTS=^[\x20-\x7e]{1,199}$
 * ID=^[\x20-\x7e]*$
 * IS=^[\x20-\x7e]{1,20}$
 * NM=^[+-]?\d*\.?\d*$
 * SI=^\d{1,4}$
 * TM=^([01]?\d|2[0-3])(([0-5]\d)?)(([0-5]\d)?((.\d{1,4})?)([+-]([0]\d|1[0-3])([0-5]\d)))?$
 * TN=^(\d\d )?(\(\d\d\d\))?\d\d\d-\d\d\d\d([X,x]\d{1,5})?([B,b]\d{1,5})?([C,c][\x20-\x7e]{0,199})?$
 * DTM=^\d{4}(((0[1-9])|(1[0-2]))(((0[1-9])|([1-2]\d)|(3[0-1]))((([01]\d|2[0-3])([0-5]\d))(([0-5]\d)((\.\d{1,4}))?)?)?)?)?([+-](([0]\d|1[0-3])([0-5]\d)))?$
 */

class CHL7v2DataType extends CHL7v2 {
  const RE_HL7_DATE = '(?P<year>\d{4})(?:(?P<month>0[1-9]|1[012])(?P<day>0[1-9]|[12]\d|3[01])?)?';
  const RE_HL7_TIME = '(?P<hour>[01]\d|2[0-3])(?:(?P<minute>[0-5]\d)(?:(?P<second>[0-5]\d)?(?:\.\d{1,4})?)?)?(?P<tz>[+-]\d{4})?';
  
  const RE_MB_DATE  = '(?P<year>\d{4})-(?P<month>0\d|1[012])-(?P<day>0\d|[12]\d|3[01])';
  const RE_MB_TIME  = '(?P<hour>[01]\d|2[0-3]):(?P<minute>[0-5]\d):(?P<second>[0-5]\d)';
  
  static $typesBase = array(
    "Date",
    "DateTime",
    "Time",
    "Double", 
    "Integer",
    "String",
  );
  
  static $typesMap = array(
    "TimeStamp" => "DateTime",
    //"DT"  => "Date",
    //"DTM" => "DateTime",
    //"GTS" => "String",
    //"ID"  => "String",
    //"IS"  => "String",
    //"FT"  => "String",
    //"NM"  => "Double",
    //"SI"  => "String",
    //"ST"  => "String",
    //"TM"  => "Time",
    //"TN"  => "String",
    //"TS"  => "DateTime",
    //"TX"  => "String",
  );
  
  static $re_hl7 = array();
  static $re_mb  = array();
  
  protected $type;
  protected $version;
  
  protected function __construct($datatype, $version) {
    $this->type = (string)$datatype;
    $this->version = $version;
  }
  
  static function init(){
    self::$re_hl7 = array(
      "Date"     => '/^'.self::RE_HL7_DATE.'$/',
      "DateTime" => '/^'.self::RE_HL7_DATE.'(?:'.self::RE_HL7_TIME.')?$/',
      "Time"     => '/^'.self::RE_HL7_TIME.'$/',
      "Double"   => '/^[+-]?\d*\.?\d*$/', 
      "Integer"  => '/^[+-]?\d+$/',
      "String"   => '/.*/',
    );
    
    self::$re_mb = array(
      "Date"     => '/^'.self::RE_MB_DATE.'$/',
      "DateTime" => '/^'.self::RE_MB_DATE.'[ T]'.self::RE_MB_TIME.'$/',
      "Time"     => '/^'.self::RE_MB_TIME.'$/',
      "Double"   => self::$re_hl7["Double"], 
      "Integer"  => self::$re_hl7["Integer"],
      "String"   => self::$re_hl7["String"],
    );
  }
  
  /**
   * @todo Check if all these types will always be a direct match of base types
   * @param string $type
   * @return string
   */
  static function mapToBaseType($type) {
    return CValue::read(self::$typesMap, $type, $type);
  }
  
  /**
   * @param string $type
   * @return CHL7v2DataType
   */
  static function load($type, $version) {
    static $cache = array();
    
    $class_type = self::mapToBaseType($type);
    
    if (isset($cache[$version][$class_type])) {
      return $cache[$version][$class_type];
    }
    
    if (in_array($class_type, self::$typesBase)) {
      $class = "CHL7v2DataType$class_type";
      $instance = new $class($class_type, $version);
    }
    else {
      $instance = new CHL7v2DataTypeComposite($type, $version);
    }
    
    return $cache[$version][$class_type] = $instance;
  }
  
  /**
   * @param string $value
   * @return bool
   */
  function validate($value){
    if (is_array($value)) {
      $count = count($value);
      
      if ($count === 1) {
        $value = $value[0];
      }
      elseif ($count === 0) {
        $value = "";
      }
    }
    
    $valid = preg_match($this->getRegExpHL7(), trim($value));
    
    if (!$valid) {
      throw new Exception("Invalid data for type $this->type : ".var_export($value, true)." Regexp : ".htmlentities($this->getRegExpHL7()));
      return false;
    }
    
    return true;
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
    if ($this->validate($value)) {
      return $value;
    }
  }
  
  function toHL7($value) {
    return $value;
  }
  
  function getSpecs(){
    return $this->getSchema(self::PREFIX_COMPOSITE_NAME, $this->type);
  }
  
  function getVersion(){
    return $this->version;
  }
  
  protected function getRegExpMB(){
    return self::$re_mb[$this->type];
  }
  
  protected function getRegExpHL7(){
    return self::$re_hl7[$this->type];
  }
}

CHL7v2DataType::init();

<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
*/

require_once("./classes/mbFieldSpec.class.php");

class CDateTimeSpec extends CMbFieldSpec {
  
  function getValue($object, $params = null) {
    global $AppUI;
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    $format = mbGetValue(@$params["format"], "%d/%m/%Y %H:%M");
    return smarty_modifier_date_format($propValue, $format);
  }
  
  function getSpecType() {
    return("time");
  }
  
  function checkProperty(&$object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    if (!preg_match ("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})[ \+]([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})$/", $propValue)) {
      return "format de dateTime invalide";
    }
    return null;
  }
  
  function getConfidential(&$object){
    $fieldName = $this->fieldName;
    $propValue =& $object->$fieldName;
    
    $propValue = "19".$this->randomString($this->_nums, 2)."-".$this->randomString($this->_monthes, 1)."-".$this->randomString($this->_days, 1);
    $propValue .= " ".$this->randomString($this->_hours, 1).":".$this->randomString($this->_mins, 1).":".$this->randomString($this->_mins, 1);
  }
  
  function checkFieldType(){
    return "text";
  }
  
  function getDBSpec(){
    return "datetime";
  }
}

?>
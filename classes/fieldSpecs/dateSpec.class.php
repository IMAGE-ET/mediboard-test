<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
*/

require_once("./classes/mbFieldSpec.class.php");

class CDateSpec extends CMbFieldSpec {
  
  function getValue($object, $smarty, $params = null) {
    require_once $smarty->_get_plugin_filepath('modifier','date_format');
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    $format = mbGetValue(@$params["format"], "%d/%m/%Y");
    return ($propValue && $propValue != "0000-00-00") ? 
      smarty_modifier_date_format($propValue, $format) :
      "-";
  }
  
  function getSpecType() {
    return("date");
  }
  
  function checkProperty(&$object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    if (!preg_match ("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/", $propValue)) {
      return "format de date invalide";
    }
    return null;
  }
  
  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $fieldName = $this->fieldName;
    $propValue =& $object->$fieldName;
    
    $propValue = "19".$this->randomString(CMbFieldSpec::$nums, 2).
      "-".$this->randomString(CMbFieldSpec::$months, 1).
      "-".$this->randomString(CMbFieldSpec::$days, 1);
  }
  
  function getDBSpec(){
    return "DATE";
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    return $this->getFormElementDateTime($object, $params, $value, $className, "%d/%m/%Y");
  }
}

?>
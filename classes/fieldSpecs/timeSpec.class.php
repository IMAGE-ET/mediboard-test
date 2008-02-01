<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
*/

require_once("./classes/mbFieldSpec.class.php");

class CTimeSpec extends CMbFieldSpec {
  
  function getValue($object, $smarty, $params = null) {
    require_once $smarty->_get_plugin_filepath('modifier','date_format');
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    $format = mbGetValue(@$params["format"], "%Hh%M");
    return $propValue ? smarty_modifier_date_format($propValue, $format) : "-";
  }
  
  function getSpecType() {
    return("time");
  }
  
  function checkProperty($object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    if (!preg_match ("/^([0-9]{1,2}):([0-9]{1,2})(:([0-9]{1,2}))?$/", $propValue)) {
      return "format de time invalide";
    }
    return null;
  }

  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $fieldName = $this->fieldName;
    $propValue =& $object->$fieldName;
    
    $propValue = $this->randomString(CMbFieldSpec::$hours, 1).":".$this->randomString(CMbFieldSpec::$mins, 1).":".$this->randomString(CMbFieldSpec::$mins, 1);
  }
  
  function getDBSpec(){
    return "TIME";
  }
  
  function getFormHtmlElement($object, $params, $value, $className) {
    CMbArray::defaultValue($params, "size", 5);
    CMbArray::defaultValue($params, "maxLength", 9);
    return $this->getFormElementText($object, $params, $value, $className);
  }
}

?>
<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author Sébastien Fillonneau
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("mbFieldSpec");

class CDateTimeSpec extends CMbFieldSpec {
  
  function getValue($object, $smarty = null, $params = null) {
    if ($smarty) require_once $smarty->_get_plugin_filepath('modifier','date_format');
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    $format = mbGetValue(@$params["format"], "%d/%m/%Y %H:%M");
    return ($propValue && $propValue != "0000-00-00 00:00:00") ?
      smarty_modifier_date_format($propValue, $format) :
      "";
  }
  
  function getSpecType() {
    return("dateTime");
  }
  
  function checkProperty($object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    if (!preg_match ("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})[ \+]([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})$/", $propValue)) {
      if($object->$fieldName == 'current'|| $object->$fieldName ==  'now') {
        $object->$fieldName = mbDateTime();
        return null;
      } 
    	return "format de dateTime invalide : '$propValue'";
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
    $propValue .= " ".$this->randomString(CMbFieldSpec::$hours, 1).
      ":".$this->randomString(CMbFieldSpec::$mins, 1).
      ":".$this->randomString(CMbFieldSpec::$mins, 1);
  }
  
  function getDBSpec(){
    return "DATETIME";
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    return $this->getFormElementDateTime($object, $params, $value, $className, "%d/%m/%Y %H:%M");
  }
}

?>
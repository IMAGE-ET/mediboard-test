<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("mbFieldSpec");

class CDateTimeSpec extends CMbFieldSpec {
  
  function getSpecType() {
    return("dateTime");
  }
  
  function getDBSpec(){
    return "DATETIME";
  }
  
  function getValue($object, $smarty = null, $params = null) {
    if ($smarty) require_once $smarty->_get_plugin_filepath('modifier','date_format');
    
    $propValue = $object->{$this->fieldName};
    
    $format = CMbArray::extract($params, "format", CAppUI::conf("datetime"));
    if ($format === "relative") {
      $relative = CMbDate::relative($propValue, mbDateTime());
      return $relative["count"] . " " . CAppUI::tr($relative["unit"] . ($relative["count"] > 1 ? "s" : ""));
    }
    
    return ($propValue && $propValue != "0000-00-00 00:00:00") ?
      smarty_modifier_date_format($propValue, $format) :
      "";
  }
  
  function checkProperty($object){
    $propValue = &$object->{$this->fieldName};
    
    if (!preg_match ("/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}[ \+][0-9]{1,2}:[0-9]{1,2}(:[0-9]{1,2})?$/", $propValue)) {
      if($propValue === 'current'|| $propValue ===  'now') {
        $propValue = mbDateTime();
        return null;
      } 
    	return "format de dateTime invalide : '$propValue'";
    }
    return null;
  }
  
  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);

    $object->{$this->fieldName} = "19".self::randomString(CMbFieldSpec::$nums, 2).
      "-".self::randomString(CMbFieldSpec::$months, 1).
      "-".self::randomString(CMbFieldSpec::$days, 1).
      " ".self::randomString(CMbFieldSpec::$hours, 1).
      ":".self::randomString(CMbFieldSpec::$mins, 1).
      ":".self::randomString(CMbFieldSpec::$mins, 1);
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    return $this->getFormElementDateTime($object, $params, $value, $className, CAppUI::conf("datetime"));
  }
}

?>
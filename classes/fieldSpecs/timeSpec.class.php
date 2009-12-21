<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("mbFieldSpec");

class CTimeSpec extends CMbFieldSpec {
  
  function getSpecType() {
    return "time";
  }
  
  function getDBSpec(){
    return "TIME";
  }
  
  function checkProperty($object){
    $propValue = &$object->{$this->fieldName};
  
    if (!preg_match ("/^\d{1,2}:\d{1,2}(:\d{1,2})?$/", $propValue)) { 
    	if($propValue === 'current' || $propValue ===  'now') {
        $propValue = mbTime();
        return null;
      }
      return "Format d'heure invalide";
    }
  }
  
  function getValue($object, $smarty = null, $params = array()) {
    require_once $smarty->_get_plugin_filepath('modifier','date_format');
    $propValue = $object->{$this->fieldName};
    $format = CValue::first(@$params["format"], CAppUI::conf("time"));
    return $propValue ? smarty_modifier_date_format($propValue, $format) : "";
  }

  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $object->{$this->fieldName} = 
      self::randomString(CMbFieldSpec::$hours, 1).":".
      self::randomString(CMbFieldSpec::$mins, 1).":".
      self::randomString(CMbFieldSpec::$mins, 1);
  }
  
  function getFormHtmlElement($object, $params, $value, $className) {
    return $this->getFormElementDateTime($object, $params, $value, $className, CAppUI::conf("time"));
  }
}

?>
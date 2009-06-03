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
  
  function getValue($object, $smarty = null, $params = null) {
    require_once $smarty->_get_plugin_filepath('modifier','date_format');
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    $format = mbGetValue(@$params["format"],  CAppUI::conf("time"));
    return $propValue ? smarty_modifier_date_format($propValue, $format) : "";
  }
  
  function getSpecType() {
    return("time");
  }
  
  function checkProperty($object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
  
    if (!preg_match ("/^(\d{1,2}):(\d{1,2})(:(\d{1,2}))?$/", $propValue)) { 
    	if($object->$fieldName === 'current'|| $object->$fieldName ===  'now') {
        $object->$fieldName = mbTime();
        return null;
      }  
      return "Format d'heure invalide";
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
    return $this->getFormElementDateTime($object, $params, $value, $className, CAppUI::conf("time"));
  }
}

?>
<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("mbFieldSpec");

/**
 * Susceptible de gérer les dates de naissance non grégorienne 
 * au format pseudo ISO : YYYY-MM-DD mais avec potentiellement :
 *  MM > 12
 *  DD > 31
 */
class CBirthDateSpec extends CMbFieldSpec {
  function getSpecType() {
    return("birthdate");
  }
  
  function getDBSpec(){
    return "CHAR(10)";
  }
  
  function getValue($object, $smarty = null, $params = null) {
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    if (!$propValue || $propValue === "0000-00-00") {
      return "-";
    }
    return parent::getValue($object, $smarty, $params);
  }
  
  function checkProperty($object){
    $fieldName = $this->fieldName;
    $propValue = &$object->$fieldName;

    if (!preg_match ("/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/", $propValue)) {
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
  
  function getFormHtmlElement($object, $params, $value, $className){
    $maxLength = 10;
    CMbArray::defaultValue($params, "size", $maxLength);
    CMbArray::defaultValue($params, "maxlength", $maxLength);
    return $this->getFormElementText($object, $params, $value, $className);
  }
}

?>
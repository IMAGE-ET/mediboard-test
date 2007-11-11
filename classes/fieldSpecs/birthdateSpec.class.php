<?php /* $Id: dateSpec.class.php 2269 2007-07-16 15:19:31Z rhum1 $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Thomas Despoix
*/

require_once("./classes/mbFieldSpec.class.php");

/**
 * Susceptible de gérer les dates de naissance non grégorienne 
 * au format pseudo ISO : YYYY-MM-DD mais avec potentiellement :
 *  MM > 31
 *  DD > 31
 */
class CBirthDateSpec extends CMbFieldSpec {
  
  function getValue($object, $smarty, $params = null) {
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    if (!$propValue || $propValue == "0000-00-00") {
      return "-";
    }

    return mbDateToLocale($propValue);
  }
  
  function getSpecType() {
    return("birthdate");
  }
  
  function checkProperty(&$object){
    $fieldName = $this->fieldName;
    $propValue =& $object->$fieldName;
    
    $matches = array();
    if (!preg_match ("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/", $propValue, $matches)) {
      return "format de date invalide";
    }
    
    $propValue = format("%04s-%02s-%02s", $matches[1], $matches[2], $matches[3]);
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
    return "CHAR(10)";
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    $maxLength = 10;
    CMbArray::defaultValue($params, "size", $maxLength);
    CMbArray::defaultValue($params, "maxlength", $maxLength);
    return $this->getFormElementText($object, $params, $value, $className);
  }
}

?>
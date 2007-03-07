<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
*/

require_once("./classes/mbFieldSpec.class.php");

class CTextSpec extends CMbFieldSpec {
  
  function getValue($object, $smarty, $params = null) {
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    return nl2br($propValue);
  }
  
  function getSpecType() {
    return("text");
  }
  
  function checkProperty($object){
    return null;
  }
  
  function sample(&$object){
    parent::sample($object);
    $fieldName = $this->fieldName;
    $object->$fieldName = $this->randomString(CMbFieldSpec::$chars, 40);
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    return $this->getFormElementTextarea($object, $params, $value, $className);
  }
  
  function getDBSpec(){
    return "text";
  }
}

?>
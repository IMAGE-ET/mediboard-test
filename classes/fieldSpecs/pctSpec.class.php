<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
*/

require_once("./classes/mbFieldSpec.class.php");

class CPctSpec extends CMbFieldSpec {
  
  function getSpecType() {
    return("pct");
  }
  
  function checkProperty($object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    if (!preg_match ("/^([0-9]+)(\.[0-9]{0,2}){0,1}$/", $propValue)) {
      return "n'est pas un pourcentage (utilisez le . pour la virgule)";
    }
    return null;
  }
  
  function getDBSpec(){
    return "float";
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    return $this->getFormElementText($object, $params, $value, $className);
  }
  
  function sample(&$object) {
    parent::sample($object);
    $fieldName = $this->fieldName;
    $object->$fieldName = rand(0, 100);
  }
}

?>
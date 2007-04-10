<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
*/

require_once("./classes/mbFieldSpec.class.php");

class CEmailSpec extends CMbFieldSpec {
  
  function getSpecType() {
    return("email");
  }
  
  function checkProperty($object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    if (!preg_match("/^[-a-z0-9\._]+@[-a-z0-9\.]+\.[a-z]{2,4}$/i", $propValue)) {
      return "Le format de l'email n'est pas valide";
    }
    return null;
  }
  
  function getDBSpec(){
    return "varchar(50)";
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    return $this->getFormElementText($object, $params, $value, $className);
  }
  
  function sample(&$object) {
    parent::sample($object);
    $fieldName = $this->fieldName;
    $object->$fieldName = "noone@nowhere.com";
  }
}

?>
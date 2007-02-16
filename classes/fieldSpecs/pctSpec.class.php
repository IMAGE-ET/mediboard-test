<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
*/

require_once("./classes/mbFieldSpec.class.php");

class CPctSpec extends CMbFieldSpec {
  
  function checkProperty(&$object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    if (!preg_match ("/^([0-9]+)(\.[0-9]{0,2}){0,1}$/", $propValue)) {
      return "n'est pas un pourcentage (utilisez le . pour la virgule)";
    }
    return null;
  }
  
  function checkFieldType(){
    return "text";
  }
  
  function getDBSpec(){
    return "float";
  }
}

?>
<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
*/

require_once("./classes/mbFieldSpec.class.php");

class CRefSpec extends CMbFieldSpec {
  
  var $xor  = null;
  //var $nand = null;
  
  function getSpecType() {
    return("ref");
  }

  function checkProperty($object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    $propValue = $this->checkNumeric($propValue, false);
      
    if($propValue === null || $object->$fieldName === ""){
      return "N'est pas une référence (format non numérique)";
    }
    
    if ($propValue < 0) {
      return "N'est pas une référence (entier négatif)";
    }
    
    if($field = $this->xor){
      if($msg = $this->checkTargetPropValue($object, $field)){
        return $msg;
      }
      $targetPropValue = $object->$field; 
      if ($propValue==0 and $targetPropValue==0) {
        return "Merci de choisir soit '$propName', soit '$targetPropName'"; 
      }
      if ($propValue!=0 and $targetPropValue!=0) {
        return "Vous ne devez choisir qu'un seul de ces champs : '$propName', '$targetPropName'"; 
      }
    }
    
    //if($this->nand){
    //}
    return null;
  }
  
  function getDBSpec(){
    return "int(11) unsigned";
  }
}

?>
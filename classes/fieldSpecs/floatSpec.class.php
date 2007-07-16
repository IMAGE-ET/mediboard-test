<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
*/

require_once("./classes/mbFieldSpec.class.php");

class CFloatSpec extends CMbFieldSpec {
  
  var $min    = null;
  var $max    = null;
  var $pos    = null;
  var $minMax = null;
  
  function getSpecType() {
    return("float");
  }
  
  function checkProperty($object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    $propValue = $this->checkNumeric($propValue, false);
    if($propValue === null){
      return "n'est pas une valeur décimale (utilisez le . pour la virgule)";
    }
    
    // pos
    if($this->pos){
      if ($propValue <= 0) {
        return "Doit avoir une valeur positive";
      }
    }
    
    // min
    if($this->min){
      if(!$min = $this->checkNumeric($this->min, false)){
        trigger_error("Spécification de minimum numérique invalide (min = $this->min)", E_USER_WARNING);
        return "Erreur système";
      }
      if ($propValue < $min) {
        return "Doit avoir une valeur minimale de $min";
      }
    }
      
    // max
    if($this->max){
      $max = $this->checkNumeric($this->max, false);
      if($max === null){
        trigger_error("Spécification de maximum numérique invalide (max = $this->max)", E_USER_WARNING);
        return "Erreur système";
      }      
      if ($propValue > $max) {
        return "Doit avoir une valeur maximale de $max";
      }
    }
     
    // minMax
    if($this->minMax){
      $specFragments = explode("|", $this->minMax);
      $min= $this->checkNumeric(@$specFragments[0], false);
      $max= $this->checkNumeric(@$specFragments[1], false);
      if(count($specFragments) != 2 || $min === null || $max === null){
        trigger_error("Spécification de minimum maximum numérique invalide (minMax = $this->minMax)", E_USER_WARNING);
        return "Erreur système";
      }
      if($propValue>$max || $propValue<$min){
        return "N'est pas compris entre $min et $max";
      }
    }
    return null;
  }
  
  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $fieldName = $this->fieldName;
    $propValue =& $object->$fieldName;
    
    $propValue = $this->randomString(CMbFieldSpec::$nums, 2).".".$this->randomString(CMbFieldSpec::$nums, 2);
  }
  
  function getDBSpec(){
    $type_sql = "FLOAT";
    if($this->pos){
      $type_sql = "FLOAT UNSIGNED";
    }
    return $type_sql;
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    $size = 8;

    if (!array_key_exists("size", $params)) {
      $params["size"] = $size + 2;
    }

    if (!array_key_exists("maxlength", $params)) {
      $params["maxlength"] = $size;
    }

    return $this->getFormElementText($object, $params, $value, $className);
  }
}

?>
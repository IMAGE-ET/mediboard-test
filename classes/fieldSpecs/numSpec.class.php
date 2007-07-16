<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
*/

require_once("./classes/mbFieldSpec.class.php");

class CNumSpec extends CMbFieldSpec {
  
  function getSpecType() {
    return("num");
  }
  
  var $min       = null;
  var $max       = null;
  var $pos       = null;
  var $length    = null;
  var $minLength = null;
  var $maxLength = null;
  var $minMax    = null;
  
  function checkProperty($object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    $propValue = $this->checkNumeric($propValue, false);
    
    if($propValue === null){
      return "N'est pas une chaîne numérique";
    }

    // pos
    if($this->pos){
      if ($propValue <= 0) {
        return "Doit avoir une valeur positive";
      }
    }  

    // min
    if($this->min){
      if(!$min = $this->checkNumeric($this->min)){
        trigger_error("Spécification de minimum numérique invalide (min = $this->min)", E_USER_WARNING);
        return "Erreur système";
      }
      if ($propValue < $min) {
        return "Soit avoir une valeur minimale de $min";
      }
    }
    
    // max  
    if($this->max){
      $max = $this->checkNumeric($this->max);
      if($max === null){
        trigger_error("Spécification de maximum numérique invalide (max = $this->max)", E_USER_WARNING);
        return "Erreur système";
      }      
      if ($propValue > $max) {
        return "Soit avoir une valeur maximale de $max";
      }
    }
    
    // length  
    if($this->length){
      if(!$length = $this->checkLengthValue($this->length)){
        trigger_error("Spécification de longueur invalide (longueur = $this->length)", E_USER_WARNING);
        return "Erreur système";
      }
      if (strlen($propValue) != $length) {
        return "N'a pas la bonne longueur (longueur souhaité : $length)'";
      }
    }
    
    // minLength
    if($this->minLength){
      if(!$length = $this->checkLengthValue($this->minLength)){
        trigger_error("Spécification de longueur minimale invalide (longueur = $this->minLength)", E_USER_WARNING);
        return "Erreur système";
      }
      if (strlen($propValue) < $length) {
        return "N'a pas la bonne longueur (longueur minimale souhaitée : $length)'";
      }
    }
    
    // maxLength
    if($this->maxLength){
      if(!$length = $this->checkLengthValue($this->maxLength)){
        trigger_error("Spécification de longueur maximale invalide (longueur = $this->maxLength)", E_USER_WARNING);
        return "Erreur système";
      }
      if (strlen($propValue) > $length) {
        return "N'a pas la bonne longueur (longueur maximale souhaitée : $length)'";
      }
    }
    
    // minMax
    if($this->minMax){
      $specFragments = explode("|", $this->minMax);
      $min= $this->checkNumeric(@$specFragments[0]);
      $max= $this->checkNumeric(@$specFragments[1]);
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

  function sample(&$object){
    parent::sample($object);
    $fieldName = $this->fieldName;
    $propValue =& $object->$fieldName;
    
    if($this->length){
      $propValue = $this->randomString(CMbFieldSpec::$nums, $this->length);
      
    }elseif($this->minLength){
      if($this->_defaultLength < $this->minLength){
        $propValue = $this->randomString(CMbFieldSpec::$nums, $this->minLength);
      }else{
        $propValue = $this->randomString(CMbFieldSpec::$nums, $this->_defaultLength);
      }
      
    }elseif($this->maxLength){
      if($this->_defaultLength > $this->maxLength){
        $propValue = $this->randomString(CMbFieldSpec::$nums, $this->maxLength);
      }else{
        $propValue = $this->randomString(CMbFieldSpec::$nums, $this->_defaultLength);
      }
    }elseif($this->minMax || $this->max || $this->min){
      if($this->minMax){
        $specFragments = explode("|", $this->minMax);
        $min= $this->checkNumeric($specFragments[0]);
        $max= $this->checkNumeric($specFragments[1]);
      }else{
        $min = $this->min ? $this->min : 0;
        $max = $this->max ? $this->max : 999999;
      }
      $propValue = rand($min, $max);
    }else{
      $propValue = $this->randomString(CMbFieldSpec::$nums, $this->_defaultLength);
    }

  }
  
  function getDBSpec(){
    $type_sql   = "INT(11)";
    $valeur_max = null;
    
    if($this->minMax || $this->max){
      if($this->minMax){
        $specFragments = explode("|", $this->minMax);
        $valeur_max = $specFragments[1];
      }else{
        $valeur_max = $this->max;
      }
      $type_sql = "TINYINT(4)";
      if ($valeur_max > pow(2,8)) {
        $type_sql = "MEDIUMINT(9)";
      }
      if ($valeur_max > pow(2,16)) {
        $type_sql = "INT(11)";
      }
      if ($valeur_max > pow(2,32)) {
        $type_sql = "BIGINT(20)";
      }
    }elseif($this->pos){
      $type_sql = "INT(11) UNSIGNED";
    }
    
    return $type_sql;
  }

  function getFormHtmlElement($object, $params, $value, $className){
    $maxLength = mbGetValue($this->length, $this->maxLength, 255);
    
    if (!array_key_exists("size", $params)) {
      $params["size"] = min($maxLength, 20) + 2;
    }

    if (!array_key_exists("maxlength", $params)) {
      $params["maxlength"] = $maxLength;
    }

    return $this->getFormElementText($object, $params, $value, $className);
  }
}

?>
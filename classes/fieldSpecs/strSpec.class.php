<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
*/

require_once("./classes/mbFieldSpec.class.php");

class CStrSpec extends CMbFieldSpec {
  
  var $length    = null;
  var $minLength = null;
  var $maxLength = null;
  
  function getSpecType() {
    return("str");
  }
  
  function checkProperty($object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    // length
    if($this->length){
      if(!$length = $this->checkLengthValue($this->length)){
        trigger_error("Spécification de longueur invalide (longueur = $this->length)", E_USER_WARNING);
        return "Erreur système";
      } 
      if (strlen($propValue) != $length) {
        return "N'a pas la bonne longueur (longueur souhaitée : $length)'";
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
    
    return null;
  }
  
  function sample(&$object){
    parent::sample($object);
    $fieldName = $this->fieldName;
    $propValue =& $object->$fieldName;
    
    if($this->length){
      $propValue = $this->randomString(CMbFieldSpec::$chars, $this->length);
    
    }elseif($this->minLength){
      if($this->_defaultLength < $this->minLength){
        $propValue = $this->randomString(CMbFieldSpec::$chars, $this->minLength);
      }else{
        $propValue = $this->randomString(CMbFieldSpec::$chars, $this->_defaultLength);
      }
    
    }elseif($this->maxLength){
      if($this->_defaultLength > $this->maxLength){
        $propValue = $this->randomString(CMbFieldSpec::$chars, $this->maxLength);
      }else{
        $propValue = $this->randomString(CMbFieldSpec::$chars, $this->_defaultLength);
      }

    }else{
      $propValue = $this->randomString(CMbFieldSpec::$chars, $this->_defaultLength);
    }
  }
  
  function getDBSpec(){
    $type_sql = "VARCHAR(255)";
    
    if ($this->maxLength) {
      $type_sql = "VARCHAR($this->maxLength)";
    } 
    
    if ($this->length) {
      $type_sql = "CHAR($this->length)";
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
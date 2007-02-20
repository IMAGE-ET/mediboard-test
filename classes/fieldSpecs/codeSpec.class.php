<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
*/

require_once("./classes/mbFieldSpec.class.php");

class CCodeSpec extends CMbFieldSpec {
  
  var $ccam  = null;
  var $cim10 = null;
  var $adeli = null;
  var $insee = null;
  
  function getSpecType() {
    return("code");
  }
  
  function checkProperty(&$object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    // ccam
    if($this->ccam){
      if (!preg_match ("/^([a-z0-9]){0,7}$/i", $propValue)) {
        return "Code CCAM incorrect, doit contenir 4 lettres et trois chiffres";
      }
    
    // cim10
    }elseif($this->cim10){
      if (!preg_match ("/^([a-z0-9]){0,5}$/i", $propValue)) {
        return "Code CCAM incorrect, doit contenir 5 lettres maximum";
      }
      
    // adeli
    }elseif($this->adeli){
      if (!preg_match ("/^([0-9]){9}$/i", $propValue)) {
        return "Code Adeli incorrect, doit contenir exactement 9 chiffres";
      }
      
    // insee
    }elseif($this->insee){
      $matches = null;
      if (!preg_match ("/^([1-2][0-9]{2}[0-9]{2}[0-9]{2}[0-9]{3}[0-9]{3})([0-9]{2})$/i", $propValue, $matches)) {
        return "Matricule incorrect, doit contenir exactement 15 chiffres (commençant par 1 ou 2)";
      }
      $code = $matches[1];
      $cle  = $matches[2];
      
      // Use bcmod since standard modulus can't work on numbers exceedind the 2^32 limit
      if (function_exists("bcmod")) {
        if (97 - bcmod($code, 97) != $cle) {
          return "Matricule incorrect, la clé n'est pas valide";
        }
      }
      
    }else{
      return "Spécification de code invalide";
    }
    return null;
  }
  
  function checkFieldType(){
    return "text";
  }
  
  function getDBSpec(){
    $type_sql = null;
    
    if($this->ccam){
      $type_sql = "varchar(7)";
    }elseif($this->cim10){
      $type_sql = "varchar(5)";
    }elseif($this->adeli){
      $type_sql = "varchar(9)";
    }elseif($this->insee){
      $type_sql = "varchar(15)";
    }
    
    return $type_sql;
  }
}

?>
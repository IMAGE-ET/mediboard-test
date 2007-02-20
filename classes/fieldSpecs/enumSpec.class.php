<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
*/

require_once("./classes/mbFieldSpec.class.php");

class CEnumSpec extends CMbFieldSpec {
  
  var $list = null;
  
  function getSpecType() {
    return("enum");
  }
  
  function checkValues(){
    parent::checkValues();
    if(!$this->list){
      trigger_error("Spécification 'list' manquante pour le champ '".$this->fieldName."' de la classe '".$this->className."'", E_USER_WARNING);
    }
  }
  
  function checkProperty(&$object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    $specFragments = explode("|", $this->list);
    if (!in_array($propValue, $specFragments)) {
      return "N'a pas une valeur possible";
    }
    return null;
  }
  
  function getConfidential(&$object){
    $fieldName = $this->fieldName;
    $propValue =& $object->$fieldName;
    $specFragments = explode("|", $this->list);
    
    $propValue = $this->randomString($specFragments, 1);
  }
  
  function checkFieldType(){
    return "enum";
  }
  
  function getDBSpec(){
    $aSpecFragments = explode("|", $this->list);
    $type_sql = "enum('".implode("','", $aSpecFragments)."')";
    return $type_sql;
  }
}

?>
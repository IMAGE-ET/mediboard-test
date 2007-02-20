<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
*/

class CMbFieldSpec {
  //
  var $object         = null;
  var $spec           = null;
  var $fieldName      = null;
  
  var $notNull        = null;
  var $confidential   = null;
  var $moreThan       = null;
  var $moreEquals     = null;
  var $sameAs         = null;
  
  var $msgError       = null;
  
  var $_defaultLength = null;
  var $_chars         = null;
  var $_nums          = null;
  var $_days          = null;
  var $_monthes       = null;
  var $_hours         = null;
  var $_mins          = null; 
  
  function CMbFieldSpec(&$className, &$field, $propSpec, $aProperties) {
    $this->className =& $className;
    $this->fieldName =& $field;
    $this->spec      =& $propSpec;
    
    $aObjProperties = get_object_vars($this);

    foreach($aProperties as $k => $v) {
      if(array_key_exists($k ,$aObjProperties)){
        $this->$k = $aProperties[$k];
      }else{
        trigger_error("La spécification '$k' trouvée dans '".$this->className."' est inexistante dans la classe '".get_class($this)."'", E_USER_WARNING);
      }
    }
    
    
    static $_chars   = null;
    static $_nums    = null;
    static $_days    = null;
    static $_monthes = null;
    static $_hours   = null;
    static $_mins    = null;
    if(!$_chars){
      $_chars = array("a","b","c","d","e","f","g","h","i","j","k","l","m",
                     "n","o","p","q","r","s","t","u","v","w","x","y","z");
    }
    if(!$_nums){
      $_nums = array("0","1","2","3","4","5","6","7","8","9");
    }
    if(!$_days){
      $_days = array();
      for($i = 1; $i < 29; $i++) {
        if($i < 10)
          $_days[] = "0".$i;
        else
          $_days[] = $i;
      }
    }
    if(!$_monthes){
      $_monthes = array("01","02","03","04","05","06","07","08","09", "10", "11", "12");
    }
    if(!$_hours){
      $_hours = array();
      for($i = 9; $i < 18; $i++) {
        if($i < 10)
          $_hours[] = "0".$i;
        else
          $_hours[] = $i;
      }
    }
    if(!$_mins){
      $_mins = array();
      for($i = 0; $i < 60; $i++) {
        if($i < 10)
          $_mins[] = "0".$i;
        else
          $_mins[] = $i;
      }
    }
    $this->_defaultLength = 6;
    $this->_chars   =& $_chars;
    $this->_nums    =& $_nums;
    $this->_days    =& $_days;
    $this->_monthes =& $_monthes;
    $this->_hours   =& $_hours;
    $this->_mins    =& $_mins;
    
    $this->checkValues();
  }
  
  function getValue(&$object, $smarty, $params = null) {
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    return $propValue;
  }
  
  function getSpecType() {
    return("mbField");
  }
  
  function checkParams(&$object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    // NotNull
    if($this->notNull && ($propValue === null || $propValue === "")){
      return "Ne pas peut pas avoir une valeur nulle";
    }
    
    // moreThan
    if($field = $this->moreThan){
      if($msg = $this->checkTargetPropValue($object, $field)){
        return $msg;
      }
      $targetPropValue = $object->$field;
      if ($propValue <= $targetPropValue) {
        return "'$propValue' n'est pas strictement supérieur à '$targetPropValue'";
      }
    }
    
    // moreEquals
    if($field = $this->moreEquals){
      if($msg = $this->checkTargetPropValue($object, $field)){
        return $msg;
      }
      $targetPropValue = $object->$field;      
      if ($propValue < $targetPropValue) {
        return "'$propValue' n'est pas supérieur ou égal à '$targetPropValue'";
      }
    }
    
    // sameAs
    if($field = $this->sameAs){
      if($msg = $this->checkTargetPropValue($object, $field)){
        return $msg;
      }
      $targetPropValue = $object->$field;  
      if ($propValue !== $targetPropValue) {
        return "'Doit être identique à '$targetPropName'";
      }
    }
    
    return null;
  }
  
  function checkTargetPropValue($object, $field){
    $aObjProperties = get_object_vars($object);
    if(!$field || $field === true || !is_scalar($field) || !array_key_exists($field ,$aObjProperties)){
      trigger_error("Elément cible '$field' invalide ou inexistant dans la classe '".get_class($this)."'", E_USER_WARNING);
      return "Erreur système";
    }
    return null;
  }
  
  function checkPropertyValue(&$object){
    $fieldName = $this->fieldName;
    $propValue =& $object->$fieldName;
    
    if($this->msgError = $this->checkParams($object)){
      return $this->msgError;
    }
    
    if ($propValue === null || $propValue === "") {
      return null;
    }
    
    if($this->msgError = $this->checkProperty($object)){
      return $this->msgError;
    }
    
    return null;
  }
  
  function randomString($array, $length) {
    $key = "";
    $count = count($array) - 1;
    srand((double)microtime()*1000000);
    for($i = 0; $i < $length; $i++) $key .= $array[rand(0, $count)];
    return($key);
  }

  function checkNumeric($value, $returnInteger = true){
    if (!is_numeric($value)) {
      return null;
    }
    if($returnInteger){
      $value = intval($value);
    }
    return $value;
  }
  
  function checkLengthValue($length){
    if(!$length = $this->checkNumeric($length)){
      return null;
    }
    if ($length < 1 or $length > 255) {
      return null;
    }
    return $length;
  }
  
  function checkProperty(){
  }
  function checkConfidential(&$object){
    if(!$this->confidential){
      return null;
    }

    $this->getConfidential($object);
  }
  function getConfidential(&$object){
  }
  function getDBSpec(){
    return null;
  }
  function checkFieldType(){
    return null;
  }
  function checkValues(){
  }
}

?>
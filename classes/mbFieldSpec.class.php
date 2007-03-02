<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
*/

class CMbFieldSpec {
  var $object         = null;
  var $spec           = null;
  var $fieldName      = null;
  var $default        = null;
  
  var $notNull        = null;
  var $confidential   = null;
  var $moreThan       = null;
  var $moreEquals     = null;
  var $sameAs         = null;
  
  var $msgError       = null;
  
  static $chars  = array();
  static $nums   = array();
  static $months = array();
  static $days   = array();
  static $hours  = array();
  static $mins   = array();
  
  var $_defaultLength = null;
  
  function CMbFieldSpec(&$className, &$field, $prop = null, $aProperties = array()) {
    $this->className =& $className;
    $this->fieldName =& $field;
    $this->prop      =& $prop;
    
    $aObjProperties = get_object_vars($this);

    foreach($aProperties as $k => $v) {
      if(array_key_exists($k ,$aObjProperties)){
        $this->$k = $aProperties[$k];
      }else{
        trigger_error("La spécification '$k' trouvée dans '".$this->className."' est inexistante dans la classe '".get_class($this)."'", E_USER_WARNING);
      }
    }
    
    $this->_defaultLength = 6;
    
    $this->checkValues();
  }
  
  function getValue($object, $smarty, $params = null) {
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    return $propValue;
  }
  
  function checkParams($object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    // NotNull
    if($this->notNull && ($propValue === null || $propValue === "")){
      return "Ne pas peut pas avoir une valeur nulle";
    }
    if($propValue === null || $propValue === ""){
      return null;
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
        return "Doit être identique à '$targetPropName'";
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
  
  function checkPropertyValue($object){
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
  
  function checkConfidential(&$object){
    if(!$this->confidential){
      return null;
    }

    $this->getConfidential($object);
  }
  
  function getFormElement($object, $params){
    $hidden    = CMbArray::extract($params, "hidden");
    $className = CMbArray::extract($params, "class");
    $value     = $object->{$this->fieldName};
    if($hidden){
      return $this->getFormHiddenElement($object, $params, $value, $className);
    }
    return $this->getFormHtmlElement($object, $params, $value, $className);
  }
  
  function getLabelElement($object, $params){
    global $AppUI;
    
    $defaultFor = CMbArray::extract($params, "defaultFor");
    if($defaultFor){
      $selected = $this;
    }else{
      $selected = $this->getLabelForElement($object, $params);
    }
    $extra  = CMbArray::makeXmlAttributes($params);
    
    $sHtml  = "<label for=\"$selected\" title=\"".$AppUI->_($object->_class_name."-".$this->fieldName."-desc")."\" $extra>";
    $sHtml .= $AppUI->_($object->_class_name."-".$this->fieldName);
    $sHtml .= "</label>";
    
    return $sHtml;
  }
  
  function getLabelForElement($object, $params){
    return $this->fieldName;
  }
  
  function getFormHiddenElement($object, $params, $value, $className){
    $field = $this->fieldName;
    $extra = CMbArray::makeXmlAttributes($params);
    $sHtml = "<input type=\"hidden\" name=\"".htmlspecialchars($field)."\" value=\"".htmlspecialchars($value)."\"";
    if($this->prop){
      $sHtml .= " class=\"".htmlspecialchars($this->prop)."\"";
    }
    $sHtml  .= " $extra/>";
    
    return $sHtml;
  }
  
  function getFormElementText($object, &$params, $value, $className){
    $field        = htmlspecialchars($this->fieldName);
    $extra        = CMbArray::makeXmlAttributes($params);
    $sHtml        = "<input type=\"text\" name=\"$field\" value=\"".htmlspecialchars($value)."\"";    
    $sHtml       .= " class=\"".htmlspecialchars(trim($className." ".$this->prop))."\" $extra/>";
    return $sHtml;
  }
  
  function getFormElementTextarea($object, &$params, $value, $className){
    $field        = htmlspecialchars($this->fieldName);
    $extra        = CMbArray::makeXmlAttributes($params);
    $sHtml        = "<textarea name=\"$field\" class=\"".htmlspecialchars(trim($className." ".$this->prop))."\" $extra>".htmlspecialchars($value)."</textarea>";
    return $sHtml;
  }
  
  function getFormHtmlElement($object, &$params, $value, $className){
    return $this->getFormElementText($object, $params, $value, $className);
    //trigger_error("mb_field: Specification '".$this->prop."' non prise en charge", E_USER_NOTICE);
  }
  
  function getSpecType() {
    return("mbField");
  }
  
  function checkProperty(){
  }
  function getConfidential(&$object){
  }
  function getDBSpec(){
    return null;
  }
  function checkValues(){
  }
}

CMbFieldSpec::$chars  = range("a","z");
CMbFieldSpec::$nums   = range(0, 9);
CMbFieldSpec::$months = range(1, 12);
CMbFieldSpec::$days   = range(1, 29);
CMbFieldSpec::$hours  = range(9, 19);
CMbFieldSpec::$mins   = range(0, 60, 10);

?>
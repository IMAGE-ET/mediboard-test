<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
 *  @author Fabien Ménager
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
  var $notContaining  = null;
  var $notNear        = null;
  var $alphaAndNum    = null;
  var $xor            = null;

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
      if (array_key_exists($k ,$aObjProperties)){
        $this->$k = $aProperties[$k];
      } else {
        trigger_error("La spécification '$k' trouvée dans '{$this->className}' est inexistante dans la classe '".get_class($this)."'", E_USER_WARNING);
      }
    }

    $this->_defaultLength = 6;

    $this->checkValues();
  }

  function getValue($object, $smarty, $params = null) {
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    return htmlspecialchars($propValue);
  }

  function checkParams($object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;

    // NotNull
    if($this->notNull && !$this->default && ($propValue === null || $propValue === "")){
      return "Ne pas peut pas avoir une valeur nulle";
    }

    // xor
    if($this->xor){
      $fields = explode("|", $this->xor);
      $otherfields = "";
      foreach($fields as $field) {
        if($msg = $this->checkTargetPropValue($object, $field)){
          return $msg;
        }
        $targetPropValue[$field] = $object->$field;
        $otherfields .= ", '$field'";
      }
      $noValue  = !$propValue;
      $nbValues = ($propValue !== "");
      foreach($targetPropValue as $key => $value) {
        if ($value === null) {
          trigger_error("La valeur du champ '$key' impliqué dans un xor dans la classe '$this->className' n'est pas présente dans le formulaire", E_USER_ERROR);
          die();
        }
        $noValue  &= !$value;
        $nbValues += ($value !== "");
      }
      if ($noValue) {
        return "Merci de choisir un de ces champs : '$fieldName', '$otherfields'";
      }
      if ($nbValues > 1) {
        return "Vous ne devez choisir qu'un seul de ces champs : '$fieldName''$otherfields'";
      }
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
        return "Doit être identique à '$field->fieldName'";
      }
    }

    // notContaining
    if($field = $this->notContaining){
      if($msg = $this->checkTargetPropValue($object, $field)){
        return $msg;
      }
      $targetPropValue = $object->$field;
      if (stristr($propValue, $targetPropValue)) {
        return "Ne doit pas contenir '$field->fieldName'";
      }
    }
    
      // notNear
    if($field = $this->notNear){
      if($msg = $this->checkTargetPropValue($object, $field)){
        return $msg;
      }
      $targetPropValue = $object->$field;  
      if (levenshtein($propValue, $targetPropValue) < 2) {
        return "Le mot de passe ressemble trop à '$field->fieldName'";
      }
    }

    // alphaAndNum
    if($field = $this->alphaAndNum){
      if (!preg_match("/[a-z]/", $propValue) || !preg_match("/\d+/", $propValue)) {
        return 'Doit contenir au moins un chiffre ET une lettre';
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
    for($i = 0; $i < $length; $i++)  {
      $key .= $array[rand(0, $count)];
      if ($i % 20 == 19) {
        $key .= " ";
      }
    }
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
    $field = $this->fieldName;
    if(!$this->confidential || $object->$field === null){
      return null;
    }

    $this->sample($object);
  }

  function getFormElement($object, $params){
    $hidden    = CMbArray::extract($params, "hidden");
    $className = CMbArray::extract($params, "class");
    if($object->_locked) {
      $params["readonly"] = "readonly";
    }
    $value     = $object->{$this->fieldName};
    if ($hidden) {
      return $this->getFormHiddenElement($object, $params, $value, $className);
    }
    return $this->getFormHtmlElement($object, $params, $value, $className);
  }

  /**
   * Produit le code HTML pour une label de champ de formulaire
   * pour le champ de la spécification
   *
   * @param CMbObject $object Objet concerné
   * @param array $params Extra parameters
   *   - defaultFor : name of a radio option to associate label with
   * @return string Rendered HTML
   */
  function getLabelElement($object, $params) {
    $defaultFor = CMbArray::extract($params, "defaultFor");
    $forName = $defaultFor ? $defaultFor : $this->getLabelForElement($object, $params);

    $extra  = CMbArray::makeXmlAttributes($params);

    $sHtml  = "<label for=\"$forName\" title=\"".CAppUI::tr($object->_class_name."-".$this->fieldName."-desc")."\" $extra>";
    $sHtml .= CAppUI::tr($object->_class_name."-".$this->fieldName);
    $sHtml .= "</label>";

    return $sHtml;
  }

  /**
   * Produit le code HTML pour un titre de colonne
   * pour le champ de la spécification
   *
   * @param CMbObject $object Objet concerné
   * @param array $params Extra parameters
   * @return string Rendered HTML
   */
  function getTitleElement($object, $params) {
    $title = CAppUI::tr($object->_class_name."-".$this->fieldName."-court");
    $desc  = CAppUI::tr($object->_class_name."-".$this->fieldName."-desc");

    $sHtml  = "<label title=\"$desc\" >";
    $sHtml .= $title;
    $sHtml .= "</label>";

    return $sHtml;
  }

  function getLabelForElement($object, &$params){
    return $this->fieldName;
  }

  function getFormHiddenElement($object, $params, $value, $className) {
    $field = $this->fieldName;
    $extra = CMbArray::makeXmlAttributes($params);
    $sHtml = "<input type=\"hidden\" name=\"".htmlspecialchars($field)."\" value=\"".htmlspecialchars($value)."\"";
    if($this->prop){
      $sHtml .= " class=\"".htmlspecialchars($this->prop)."\"";
    }
    $sHtml  .= " $extra/>";

    return $sHtml;
  }

  function getFormElementText($object, $params, $value, $className){
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

  function getFormElementDateTime($object, &$params, $value, $className, $format = "%d/%m/%Y %H:%M") {
    if ($object->_locked) {
      $params["readonly"] = "readonly";
    }

    $class = htmlspecialchars(trim("$className $this->prop"));
    $field = htmlspecialchars($this->fieldName);
    $date  = $value ? mbTranformTime(null, $value, $format) : "";
    
    $form     = CMbArray::extract($params, "form");
    $register = CMbArray::extract($params, "register");
    
    
    $id    = $form.'_'.$field;
    $extra = CMbArray::makeXmlAttributes($params);
    $aHtml[] = '<div class="'.$this->getSpecType().'" id="'.$id.'_da">'.$date.'</div>';
    $aHtml[] = '<input type="hidden" name="'.$field.'" class="'.$class.'" value="'.$value.'" '.$extra.' />';
    $aHtml[] = '<img id="'.$id.'_trigger" src="./images/icons/calendar.gif" alt="Choisir la date"/>';

    if (!$this->notNull) {
      $aHtml[] = '<button class="cancel notext" type="button" onclick="Form.Element.setValue('.$field.', new String); $(\''.$id.'_da\').innerHTML = new String;">'.CAppUI::tr("Delete").'</button>';
    }
    
    if ($register) {
      $time = $this instanceof CDateTimeSpec ? "true" : "false";
      $aHtml[] = '<script type="text/javascript">Main.add(function() { regFieldCalendar("'.$form.'", "'.$field.'", '.$time.'); } ); </script>';
		}
    
    return join("\n", $aHtml);
  }

  function getFormHtmlElement($object, $params, $value, $className){
    return $this->getFormElementText($object, $params, $value, $className);
  }

  function getSpecType() {
    return("mbField");
  }

  /**
   * Check whether property value bound to objects is compliant to the specification
   * @param $object object bound to property
   * @return string Store-like message
   */
  function checkProperty($object) {
    return;
  }

  // Return a sample value.
  //If consistent, the random value stay the same for a given initial value
  function sample(&$object, $consistent = true){
    $fieldName = $this->fieldName;
    $propValue =& $object->$fieldName;
    if($consistent) {
      srand(crc32($propValue));
    }
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
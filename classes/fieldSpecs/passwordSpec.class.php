<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("mbFieldSpec");

class CPasswordSpec extends CMbFieldSpec {
  
  function getSpecType() {
    return('password');
  }
  
  var $minLength     = null;
  var $notContaining = null;
  var $notNear       = null;
  var $alphaAndNum   = null;
  
  function getDBSpec(){
    return "VARCHAR(50)";
  }
  
  
  // TODO: Factoriser les check
  function checkProperty($object) {
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;

    // minLength
    if($this->minLength){
      if(!$length = $this->checkLengthValue($this->minLength)){
        trigger_error("Spécification de longueur minimale invalide (longueur = $this->minLength)", E_USER_WARNING);
        return "Erreur système";
      }     
      if (strlen($propValue) < $length) {
        return "Le mot de passe n'a pas la bonne longueur '$propValue' (longueur minimale souhaitée : $length)'";
      }
    }
    
    // notContaining
    if($field = $this->notContaining){
      if($msg = $this->checkTargetPropValue($object, $field)){
        return $msg;
      }
      $targetPropValue = $object->$field;  
      if (stristr($propValue, $targetPropValue)) {
        return "Le mot de passe ne doit pas contenir '$field->fieldName'";
      }
    }
    
    // notNear
    if($field = $this->notNear){
      if($msg = $this->checkTargetPropValue($object, $field)){
        return $msg;
      }
      $targetPropValue = $object->$field;  
      if (levenshtein($propValue, $targetPropValue) < 3) {
        return "Le mot de passe ressemble trop à '$field->fieldName'";
      }
    }
    
    // alphaAndNum
    if($field = $this->alphaAndNum){
      if (!preg_match("/[A-z]/", $propValue) || !preg_match("/\d+/", $propValue)) {
        return 'Le mot de passe doit contenir au moins un chiffre ET une lettre';
      }
    }
    return null;
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    $field        = htmlspecialchars($this->fieldName);
    $extra        = CMbArray::makeXmlAttributes($params);
    $sHtml        = '<input type="password" name="'.$field.'"';    
    $sHtml       .= ' class="'.htmlspecialchars(trim($className.' '.$this->prop)).'" '.$extra.' />';
    $sHtml       .= '<span id="'.$field.'_message"></span>';
    return $sHtml;
  }
  
  function sample(&$object, $consistent = true) {
    parent::sample($object, $consistent);
    $fieldName = $this->fieldName;
    $object->$fieldName = 'mgF61Ty';
  }
}

?>
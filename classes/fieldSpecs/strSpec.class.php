<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("mbFieldSpec");

class CStrSpec extends CMbFieldSpec {
  var $length    = null;
  var $minLength = null;
  var $maxLength = null;
  var $protected = null;
  var $class = null;
  
  function getSpecType() {
    return "str";
  }
  
  function getDBSpec(){
    if ($this->maxLength) {
      return "VARCHAR($this->maxLength)";
    } 
    
    if ($this->length) {
      return "CHAR($this->length)";
    }
		
    if ($this->class) {
      return "VARCHAR (80)";
    }
		
    return "VARCHAR(255)";
  }
  
  function getOptions(){
    return parent::getOptions() + array(
      'length'    => 'num',
      'minLength' => 'num',
      'maxLength' => 'num',
      'protected' => 'bool',
      'class'     => 'class',
    );
  }
  
  function getValue($object, $smarty = null, $params = array()) {
    $value = $object->{$this->fieldName};
    if ($this->class) {
      return htmlspecialchars(CAppUI::tr($value));
    }

    return $value;
  }

  function checkProperty($object){
    $propValue = $object->{$this->fieldName};
    
    // length
    if($this->length){
      if(!$length = $this->checkLengthValue($this->length)){
        trigger_error("Spécification de longueur invalide (longueur = $this->length)", E_USER_WARNING);
        return "Erreur système";
      } 
      if (strlen($propValue) != $length) {
        return "N'a pas la bonne longueur '$propValue' (longueur souhaitée : $length)'";
      }
    }
    
    // minLength
    if($this->minLength){
      if(!$length = $this->checkLengthValue($this->minLength)){
        trigger_error("Spécification de longueur minimale invalide (longueur = $this->minLength)", E_USER_WARNING);
        return "Erreur système";
      }     
      if (strlen($propValue) < $length) {
        return "N'a pas la bonne longueur '$propValue' (longueur minimale souhaitée : $length)'";
      }
    }
    
    // maxLength
    if($this->maxLength){
      if(!$length = $this->checkLengthValue($this->maxLength)){
        trigger_error("Spécification de longueur maximale invalide (longueur = $this->maxLength)", E_USER_WARNING);
        return "Erreur système";
      }
      if (strlen($propValue) > $length) {
        return "N'a pas la bonne longueur '$propValue' (longueur maximale souhaitée : $length)'";
      }
    }
		
    if ($this->class) {
      $object = @new $propValue;
      if (!$object) {
        return "La classe '$propValue' n'existe pas";
      }
    }
  }
  
  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $propValue =& $object->{$this->fieldName};
    
    if($this->length) {
      $propValue = self::randomString(CMbFieldSpec::$chars, $this->length);
    }
    elseif($this->minLength) {
      if($this->_defaultLength < $this->minLength)
        $propValue = self::randomString(CMbFieldSpec::$chars, $this->minLength);
      else
        $propValue = self::randomString(CMbFieldSpec::$chars, $this->_defaultLength);
    }
    elseif($this->maxLength) {
      if($this->_defaultLength > $this->maxLength)
        $propValue = self::randomString(CMbFieldSpec::$chars, $this->maxLength);
      else
        $propValue = self::randomString(CMbFieldSpec::$chars, $this->_defaultLength);
    }
    else {
      $propValue = self::randomString(CMbFieldSpec::$chars, $this->_defaultLength);
    }
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    $maxLength = CValue::first($this->length, $this->maxLength, 255);
    CMbArray::defaultValue($params, "size", min($maxLength, 25));
    CMbArray::defaultValue($params, "maxlength", $maxLength);
    return $this->getFormElementText($object, $params, $value, $className);
  }
}

?>
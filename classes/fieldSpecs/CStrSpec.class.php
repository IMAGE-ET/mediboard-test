<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

class CStrSpec extends CMbFieldSpec {
  var $length    = null;
  var $minLength = null;
  var $maxLength = null;
  var $protected = null;
  var $class     = null;
  var $delimiter = null;
  var $canonical = null;
  
  function getSpecType() {
    return "str";
  }
  
  function getDBSpec(){
    if ($this->maxLength) {
      return "VARCHAR ($this->maxLength)";
    } 
    
    if ($this->length) {
      return "CHAR ($this->length)";
    }
    
    if ($this->class) {
      return "VARCHAR (80)";
    }
    
    return "VARCHAR (255)";
  }
  
  function getOptions(){
    return array(
      'length'    => 'num',
      'minLength' => 'num',
      'maxLength' => 'num',
      'protected' => 'bool',
      'class'     => 'class',
      'delimiter' => 'num',
      'canonical' => 'bool',
    ) + parent::getOptions();
  }
  
  function getValue($object, $smarty = null, $params = array()) {
    if ($this->class) {
      return CMbString::htmlSpecialChars(CAppUI::tr($object->{$this->fieldName}));
    }
    
    return parent::getValue($object, $smarty, $params);
  }

  function checkProperty($object){
    $propValue = $object->{$this->fieldName};
    
    // length
    if ($this->length) {
      if (!$length = $this->checkLengthValue($this->length)) {
        trigger_error("Spécification de longueur invalide (longueur = $this->length)", E_USER_WARNING);
        return "Erreur système";
      } 
      if (strlen($propValue) != $length) {
        return "N'a pas la bonne longueur '$propValue' (longueur souhaitée : $length)'";
      }
    }
    
    // minLength
    if ($this->minLength) {
      if (!$length = $this->checkLengthValue($this->minLength)) {
        trigger_error("Spécification de longueur minimale invalide (longueur = $this->minLength)", E_USER_WARNING);
        return "Erreur système";
      }     
      if (strlen($propValue) < $length) {
        return "N'a pas la bonne longueur '$propValue' (longueur minimale souhaitée : $length)'";
      }
    }
    
    // maxLength
    if ($this->maxLength) {
      if (!$length = $this->checkLengthValue($this->maxLength)) {
        trigger_error("Spécification de longueur maximale invalide (longueur = $this->maxLength)", E_USER_WARNING);
        return "Erreur système";
      }
      if (strlen($propValue) > $length) {
        return "N'a pas la bonne longueur '$propValue' (longueur maximale souhaitée : $length)'";
      }
    }
    
    // delimiter
    if ($this->delimiter) {
      $delim = chr(intval($this->delimiter));
      $values = explode($delim, $propValue);
      
      if (array_search("", $values, true) !== false) {
        return "Contient des valeurs vides '$propValue'";
      }
    }
    
    // canonical
    if ($this->canonical) {
      if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $propValue)) {
        return "Ne doit contenir que des chiffres et des lettres non-accentuées (pas d'espaces)";
      }
    }
    
    // class
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
    
    if ($this->length) {
      $propValue = self::randomString(CMbFieldSpec::$chars, $this->length);
    }
    elseif ($this->minLength) {
      if ($this->_defaultLength < $this->minLength) {
        $propValue = self::randomString(CMbFieldSpec::$chars, $this->minLength);
      }
      else {
        $propValue = self::randomString(CMbFieldSpec::$chars, $this->_defaultLength);
      }
    }
    elseif ($this->maxLength) {
      if ($this->_defaultLength > $this->maxLength) {
        $propValue = self::randomString(CMbFieldSpec::$chars, $this->maxLength);
      }
      else {
        $propValue = self::randomString(CMbFieldSpec::$chars, $this->_defaultLength);
      }
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

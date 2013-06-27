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

/**
 * Integer value
 */
class CNumSpec extends CMbFieldSpec {
  public $min;
  public $max;
  public $pos;
  public $length;
  public $minLength;
  public $maxLength;
  public $byteUnit;

  /**
   * @see parent::getSpecType()
   */
  function getSpecType() {
    return "num";
  }

  /**
   * @see parent::getDBSpec()
   */
  function getDBSpec(){
    $type_sql = "INT(11)";
    
    if ($this->max !== null) {
      $max = $this->max;
      $type_sql = "TINYINT(4)";
      
      if ($max > pow(2, 8)) {
        $type_sql = "MEDIUMINT(9)";
      }
      
      if ($max > pow(2, 16)) {
        $type_sql = "INT(11)";
      }
      
      if ($max > pow(2, 32)) {
        $type_sql = "BIGINT(20)";
      }
    }
    
    if ($this->pos || ($this->min !== null && $this->min >= 0)) {
      $type_sql .= ' UNSIGNED';
    }

    return $type_sql;
  }

  /**
   * @see parent::getOptions()
   */
  function getOptions(){
    return array(
      'min' => 'num',
      'max' => 'num',
      'pos' => 'bool',
      'length' => 'num',
      'minLength' => 'num',
      'maxLength' => 'num',
      'byteUnit' => 'str',
    ) + parent::getOptions();
  }

  /**
   * @see parent::getValue()
   */
  function getValue($object, $smarty = null, $params = array()) {
    if (!isset($this->byteUnit)) {
      return parent::getValue($object, $smarty, $params);
    }
    
    $propValue = $object->{$this->fieldName};
    $ratio = 1;
    
    switch ($this->byteUnit) {
      case "GB": $ratio *= 1024;
      case "MB": $ratio *= 1024;
      case "KB": $ratio *= 1024;
      case "B":
      default:
    }
    
    return CMbString::toDecaBinary($propValue * $ratio);
  }

  /**
   * @see parent::checkProperty()
   */
  function checkProperty($object){
    $propValue = CMbFieldSpec::checkNumeric($object->{$this->fieldName});
    
    if ($propValue === null) {
      return "N'est pas une chaîne numérique";
    }

    // pos
    if ($this->pos && $propValue <= 0) {
      return "Doit avoir une valeur positive";
    }  

    // min
    if ($this->min) {
      if (!$min = CMbFieldSpec::checkNumeric($this->min)) {
        trigger_error("Spécification de minimum numérique invalide (min = $this->min)", E_USER_WARNING);
        return "Erreur système";
      }
      if ($propValue < $min) {
        return "Doit avoir une valeur minimale de $min";
      }
    }
    
    // max  
    if ($this->max) {
      $max = CMbFieldSpec::checkNumeric($this->max);
      if ($max === null) {
        trigger_error("Spécification de maximum numérique invalide (max = $this->max)", E_USER_WARNING);
        return "Erreur système";
      }      
      if ($propValue > $max) {
        return "Doit avoir une valeur maximale de $max";
      }
    }
    
    // length  
    if ($this->length) {
      if (!$length = $this->checkLengthValue($this->length)) {
        trigger_error("Spécification de longueur invalide (longueur = $this->length)", E_USER_WARNING);
        return "Erreur système";
      }
      if (strlen($propValue) != $length) {
        return "N'a pas la bonne longueur (longueur souhaité : $length)'";
      }
    }
    
    // minLength
    if ($this->minLength) {
      if (!$length = $this->checkLengthValue($this->minLength)) {
        trigger_error("Spécification de longueur minimale invalide (longueur = $this->minLength)", E_USER_WARNING);
        return "Erreur système";
      }
      if (strlen($propValue) < $length) {
        return "N'a pas la bonne longueur (longueur minimale souhaitée : $length)'";
      }
    }
    
    // maxLength
    if ($this->maxLength) {
      if (!$length = $this->checkLengthValue($this->maxLength)) {
        trigger_error("Spécification de longueur maximale invalide (longueur = $this->maxLength)", E_USER_WARNING);
        return "Erreur système";
      }
      if (strlen($propValue) > $length) {
        return "N'a pas la bonne longueur (longueur maximale souhaitée : $length)'";
      }
    }
  }

  /**
   * @see parent::sample()
   */
  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $propValue =& $object->{$this->fieldName};
    
    if ($this->length) {
      $propValue = self::randomString(CMbFieldSpec::$nums, $this->length);
    }
    elseif ($this->minLength) {
      $propValue = self::randomString(CMbFieldSpec::$nums, max($this->minLength, $this->_defaultLength));
    }
    elseif ($this->maxLength) {
      $propValue = self::randomString(CMbFieldSpec::$nums, min($this->maxLength, $this->_defaultLength));
    }
    elseif ($this->max || $this->min) {
      $min = $this->min !== null ? $this->min : 0;
      $max = $this->max !== null ? $this->max : 999999;
      $propValue = rand($min, $max);
    }
    else {
      $propValue = self::randomString(CMbFieldSpec::$nums, $this->_defaultLength);
    }
  }

  /**
   * @see parent::getFormHtmlElement()
   */
  function getFormHtmlElement($object, $params, $value, $className) {
    $form       = CMbArray::extract($params, "form");
    $increment  = CMbArray::extract($params, "increment");
    $showPlus   = CMbArray::extract($params, "showPlus");
    $deferEvent = CMbArray::extract($params, "deferEvent");
    $bigButtons = CMbArray::extract($params, "bigButtons");

    $field      = CMbString::htmlSpecialChars($this->fieldName);
    
    $min = CMbArray::extract($params, "min");
    if ($min === null) {
      $min = CMbFieldSpec::checkNumeric($this->min);
    }
    
    $max = CMbArray::extract($params, "max");
    if ($max === null) {
      $max = CMbFieldSpec::checkNumeric($this->max);
    }
    
    $new_value = CMbArray::extract($params, "value");
    if ($new_value !== null) {
      $value = $new_value;
    }
    
    $step = CMbFieldSpec::checkNumeric(CMbArray::extract($params, "step"));
    
    CMbArray::defaultValue($params, "size", 4);

    if ($form && $increment) {
      $sHtml = $this->getFormElementText($object, $params, (($value>=0 && $showPlus)?'+':'').(($value==0&&$showPlus)?'0':$value), $className, "number");
      $sHtml .= '
    <script type="text/javascript">
      Main.add(function(){
        var element = $(document.forms["'.$form.'"]["'.$field.'"]);
        
        if ($(element.form).isReadonly()) return;
        
        element.addSpinner({';
          if ($step)          $sHtml .= "step: $step,";
          if ($this->pos)     $sHtml .= "min: 0,";
          elseif(isset($min)) $sHtml .= "min: $min,";
          if (isset($max))    $sHtml .= "max: $max,";
          if ($deferEvent)    $sHtml .= "deferEvent: true,";
          if ($bigButtons)    $sHtml .= "bigButtons: true,";
          if ($showPlus)      $sHtml .= "showPlus: true,";
          $sHtml .= '_:0 // IE rules
        });
      });
    </script>';
    }
    else {
      $sHtml = $this->getFormElementText($object, $params, $value, $className, "number");
    }
    return $sHtml;
  }
}

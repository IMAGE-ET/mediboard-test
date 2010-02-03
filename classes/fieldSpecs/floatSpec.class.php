<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("mbFieldSpec");

class CFloatSpec extends CMbFieldSpec {
  var $min    = null;
  var $max    = null;
  var $pos    = null;
  
  function getSpecType() {
    return "float";
  }
  
  function getDBSpec(){
    return 'FLOAT'.($this->pos || ($this->min !== null && $this->min >= 0) ? ' UNSIGNED' : '');
  }
  
  function getOptions(){
    return parent::getOptions() + array(
      'min' => 'num',
      'max' => 'num',
      'pos' => 'bool',
    );
  }
  
  function checkProperty($object){
    $propValue = CMbFieldSpec::checkNumeric($object->{$this->fieldName}, false);
    if($propValue === null){
      return "N'est pas une valeur décimale";
    }
    
    // pos
    if($this->pos && $propValue <= 0) {
      return "Doit avoir une valeur positive";
    }
    
    // min
    if($this->min){
      if(!$min = CMbFieldSpec::checkNumeric($this->min, false)){
        trigger_error("Spécification de minimum numérique invalide (min = $this->min)", E_USER_WARNING);
        return "Erreur système";
      }
      if ($propValue < $min) {
        return "Doit avoir une valeur minimale de $min";
      }
    }
      
    // max
    if($this->max){
      $max = CMbFieldSpec::checkNumeric($this->max, false);
      if($max === null){
        trigger_error("Spécification de maximum numérique invalide (max = $this->max)", E_USER_WARNING);
        return "Erreur système";
      }      
      if ($propValue > $max) {
        return "Doit avoir une valeur maximale de $max";
      }
    }
  }
  
  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $object->{$this->fieldName} = self::randomString(CMbFieldSpec::$nums, 2).".".self::randomString(CMbFieldSpec::$nums, 2);
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    $form      = CMbArray::extract($params, "form");
    $increment = CMbArray::extract($params, "increment");
    $showPlus  = CMbArray::extract($params, "showPlus");
    $fraction  = CMbArray::extract($params, "fraction");
    $field     = htmlspecialchars($this->fieldName);
    $maxLength = 8;
    CMbArray::defaultValue($params, "size", $maxLength);
    CMbArray::defaultValue($params, "maxlength", $maxLength);
    $fieldId = $form.'_'.$field;
    
    $min = CMbArray::extract($params, "min");
    if ($min === null) {
      $min = CMbFieldSpec::checkNumeric($this->min);
    }
    
    $max = CMbArray::extract($params, "max");
    if ($max === null) {
      $max = CMbFieldSpec::checkNumeric($this->max);
    }
    
    $new_value = CMbArray::extract($params, "value");
    if ($new_value !== null) $value = $new_value;
    
    $decimals = CMbArray::extract($params, "decimals");
    if ($decimals == null) {
      $decimals = isset($this->precise) ? 4 : 2;
    }
    
    $step = $this->checkNumeric(CMbArray::extract($params, "step"));
    
    CMbArray::defaultValue($params, "size", min($maxLength, 20));
    CMbArray::defaultValue($params, "maxlength", $maxLength);

    if ($form && $increment) {
      $sHtml  = $this->getFormElementText($object, $params, (($value>=0 && $showPlus)?'+':'').(($value==0&&$showPlus)?'0':$value), $className);
      $sHtml .= '
    <script type="text/javascript">
      Main.add(function(){
        $(document.forms["'.$form.'"]["'.$field.'"]).addSpinner({';
          if ($step)       $sHtml .= "step: $step,";
          if ($this->pos)  $sHtml .= "min: 0,";
          elseif(isset($min))     $sHtml .= "min: $min,";
          if (isset($max)) $sHtml .= "max: $max,";
          if ($showPlus)   $sHtml .= "showPlus: $showPlus,";
          if ($decimals)   $sHtml .= "decimals: $decimals,";
          if ($fraction)   $sHtml .= "fraction: $fraction,";
          $sHtml .= '_:0 // IE rules
        });
      });
    </script>';
    }
    else {
      $sHtml = $this->getFormElementText($object, $params, $value, $className);
    }
    return $sHtml;
  }
}

?>
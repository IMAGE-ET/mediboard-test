<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("mbFieldSpec");

class CNumSpec extends CMbFieldSpec {
  
  var $min       = null;
  var $max       = null;
  var $pos       = null;
  var $length    = null;
  var $minLength = null;
  var $maxLength = null;
  
  function getSpecType() {
    return("num");
  }
  
  function getDBSpec(){
    $type_sql = "INT(11)";
    
    if($this->max !== null){
      $max = $this->max;
      $type_sql = "TINYINT(4)";
      if ($max > pow(2,8)) {
        $type_sql = "MEDIUMINT(9)";
      }
      if ($max > pow(2,16)) {
        $type_sql = "INT(11)";
      }
      if ($max > pow(2,32)) {
        $type_sql = "BIGINT(20)";
      }
    }
    
    if($this->pos || ($this->min !== null && $this->min >= 0)) {
      $type_sql .= ' UNSIGNED';
    }
    
    return $type_sql;
  }
  
  function getOptions(){
    return parent::getOptions() + array(
      'min' => 'num',
      'max' => 'num',
      'pos' => 'bool',
      'length' => 'num',
      'minLength' => 'num',
      'maxLength' => 'num',
    );
  }

  function checkProperty($object){
    $propValue = $this->checkNumeric($object->{$this->fieldName}, false);
    
    if($propValue === null){
      return "N'est pas une chaîne numérique";
    }

    // pos
    if($this->pos){
      if ($propValue <= 0) {
        return "Doit avoir une valeur positive";
      }
    }  

    // min
    if($this->min){
      if(!$min = $this->checkNumeric($this->min)){
        trigger_error("Spécification de minimum numérique invalide (min = $this->min)", E_USER_WARNING);
        return "Erreur système";
      }
      if ($propValue < $min) {
        return "Soit avoir une valeur minimale de $min";
      }
    }
    
    // max  
    if($this->max){
      $max = $this->checkNumeric($this->max);
      if($max === null){
        trigger_error("Spécification de maximum numérique invalide (max = $this->max)", E_USER_WARNING);
        return "Erreur système";
      }      
      if ($propValue > $max) {
        return "Soit avoir une valeur maximale de $max";
      }
    }
    
    // length  
    if($this->length){
      if(!$length = $this->checkLengthValue($this->length)){
        trigger_error("Spécification de longueur invalide (longueur = $this->length)", E_USER_WARNING);
        return "Erreur système";
      }
      if (strlen($propValue) != $length) {
        return "N'a pas la bonne longueur (longueur souhaité : $length)'";
      }
    }
    
    // minLength
    if($this->minLength){
      if(!$length = $this->checkLengthValue($this->minLength)){
        trigger_error("Spécification de longueur minimale invalide (longueur = $this->minLength)", E_USER_WARNING);
        return "Erreur système";
      }
      if (strlen($propValue) < $length) {
        return "N'a pas la bonne longueur (longueur minimale souhaitée : $length)'";
      }
    }
    
    // maxLength
    if($this->maxLength){
      if(!$length = $this->checkLengthValue($this->maxLength)){
        trigger_error("Spécification de longueur maximale invalide (longueur = $this->maxLength)", E_USER_WARNING);
        return "Erreur système";
      }
      if (strlen($propValue) > $length) {
        return "N'a pas la bonne longueur (longueur maximale souhaitée : $length)'";
      }
    }

    return null;
  }

  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $propValue =& $object->{$this->fieldName};
    
    if($this->length){
      $propValue = self::randomString(CMbFieldSpec::$nums, $this->length);
      
    }elseif($this->minLength){
      if($this->_defaultLength < $this->minLength){
        $propValue = self::randomString(CMbFieldSpec::$nums, $this->minLength);
      }else{
        $propValue = self::randomString(CMbFieldSpec::$nums, $this->_defaultLength);
      }
      
    }elseif($this->maxLength){
      if($this->_defaultLength > $this->maxLength){
        $propValue = self::randomString(CMbFieldSpec::$nums, $this->maxLength);
      }else{
        $propValue = self::randomString(CMbFieldSpec::$nums, $this->_defaultLength);
      }
    }elseif($this->max || $this->min){
      $min = $this->min !== null ? $this->min : 0;
      $max = $this->max !== null ? $this->max : 999999;
      $propValue = rand($min, $max);
    }else{
      $propValue = self::randomString(CMbFieldSpec::$nums, $this->_defaultLength);
    }
  }

  function getFormHtmlElement($object, $params, $value, $className) {
  	$form      = CMbArray::extract($params, "form");
  	$increment = CMbArray::extract($params, "increment");
  	$showPlus  = CMbArray::extract($params, "showPlus");
  	$field     = htmlspecialchars($this->fieldName);
    $maxLength = mbGetValue($this->length, $this->maxLength, 11);
    $fieldId = $form.'_'.$field;
    
    $min = CMbArray::extract($params, "min");
    if ($min === null) {
      $min = $this->checkNumeric($this->min);
    }
    
    $max = CMbArray::extract($params, "max");
    if ($max === null) {
      $max = $this->checkNumeric($this->max);
    }
    
    $new_value = CMbArray::extract($params, "value");
    if ($new_value !== null) $value = $new_value;
    
    $step = $this->checkNumeric(CMbArray::extract($params, "step"));
    
    CMbArray::defaultValue($params, "size", min($maxLength, 20));
    CMbArray::defaultValue($params, "maxlength", $maxLength);
    if ($form && $increment) {
	    $sHtml  = '<div class="control numericField">';
	    $sHtml .= $this->getFormElementText($object, $params, (($value>=0 && $showPlus)?'+':'').(($value==0&&$showPlus)?'0':$value), $className);
	    $sHtml .= '
	  <script type="text/javascript">
	    Main.add(function(){
	      $(document.forms["'.$form.'"]["'.$field.'"]).addSpinner({';
		      if ($step)       $sHtml .= "step: $step,";
		      if ($this->pos)  $sHtml .= "min: 0,";
		      elseif($min)     $sHtml .= "min: $min,";
		      if (isset($max)) $sHtml .= "max: $max,";
		      if ($showPlus)   $sHtml .= "showPlus: $showPlus,";
		      $sHtml .= 'spinnerElement: $("img_'.$fieldId.'")
        });
      });
		</script>
    <img alt="updown" src="./images/icons/numeric_updown.gif" usemap="#arrow_'.$fieldId.'" id="img_'.$fieldId.'" />
	  <map name="arrow_'.$fieldId.'" >
	    <area coords="0,0,10,8"   tabIndex="10000" style="cursor: pointer;" onclick="$(document.forms[\''.$form.'\'][\''.$field.'\']).spinner.inc()" title="+" />
	    <area coords="0,10,10,18" tabIndex="10000" style="cursor: pointer;" onclick="$(document.forms[\''.$form.'\'][\''.$field.'\']).spinner.dec()" title="-" />
	  </map>
	  
	  </div>';
    } else {
    	$sHtml = $this->getFormElementText($object, $params, $value, $className);
    }
    return $sHtml;
  }
}

?>
<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author Sébastien Fillonneau
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CMbFieldSpec {
  var $object         = null;
  var $spec           = null;
  var $className      = null;
  var $fieldName      = null;
  var $prop           = null;
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
  var $mask           = null;
  var $format         = null;

  var $msgError       = null;

  static $chars  = array();
  static $nums   = array();
  static $months = array();
  static $days   = array();
  static $hours  = array();
  static $mins   = array();
  static $charmap = array(
    '9' => '[0-9]',
    'a' => '[A-Za-z]',
    '*' => '[A-Za-z0-9]',
    '~' => '[+-]',
  );

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
    
    if ($propValue && $this->mask) {
      $propValue = self::formattedToMasked($propValue, $this->mask, $this->format);
    }
    
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
          CApp::rip();
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
    
    // input mask
    if ($field = $this->mask) {
      $regex = self::maskToRegex($this->mask);
      $formatted = self::maskedToFormatted($propValue, $this->mask, $this->format);
      $masked = self::formattedToMasked($propValue, $this->mask, $this->format);

      if (!preg_match($regex, $propValue)) {
      	if (!preg_match($regex, $masked)) {
          return "La donnée '$propValue' ne respecte pas le masque '$this->mask'";
      	} // else, that means the value is already the formatted value
      } else {
        $object->{$this->fieldName} = $formatted;
      }
    }

    return null;
  }

 /**
  *  99/99/9999 >> 
		array	(
		  array('[0-9]', 2),
		  '/',
		  array('[0-9]', 2),
		  '/',
		  array('[0-9]', 4)
		)
  * 
  */
  static function maskToLexemes($mask) {
    $mask = str_replace(array('S', 'P'), array(' ', '|'), $mask);
    $lexemes = array();
    $prevChar = null;
    $count = 0;

    for ($i = 0; $i <= strlen($mask); $i++) {
      $c = (isset($mask[$i]) ? $mask[$i] : null); // To manage the latest char

      if (!isset(self::$charmap[$c])) {
        if (isset(self::$charmap[$prevChar])) {
          $lexemes[] = array(self::$charmap[$prevChar], $count);
        }
        if($c !== null) {
          $lexemes[] = $c;
        }
        $prevChar = $c;
        $count = 0;
      }
      else if ($prevChar !== $c) {
        if (isset(self::$charmap[$prevChar])) {
          $lexemes[] = array(self::$charmap[$prevChar], $count);
          $prevChar = $c;
          $count = 0;
        }
        else {
          $prevChar = $c;
          $count++;
        }
      }
      else if ($prevChar === $c) {
        $count++;
      }
    }
    
    return $lexemes;
  }
  
 /**
  *  99/99/9999 >> /^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/ 
  */
  static function maskToRegex($mask) {
    $mask = str_replace(array('S', 'P'), array(' ', '|'), $mask);
    $regex = '/^';
    $lexemes = self::maskToLexemes($mask);
    
    foreach ($lexemes as $lex) {
      $regex .= is_array($lex) ? 
                  ('('.$lex[0].'{'.$lex[1].'})') : 
                  (preg_match('`[A-Za-z0-9]`', $lex) ? '' : '\\').$lex;
    }
    $regex .= '$/';
    
    return $regex;
  }
  
 /** Removes the mask from the string 
  *   Example : 06-85-98-45-26  >> 0685984526
  *   Or        31/10/1985      >> 1985-10-31 with the format $3-$2-$1
  */
  static function maskedToFormatted($data, $mask, $format = null) {
    $mask = str_replace(array('S', 'P'), array(' ', '|'), $mask);
    $formatted = '';
    
    // If no format is provided, this is the raw value
    if (!$format) {
	    // Could be shorter, using str_replace
	    for ($i = 0; $i < strlen($mask); $i++) {
	      if (isset(self::$charmap[$mask[$i]]) && isset($data[$i])) {
	        $formatted .= $data[$i];
	      }
      }
    } 
    // else, we match the data to the format
    else {
      $regex = self::maskToRegex($mask);
      $formatted = $format;
      
      $matches = array();
      preg_match($regex, $data, $matches);
	    for ($i = 1; ($i < count($matches) && $i < 10); $i++) {
	      $formatted = str_replace('$'.$i, $matches[$i], $formatted);
	    }
    }
    
    return $formatted;
  }

 /** Applies the mask to the string 
  *   Example : 0685984526 >> 06-85-98-45-26
  *   Or        1985-10-31 >> 31/10/1985 with the format $3-$2-$1
  */
  static function formattedToMasked($rawdata, $mask, $format = null) {
  	$mask = str_replace(array('S', 'P'), array(' ', '|'), $mask);
  	$masked = '';
  	
  	if (!$format) {
	  	$n = 0;
	    for ($i = 0; $i < strlen($mask); $i++) {
        $masked .= isset(self::$charmap[$mask[$i]]) && isset($rawdata[$n]) ? 
                     $rawdata[$n++] :
                     $mask[$i];
	    }
  	} 
  	else {
			$lexemes = self::maskToLexemes($mask);
			$areas = array();
			$placeToLexeme = array(); // Makes the correspondance between the $1, $2, $3... in the format and the lexemes
			
			// We collect only the variable lexemes
			$n = 0;
			for ($i = 0; $i < count($lexemes); $i++) {
			  if (is_array($lexemes[$i])) {
			    $areas[++$n] = $lexemes[$i];
			    $placeToLexeme[$n] = $i;
			  }
			}

			$positions = array();
			$formatRegex = "/^$format$/";
			for ($i = 1; $i <= count($areas); $i++) {
        $pos = strpos($formatRegex, '$'.$i);
        $positions[$pos] = $i;
			  $formatRegex = str_replace('$'.$i, ('('.$areas[$i][0].'{'.$areas[$i][1].'})'), $formatRegex);
			}
			
			ksort($positions); // sort by key
			$positions = array_values($positions); // to make keys contiguous
    
	    $matches = array();
	    preg_match($formatRegex, $rawdata, $matches);
      if (count($matches)) {
		    foreach ($areas as $key => $area) {
		      $lexemes[$placeToLexeme[$key]] = $matches[$positions[$key-1]];
		    }
	      $masked = implode('', $lexemes);
      }
  	}
    
    return $masked;
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

  static function randomString($array, $length) {
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

  static function checkNumeric($value, $returnInteger = true){
    if (!is_numeric($value)) {
      return null;
    }
    if($returnInteger){
      $value = intval($value);
    }
    return $value;
  }

  static function checkLengthValue($length){
    if(!$length = CMbFieldSpec::checkNumeric($length)){
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
    if ($this->mask) {
      $value = $this->getValue($object, null);
    }
    else {
      $value = $object->{$this->fieldName};
    }
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

    return "<label title=\"$desc\" >$title</label>";
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
    $autocomplete = CMbArray::extract($params, "autocomplete");
    $form         = CMbArray::extract($params, "form");
    $extra        = CMbArray::makeXmlAttributes($params);
    
    $sHtml        = "<input type=\"text\" name=\"$field\" value=\"".htmlspecialchars($value)."\"";
    $sHtml       .= " class=\"".htmlspecialchars(trim($className." ".$this->prop))."\" ". ($autocomplete ? 'autocomplete="off"' : null) ."$extra/>";
    if ($autocomplete) {
    	list($minChars, $limit, $wholeString) = explode(',', $autocomplete);
    	if ($minChars === null) $minChars = 2;
    	if ($limit === null) $limit = 15;
    	if ($wholeString === null) $wholeString = false;
    	
    	$id = $form.'_'.$field;
    	$sHtml .= '<script type="text/javascript">
    	Main.add(function(){
			  url = new Url();
			  url.setModuleAction("system", "httpreq_field_autocomplete");
			  url.addParam("class", "'.$object->_class_name.'");
			  url.addParam("field", "'.$field.'");
			  url.addParam("limit", '.$limit.');
			  url.addParam("wholeString", '.$wholeString.');
			  url.autoComplete($("'.$id.'"), "'.$id.'_autocomplete", {
			    minChars: '.$minChars.',
			    method: "get"
			  });
		  });
		  </script>';
      $sHtml .= '<div style="display:none; width:0;" class="autocomplete" id="'.$id.'_autocomplete"></div>';
    }
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
    $date  = ($value && $value != '0000-00-00') ? mbTransformTime(null, $value, $format) : "";
    
    $form     = CMbArray::extract($params, "form");
    $register = CMbArray::extract($params, "register");
    
    
    $id    = $form.'_'.$field;
    $extra = CMbArray::makeXmlAttributes($params);
    $html = array();
    $html[] = '<div class="control">';
    //$html[] = '<input type="text" value="'.$date.'" class="'.$this->getSpecType().'" id="'.$id.'_da" disabled="disabled" />';
    $html[] = '<div class="'.$this->getSpecType().'" id="'.$id.'_da">'.$date.'</div>';
    $html[] = '<input type="hidden" name="'.$field.'" class="'.$class.'" value="'.$value.'" '.$extra.' />';
    $html[] = '<img id="'.$id.'_trigger" src="./images/icons/calendar.gif" alt="Choisir la date"  class="trigger" />';

    if (!$this->notNull) {
      $html[] = '<button id="'.$id.'_cancel" class="cancel notext" type="button" onclick="$V('.$field.', new String); $(\''.$id.'_da\').innerHTML = new String;">'.CAppUI::tr("Delete").'</button>';
    }
    
    if ($register) {
      $time = $this instanceof CDateTimeSpec ? "true" : "false";
      $html[] = '<script type="text/javascript">Main.add(function() { Calendar.regField("'.$form.'", "'.$field.'", '.$time.'); } ); </script>';
    }
    $html[] = '</div>';
    return implode("\n", $html);
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
  function checkProperty($object) {}

  // Return a sample value.
  //If consistent, the random value stay the same for a given initial value
  function sample(&$object, $consistent = true){
    $fieldName = $this->fieldName;
    $propValue =& $object->$fieldName;
    if($consistent) {
      srand(crc32($propValue));
    }
  }

  function getDBSpec(){}

  function checkValues(){}
}

CMbFieldSpec::$chars  = range("a","z");
CMbFieldSpec::$nums   = range(0, 9);
CMbFieldSpec::$months = range(1, 12);
CMbFieldSpec::$days   = range(1, 29);
CMbFieldSpec::$hours  = range(9, 19);
CMbFieldSpec::$mins   = range(0, 60, 10);

?>
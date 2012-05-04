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
 * Field spec object representation of serialized prop 
 * as defined in Model objects
 */
class CMbFieldSpec {
  var $object         = null;
  var $spec           = null;
  var $className      = null; // @todo: rename to $owner
  var $fieldName      = null; // @todo: rename to $field
  var $prop           = null;
  
  // Options
  var $default        = null;
  var $notNull        = null;
  var $confidential   = null;
  var $moreThan       = null;
  var $moreEquals     = null;
  var $sameAs         = null;
  var $notContaining  = null;
  var $notNear        = null;
  var $alphaAndNum    = null;
  var $mask           = null;
  var $format         = null;
  var $autocomplete   = null;
  var $aidesaisie     = null;
  var $perm           = null; // Used by autocomplete
  var $dependsOn      = null;
  var $helped         = null;
  var $seekable       = null;
  var $show           = null;
  var $reported       = null;
  var $pattern        = null;
  var $derived        = null;

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
    'x' => '[A-Fa-f0-9]',
    '~' => '[+-]',
  );

  protected $_defaultLength = null;

  /**
   * Standard constructor
   * 
   * @param string $className Class name of the owner object
   * @param string $field     Field name 
   * @param string $prop      Serialized prop
   * 
   * @return void
   */
  function __construct($className, $field, $prop = null) {
    $this->className = $className;
    $this->fieldName = $field;
    $this->prop      = $prop;
    
    if ($suffix = trim($this->getPropSuffix())) {
      $this->prop .= " $suffix";
    }
    
    $parts = explode(" ", $this->prop);
    array_shift($parts);
    
    $spec_options = array();
    foreach ($parts as $_part) {
      $options = explode("|", $_part);
      $spec_options[array_shift($options)] = count($options) ? implode("|", $options) : true;
    }

    $vars = get_object_vars($this);
    foreach ($spec_options as $k => $v) {
      if (array_key_exists($k, $vars)) {
        $this->$k = $v;
      }
      else {
        $error = sprintf(
          "L'option '%s' trouvée dans '%s::%s' est inexistante dans la spec de classe '%s'",
          $k,
          $className,
          $field,
          get_class($this)
        );
        trigger_error($error, E_USER_WARNING);
      }
    }

    $this->_defaultLength = 11;
    
    // Helped fields
    if (is_string($this->helped)) {
      $this->helped = explode("|", $this->helped);
    }
    
    $this->checkOptions();
  }
  
  /**
   * Get spec options and there own meta-prop
   * 
   * @return array Array of default name => option meta-prop
   */
  function getOptions(){
    return array(
      'default'       => 'str',
      'notNull'       => 'bool',
      'confidential'  => 'bool',
      'moreThan'      => 'field',
      'moreEquals'    => 'field',
      'sameAs'        => 'field',
      'notContaining' => 'field',
      'notNear'       => 'field',
      'alphaAndNum'   => 'bool',
      'mask'          => 'str',
      'format'        => 'str',
      'autocomplete'  => 'bool',
      'aidesaisie'    => 'bool',
      'dependsOn'     => 'field',
      'perm'          => 'str',
      'helped'        => 'bool',
      'seekable'      => 'bool',
      'reported'      => 'bool',
      'pattern'       => 'str',
    );
  }
  
  function getPropSuffix(){
    // No prop suffix by default
  }
  
  /**
   * Standard to string magic function
   * 
   * @return string 
   */
  function __toString() {
    return $this->prop;
  }

  /**
   * Check whether prop has been declared in parent class
   * 
   * @return bool true if prop is inherited, false otherwise
   */
  function isInherited() {
    if ($parentClass = get_parent_class($this->className)) {
      if ($parent = @new $parentClass) {
        return isset($parent->_prop[$this->fieldName]);
      }
    }
    
    return false;
  }
  
  /**
   * Get the HTML formatted value for the field associated with this spec
   * 
   * @param object $object Object holding the field value
   * @param Smarty $smarty Optional smarty instance
   * @param array  $params Optional extra params
   * 
   * @return html Formatted value
   */
  function getValue($object, $smarty = null, $params = array()) {
    $propValue = $object->{$this->fieldName};
    
    if ($propValue && $this->mask) {
      $propValue = self::formattedToMasked($propValue, $this->mask, $this->format);
    }
    
    return htmlspecialchars($propValue);
  }

  /**
   * Check options (params) of the spec vs. the field value
   * 
   * @param object $object Object holding the field value
   * 
   * @return unknown_type
   */
  function checkParams($object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;

    // NotNull
    if ($this->notNull && $this->default === null && ($propValue === null || $propValue === "")) {
      return "Ne peut pas avoir une valeur nulle";
    }

    if ($propValue === null || $propValue === "") {
      return null;
    }
    
    // moreThan
    if ($field = $this->moreThan) {
      if ($msg = $this->checkTargetPropValue($object, $field)) {
        return $msg;
      }
      
      $targetPropValue = $object->$field;
      if ($propValue <= $targetPropValue) {
        return "'$propValue' n'est pas strictement supérieur à '$targetPropValue'";
      }
    }

    // moreEquals
    if ($field = $this->moreEquals) {
      if ($msg = $this->checkTargetPropValue($object, $field)) {
        return $msg;
      }
      
      $targetPropValue = $object->$field;
      if ($propValue < $targetPropValue) {
        return "'$propValue' n'est pas supérieur ou égal à '$targetPropValue'";
      }
    }

    // sameAs
    if ($field = $this->sameAs) {
      if ($msg = $this->checkTargetPropValue($object, $field)) {
        return $msg;
      }
      
      $targetPropValue = $object->$field;
      if ($propValue !== $targetPropValue) {
        return "Doit être identique à '$field->fieldName'";
      }
    }

    // notContaining
    if ($field = $this->notContaining) {
      if ($msg = $this->checkTargetPropValue($object, $field)) {
        return $msg;
      }
      
      $targetPropValue = $object->$field;
      if (stristr($propValue, $targetPropValue)) {
        return "Ne doit pas contenir '$field->fieldName'";
      }
    }
    
      // notNear
    if ($field = $this->notNear) {
      if ($msg = $this->checkTargetPropValue($object, $field)) {
        return $msg;
      }
      
      $targetPropValue = $object->$field;  
      if (levenshtein($propValue, $targetPropValue) < 2) {
        return "Le mot de passe ressemble trop à '$field->fieldName'";
      }
    }

    // alphaAndNum
    if ($this->alphaAndNum) {
      if (!preg_match("/[a-z]/", $propValue) || !preg_match("/\d+/", $propValue)) {
        return 'Doit contenir au moins un chiffre ET une lettre';
      }
    }
    
    // input mask
    if ($this->mask) {
      $regex = self::maskToRegex($this->mask);
      $formatted = self::maskedToFormatted($propValue, $this->mask, $this->format);
      $masked = self::formattedToMasked($propValue, $this->mask, $this->format);

      if (!preg_match($regex, $propValue)) {
        if (!preg_match($regex, $masked)) {
          return "La donnée '$propValue' ne respecte pas le masque '$this->mask'";
        } // else, that means the value is already the formatted value
      }
      else {
        $propValue = $object->{$this->fieldName} = $formatted;
      }
    }

    // pattern
    // regex sans modificateurs
    // par exemple : pattern|\s*[a-zA-Z][a-zA-Z0-9_]*\s*
    // On peut mettre des pipe dans la regex avec \x7C ou des espaces avec \x20
    // http://www.whatwg.org/specs/web-apps/current-work/multipage/common-input-element-attributes.html#the-pattern-attribute
    if ($this->pattern && !preg_match('/^(?:'.$this->pattern.')$/', $propValue)) {
      return 'Ne correspond pas au format attendu';
    }
  }

  /**
   * Turn a field mask into a Lexemes (?!) array
   *  
   * 99/99/9999 >> 
   * array  (
   *   array('[0-9]', 2),
   *   '/',
   *   array('[0-9]', 2),
   *   '/',
   *   array('[0-9]', 4)
   * )
   * 
   * @param string $mask Field mask, see above
   * 
   * @return array See above
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
        
        if ($c !== null) {
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
   * Turn a field mask into a regex
   *  
   * 99/99/9999 >> 
   * /^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/ 
   * 
   * @param string $mask Field mask, see above
   * 
   * @return array See above
   */
  static function maskToRegex($mask) {
    $mask = str_replace(array('S', 'P'), array(' ', '|'), $mask);
    $regex = '/^';
    $lexemes = self::maskToLexemes($mask);
    
    foreach ($lexemes as $lex) {
      $regex .= is_array($lex) ? 
                  ('('.$lex[0].'{'.max(1, $lex[1]).'})') : 
                  (preg_match('`[A-Za-z0-9]`', $lex) ? '' : '\\').$lex;
    }
    $regex .= '$/';
    
    return $regex;
  }
  
  /** 
   * Remove a mask from a value and possibly reformat it
   *  
   * Example : 06-85-98-45-26 >> 0685984526 (with no format)
   * Example : 31/10/1985     >> 1985-10-31 (with format "$3-$2-$1")
   *   
   * @param string $data   Data to format
   * @param string $mask   The mask
   * @param string $format Optional format
   * 
   * @return string Formatted data
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

  /** 
   * Apply a mask from a row value and possibly defining row format
   *  
   * Example : 0685984526 >> 06-85-98-45-26 (with no format)
   * Example : 1985-10-31 >> 31/10/1985 (with format "$3-$2-$1")
   *   
   * @param string $rawdata Data to format
   * @param string $mask    The mask
   * @param string $format  Optional format
   * 
   * @return string Masked data
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

  /**
   * Check whether target field exists in object
   * 
   * @param object $object Object to scan
   * @param string $field  Field name
   * 
   * @return string Error-like message
   */
  function checkTargetPropValue($object, $field){
    $fields = get_object_vars($object);
    if (!$field || $field === true || !is_scalar($field) || !array_key_exists($field, $fields)) {
      $class = get_class($this);
      trigger_error("Elément cible '$field' invalide ou inexistant dans la classe '$class'", E_USER_WARNING);
      return "Erreur système";
    }
  }

  /**
   * Check property value with regard to this specification
   * 
   * @param object $object Objcet holding de field
   * 
   * @return string Error-like message
   * @todo rename to checkValue()
   */
  function checkPropertyValue($object){
    $propValue = $object->{$this->fieldName};

    if ($msg = $this->checkParams($object)) {
      return $msg;
    }

    if ($propValue === null || $propValue === "") {
      return;
    }

    if ($msg = $this->checkProperty($object)) {
      return $msg;
    }
  }

  /**
   * Build a arbitrary length random string with given char set
   * 
   * @param array $array  Char set 
   * @param int   $length Length of wanted string
   * 
   * @return string The random string
   * @todo Should be part of CMBString
   */
  static function randomString($array, $length) {
    $string = "";
    $count = count($array) - 1;
    for ($i = 0; $i < $length; $i++) {
      $string .= $array[rand(0, $count)];
    }
    
    return $string;
  }

  /**
   * Check whether a value is numeric, with dot/comma tolerance, then cast it
   * 
   * @param mixed &$value Given in-out value, gets cast on success
   * @param bool  $int    Cast to int if true, float otherwise
   * 
   * @return mixed Cast value
   * @todo Should be part of CValue
   */
  static function checkNumeric(&$value, $int = true){
    // Dot/comma tolerance
    $value = preg_replace(array('/\s/', '/,/'), array('', '.'), $value);
    if (!is_numeric($value)) {
      return null;
    }
    return $value = ($int ? intval($value) : floatval($value));
  }

  /**
   * Check a length strictly positive one-byte value
   * 
   * @param mixed $length Value to check
   * 
   * @return mixed Cast value, null on failure
   */
  static function checkLengthValue($length){
    if (!$length = CMbFieldSpec::checkNumeric($length)) {
      return;
    }
    
    if ($length < 1 || $length > 255) {
      return;
    }
    
    return $length;
  }

  /**
   * Scramble bound valued field for given object, if confidential
   * 
   * @param object $object Object holding the field
   * 
   * @return void
   */
  function checkConfidential($object){
    if (!$this->confidential || $object->{$this->fieldName} === null) {
      return;
    }

    $this->sample($object);
  }

  /**
   * Get an HTML form element corresponding to the bound object value
   * 
   * @param object $object Object holding the field
   * @param array  $params Extra parameters
   *  
   * @return html HTML form string
   */
  function getFormElement($object, $params){
    $hidden    = CMbArray::extract($params, "hidden");
    $className = CMbArray::extract($params, "class");
    
    // Forces readonly param for locked objcets
    if ($object->_locked) {
      $params["readonly"] = "readonly";
    }
    
    // @todo Probably useless wrt to getValue() behaviour
    $value = $this->mask ? $this->getValue($object) : $object->{$this->fieldName};
    
    // Hidden case
    if ($hidden) {
      return $this->getFormHiddenElement($object, $params, $value, $className);
    }
    
    return $this->getFormHtmlElement($object, $params, $value, $className);
  }

  /**
   * Get an HTML label element corresponding to the bound object value
   *
   * @param object $object Object holding the field
   * @param array  $params Extra parameters
   *   - defaultFor : name of a radio option to associate label with
   *   
   * @return html HTML label string
   */
  function getLabelElement($object, $params = array()) {
    $defaultFor = CMbArray::extract($params, "defaultFor");
    $forName = $defaultFor ? $defaultFor : $this->getLabelForAttribute($object, $params);
    $className = $object->_specs[$this->fieldName]->notNull ? "checkNull" : "";
    
    $extra  = CMbArray::makeXmlAttributes($params);

    $desc  = CAppUI::tr("$object->_class-$this->fieldName-desc");
    $desc = htmlentities($desc);
    $sHtml  = "<label for=\"$forName\" class=\"$className\" title=\"$desc\" $extra>";
    $sHtml .= CAppUI::tr("$object->_class-$this->fieldName");
    $sHtml .= "</label>";

    return $sHtml;
  }

  /**
   * Get an HTML title label element corresponding to the bound object value
   *
   * @param object $object Object holding the field
   * @param array  $params Extra parameters
   * 
   * @return html HTML title string
   */
  function getTitleElement($object, $params) {
    $desc  = CAppUI::tr("$object->_class-$this->fieldName-desc");
    $desc = htmlentities($desc);
    $title = CAppUI::tr("$object->_class-$this->fieldName-court");
    return "<label title=\"$desc\" >$title</label>";
  }

  /**
   * Get the for attribute of label element
   * 
   * @param object $object  Object holding the field
   * @param array  &$params Extra parameters
   * 
   * @return string HTML label for attribute
   */
  function getLabelForAttribute($object, &$params){
    return $this->fieldName;
  }
  
  /**
   * Get an HTML form element hidden variant corresponding to the bound object value
   * 
   * @param object $object    Object holding the field
   * @param array  $params    Extra parameters
   * @param string $value     The actual value
   * @param string $className Extra CSS class name
   *  
   * @return html HTML form string
   */
  function getFormHiddenElement($object, $params, $value, $className) {
    $field = htmlspecialchars($this->fieldName);
    $value = htmlspecialchars($value);
    
    // Needs to be extracted
    CMbArray::extract($params, "form"); 
    
    // Input 
    $sHtml = "<input type=\"hidden\" name=\"$field\" value=\"$value\"";
    if ($this->prop) {
      $prop = htmlspecialchars(trim("$className $this->prop"));
      $sHtml.= " class=\"$prop\"";
    }
    
    // Extra attributes
    $extra = CMbArray::makeXmlAttributes($params);
    $sHtml.= " $extra/>";

    return $sHtml;
  }

  /**
   * Get an HTML form text input corresponding to the bound object value
   * 
   * @param object $object    Object holding the field
   * @param array  $params    Extra parameters
   * @param string $value     The actual value
   * @param string $className Extra CSS class name
   *  
   * @return html HTML form input string
   */
  function getFormElementText($object, $params, $value, $className){
    $field        = htmlspecialchars($this->fieldName);
    $protected    = $value && $object->_id && isset($this->protected) && $this->protected;
    
    if ($protected) {
      $params["readonly"] = "readonly";
    }
    
    $autocomplete = CMbArray::extract($params, "autocomplete", "true,2,30,false,false");
    $form         = CMbArray::extract($params, "form");
    $multiline    = CMbArray::extract($params, "multiline");
    $extra        = CMbArray::makeXmlAttributes($params);
    $spec         = $object->_specs[$field];
    $ref = false;

    // @todo: use a better way of getting options
    @list($activated, $minChars, $limit, $wholeString, $dropdown) = explode(',', $autocomplete);
    
    if ($this->autocomplete && $form && $activated === 'true') {
      if ($minChars    === null || $minChars    === "") {
        $minChars = 2;
      }
      if ($limit       === null || $limit       === "") {
        $limit = 30;
      }
      if ($wholeString === null || $wholeString === "") {
        $wholeString = false;
      }
      if ($dropdown    === null || $dropdown    === "" || $dropdown === "false") {
        $dropdown = false;
      }
      
      $options = explode('|', $this->autocomplete);
      $view_field = reset($options);
      $show_view = isset($options[1]);
      
      if ($spec instanceof CRefSpec && $this->autocomplete) {
        $ref_object = new $spec->class;
        $ref_object->load($value);
        $view = $ref_object->$view_field;
        
        $sHtml  = "<input type=\"hidden\" name=\"$field\" value=\"".htmlspecialchars($value)."\" 
                    class=\"".htmlspecialchars("$className $this->prop")."\" $extra />";
        $sHtml .= "<input type=\"text\" name=\"{$field}_autocomplete_view\" value=\"".htmlspecialchars($view)."\" 
                    class=\"autocomplete\" onchange='if(!this.value){this.form[\"$field\"].value=\"\"}' $extra />";
        $ref = true;
      }
      else {
        $sHtml  = "<input type=\"text\" name=\"$field\" value=\"".htmlspecialchars($value)."\"
                    class=\"".htmlspecialchars("$className $this->prop")."\" $extra />";
      }
      
      $id = $form.'_'.$field.($ref ? '_autocomplete_view' : '');
      $sHtml .= '<script type="text/javascript">
      Main.add(function(){
        var input = $("'.$id.'");
        var url = new Url("system", "httpreq_field_autocomplete");
        url.addParam("class", "'.$object->_class.'");
        url.addParam("field", "'.$field.'");
        url.addParam("limit", '.$limit.');
        url.addParam("view_field", "'.$view_field.'");
        url.addParam("show_view", '.($show_view ? 'true' : 'false').');
        url.addParam("input_field", "'.$field.($ref ? '_autocomplete_view' : '').'");
        url.addParam("wholeString", '.$wholeString.');
        url.autoComplete(input, "'.$id.'_autocomplete", {
          minChars: '.$minChars.',
          method: "get",
          select: "view",
           dropdown: '.(!$ref || $dropdown ? 'true' : 'false');
      
      if ($ref) {
        $sHtml .= ',
          afterUpdateElement: function(field,selected){
            $V(field.form["'.$field.'"], selected.getAttribute("id").split("-")[2]);
          }';
      }
      if ($this->dependsOn) {
        $wheres = explode("|", $this->dependsOn);
        $sHtml .= ',
          callback: function(element, query){
            var field;';
        
        foreach ($wheres as $_where) {
          $sHtml .= "field = input.form.elements[\"".$_where."\"];
          if (field) query += \"&where[$_where]=\" + \$V(field);";
        }
        
        $sHtml .= "  return query;";
        $sHtml .= '}';
      }
      
      $sHtml .= '});});</script>';
      $sHtml .= '<div style="display:none; width:0;" class="autocomplete" id="'.$id.'_autocomplete"></div>';
    }
    else {
      if ($multiline) {
        $has_CR = strpos($value, "\n") !== false;
        
        if ($has_CR) {
          if (!isset($params["style"])) {
            $params["style"] = "";
          }
          
          $params["style"] .= "width: auto;";
          $extra = CMbArray::makeXmlAttributes($params);
          $prop = htmlspecialchars(trim("$className $this->prop"));
          $sHtml = "<textarea name=\"$field\" class=\"$prop noresize\" $extra>".
                      htmlspecialchars($value).
                   "</textarea>";
        }
        else {
          $sHtml = "<input type=\"text\" name=\"$field\" value=\"".htmlspecialchars($value)."\"
                    class=\"".htmlspecialchars("$className $this->prop")."\" $extra/>";
        }
        
        $sHtml .= '<button type="button" class="'.($has_CR ? "singleline" : "multiline").' notext" tabIndex="10000"
                           onclick="$(this).previous(\'input,textarea\').switchMultiline(this)"></button>';
      }
      else {
        $sHtml = "<input type=\"text\" name=\"$field\" value=\"".htmlspecialchars($value)."\"
                  class=\"".htmlspecialchars("$className $this->prop")."\" $extra/>";
      }
    }
    
    if ($protected) {
      $onclick = "";
      $sHtml .= 
        '<button type="button" onclick="var p=$(this).previous(\'input,textarea\');p.readOnly=!p.readOnly;"'.
        ' class="notext lock" title="'.CAppUI::tr("Unlock").'"></button>';
    }
    
    return $sHtml;
  }

  /**
   * Get an HTML form textarea corresponding to the bound object value
   * 
   * @param object $object    Object holding the field
   * @param array  $params    Extra parameters
   * @param string $value     The actual value
   * @param string $className Extra CSS class name
   *  
   * @return html HTML form textarea string
   */
  function getFormElementTextarea($object, $params, $value, $className){
    $field = htmlspecialchars($this->fieldName);
    $rows  = CMbArray::extract($params, "rows", "3");
    $form  = CMbArray::extract($params, "form"); // needs to be extracted
    $aidesaisie = CMbArray::extract($params, "aidesaisie");
    $extra = CMbArray::makeXmlAttributes($params);
    $prop = htmlspecialchars(trim("$className $this->prop"));
    $value = htmlspecialchars($value);
    $sHtml = "<textarea name=\"$field\" rows=\"$rows\" class=\"$prop\" $extra>$value</textarea>";
    
    if ($form && $this->helped) {
      $params_aidesaisie = array();
      $params_aidesaisie[] = "objectClass: '".get_class($object) . "'";
      $depend_fields = $object->_specs[$field]->helped;
      
      if (!isset($params["dependField1"])) {
        if (isset($depend_fields["0"])) {
          $params_aidesaisie[] = "dependField1: getForm('$form').elements['".$depend_fields["0"]."']";
        }
      }
      if (!isset($params["dependField2"])) {
        if (isset($depend_fields["1"])) {
          $params_aidesaisie[] = "dependField2: getForm('$form').elements['".$depend_fields["1"]."']";
        }
      }
      
      $params_aidesaisie = '{'.implode(",", $params_aidesaisie);
      
      if ($aidesaisie) {
        $params_aidesaisie .= ", $aidesaisie";
      }
      $params_aidesaisie .= '}';
      
      $sHtml .=
      "<script type='text/javascript'>
        Main.add(function() {
          new AideSaisie.AutoComplete(getForm('$form').elements['$field'], $params_aidesaisie);
        });
       </script>";
    }
    
    return $sHtml;
  }

  /**
   * Get an HTML form datetime input corresponding to the bound object value
   * 
   * @param object $object    Object holding the field
   * @param array  $params    Extra parameters
   * @param string $value     The actual value
   * @param string $className Extra CSS class name
   * @param string $format    Optional datetime format
   *  
   * @return html HTML form datetime string
   */
  function getFormElementDateTime($object, $params, $value, $className, $format = "%d/%m/%Y %H:%M") {
    if ($object->_locked) {
      $params["readonly"] = "readonly";
    }

    $class = htmlspecialchars(trim("$className $this->prop"));
    $field = htmlspecialchars($this->fieldName);
    
    // Format the date
    $date = "";
    if ($value && $value != '0000-00-00' && $value != '00:00:00' && $value != '0000-00-00 00:00:00') {
      $date =  ($this instanceof CDateSpec && $this->progressive) ? 
        $this->getValue($object, null, $params) : 
        mbTransformTime(null, $value, $format);
    }
    
    $form     = CMbArray::extract($params, "form");
    $register = CMbArray::extract($params, "register");
    
    // Tab index in display input
    $tabindex = CMbArray::extract($params, "tabindex");
  
    $extra = CMbArray::makeXmlAttributes($params);
    $html = array();
    $html[] = '<input name="'.$field.'_da" type="text" value="'.$date.'" class="'.$class.'" 
                      readonly="readonly" '.(isset($tabindex) ? 'tabindex="'.$tabindex.'" ' : '').'/>';
    $html[] = '<input name="'.$field.'" type="hidden" value="'.$value.'" class="'.$class.'" '.$extra.' />';

    if ($form && ($register || $this instanceof CTimeSpec)) {
      $register = $this instanceof CDateSpec && $this->progressive ? 'regProgressiveField' : 'regField';
      $html[] = '<script type="text/javascript">
        Main.add(function(){Calendar.'.$register.'(getForm("'.$form.'").elements["'.$field.'"])})
      </script>';
    }
    
    return implode("\n", $html);
  }

  /**
   * Get an HTML form element corresponding to the bound object value
   * 
   * @param object $object    Object holding the field
   * @param array  $params    Extra parameters
   * @param string $value     The actual value
   * @param string $className Extra CSS class name
   *  
   * @return html HTML form element string
   */
  function getFormHtmlElement($object, $params, $value, $className){
    return $this->getFormElementText($object, $params, $value, $className);
  }

  /**
   * Get the type of this spec, reciprocally to the spec factory
   * Has to be redefined in each and every subclass
   * 
   * @return string Spec type
   * @todo could be abstract
   */
  function getSpecType() {
    return "mbField";
  }

  /**
   * Get the SQL implementation typeof this spec
   * 
   * @return string SQL type
   * @todo could be abstract
   */
  function getDBSpec() {
  }
  
  /**
   * Check whether property value bound to objects is compliant to the specification
   * 
   * @param object $object Object bound to property
   * 
   * @return string Store-like message
   * @todo could be abstract
   */
  function checkProperty($object) {
  }

  /**
   * Produce an sample value according to this spec
   * 
   * @param object $object     Object bound to property
   * @param bool   $consistent If true, random value stay the same for a given initial value
   * 
   * @return string Sample value
   */
  function sample($object, $consistent = true){
    if ($consistent) {
      srand(crc32($object->{$this->fieldName}));
    }
  }

  /**
   * Get SQL column properties out of SQL specification string
   * 
   * @param string $db_spec        Object bound to property
   * @param bool   $reduce_strings If true, unquote strings and cast to integer when possible
   * 
   * @return array SQL column properties array
   */
  static function parseDBSpec($db_spec, $reduce_strings = false) {
    $props = array(
      'type' => null,
      'params' => null,
      'unsigned' => null,
      'zerofill' => null,
    );
    
    $props['type']    = $db_spec;
    $props['unsigned'] = stristr($db_spec, 'unsigned') != false;
    $props['zerofill'] = stristr($db_spec, 'zerofill') != false;
    $props['type'] = trim(str_ireplace(array('unsigned', 'zerofill'), '', $props['type']));
    $props['params']  = null;
    
    if ($pos = strpos($props['type'], '(')) {
      $props['params'] = explode(',', substr($props['type'], $pos+1, strpos($props['type'], ')')-$pos-1));
      $props['params'] = array_map('trim', $props['params']);
      
      if ($reduce_strings) {
        foreach ($props['params'] as &$v) {
          $v =  ($v[0] === "'")  ? trim($v, "'") : $v;
        }
      }
      
      $props['type']   = substr($props['type'], 0, $pos);
    }
    
    $props['type'] = strtoupper(trim($props['type']));
    
    return $props;
  }
  
  /**
   * Tell whether this spec is a text-like spec
   * 
   * @return bool 
   * @todo Should use inheritence
   */
  function isTextBlob(){
    return in_array($this->getSpecType(), array("text", "html", "php", "xml", "set"));
  }

  /**
   * Get the full SQL implementation for this spec
   * 
   * @return string SQL column implementation unknown_type
   */
  function getFullDBSpec(){
    $object = new $this->className;
    $is_key = $object->_spec->key == $this->fieldName;
    $props = array(
     'type' => $this->getDBSpec(),
     //'unsigned' => $this instanceof CRefSpec ? 'UNSIGNED' : '',
     //'zerofill' => isset($this->zerofill) ? 'ZEROFILL' : '',
     'notnull' => isset($this->notNull) || $is_key ? 'NOT NULL' : '',
     /*'index' => ($this instanceof CRefSpec || 
                 $this instanceof CDateTimeSpec || 
                 $this instanceof CDateSpec || 
                 $this instanceof CTimeSpec ||
                 isset($this->index)) ? "INDEX" : '',*/
     'extra' => $is_key ? 'auto_increment' : null,
    );
    
    // @todo mettre dans dans les classes elles-mêmes
    if (!$this->isTextBlob()) {
      if (isset($this->default) && !($this instanceof CBoolSpec && $this->default === "")) {
        $props['default'] = "DEFAULT '$this->default'";
      }
    }
    
    return implode(' ', $props);
  }

  /**
   * Check -- and repair when possible -- options completude for this specification
   * 
   * @return void
   */
  function checkOptions() {
  }
  
  /**
   * Spec specific trim function
   * 
   * @param string $value The value
   * 
   * @return string The trimmed value
   */
  function trim($value) {
    return trim($value);
  }
}

CMbFieldSpec::$chars  = range("a", "z");
CMbFieldSpec::$nums   = range(0, 9);
CMbFieldSpec::$months = range(1, 12);
CMbFieldSpec::$days   = range(1, 29);
CMbFieldSpec::$hours  = range(9, 19);
CMbFieldSpec::$mins   = range(0, 60, 10);

?>
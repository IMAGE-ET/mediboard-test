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

CAppUI::requireLibraryFile("smarty/libs/Smarty.class");
CAppUI::requireLibraryFile("smarty/libs/plugins/modifier.escape");

/**
 * Mediboard integration of Smarty engine main class
 *
 * Provides an extension of smarty class with directory initialization
 * integrated to Mediboard framework as well as standard data assignment
 */
class CSmartyMB extends Smarty {
  static $extraPath = "";
  
  /**
   * Construction
   * Directories initialisation
   * Standard data assignment
   */
  function __construct($dir = null) {
    global $version, $can, $m, $a, $tab, $g, $action, $actionType, $dialog, $ajax, $suppressHeaders, $uistyle;
    
    $rootDir = CAppUI::conf("root_dir");
    $extraPath = self::$extraPath;

    $root = $extraPath ? "$rootDir/$extraPath" : $rootDir;

    $tmpDir = "$rootDir/tmp";
    
    if (!$dir) {
      $dir = "$root/modules/$m"; 
      $this->compile_dir = "$tmpDir/templates_c/{$extraPath}modules/$m/";
    }
    else {
      $this->compile_dir = "$tmpDir/templates_c/{$extraPath}$dir/";
    }
    
    // Directories initialisation
    $this->template_dir = "$dir/templates/";
    
    // Check if the cache dir is writeable
    if (!is_dir($this->compile_dir)) {
      CMbPath::forceDir($this->compile_dir);
    }

    // Delimiter definition
    $this->left_delimiter  = "{{";
    $this->right_delimiter = "}}";
    
    // Default modifier for security reason
    $this->default_modifiers = array("@cleanField");
    
    // Register mediboard functions
    $this->register_block   ("tr"                , array($this,"tr")); 
    $this->register_block   ("main"              , array($this,"main")); 
    
    $this->register_function("mb_default"        , array($this,"mb_default"));
    $this->register_function("mb_ditto"          , array($this,"mb_ditto"));
    $this->register_function("mb_class"          , array($this,"mb_class"));
    $this->register_function("mb_value"          , array($this,"mb_value"));
    $this->register_function("mb_include"        , array($this,"mb_include"));
    $this->register_function("mb_script"         , array($this,"mb_script"));
    $this->register_function("thumb"             , array($this,"thumb"));
    $this->register_function("unique_id"         , array($this,"unique_id"));
    
    $this->register_modifier("idex"              , array($this,"idex"));
    $this->register_modifier("conf"              , array($this,"conf"));

    $this->register_modifier("pad"               , array($this,"pad"));
    $this->register_modifier("json"              , array($this,"json"));
    $this->register_modifier("iso_date"          , array($this,"iso_date"));
    $this->register_modifier("iso_time"          , array($this,"iso_time"));
    $this->register_modifier("iso_datetime"      , array($this,"iso_datetime"));
    $this->register_modifier("rel_datetime"      , array($this,"rel_datetime"));
    $this->register_modifier("week_number_month" , array($this,"week_number_month"));
    $this->register_modifier("const"             , array($this,"_const"));
    $this->register_modifier("static"            , array($this,"_static"));
    $this->register_modifier("static_call"       , array($this,"static_call"));
    $this->register_modifier("cleanField"        , array($this,"cleanField"));
    $this->register_modifier("stripslashes"      , array($this,"stripslashes"));
    $this->register_modifier("emphasize"         , array($this,"emphasize"));
    $this->register_modifier("ternary"           , array($this,"ternary"));
    $this->register_modifier("trace"             , array($this,"trace"));
    $this->register_modifier("currency"          , array($this,"currency"));
    $this->register_modifier("percent"           , array($this,"percent"));
    $this->register_modifier("spancate"          , array($this,"spancate"));
    $this->register_modifier("decabinary"        , array($this,"decabinary"));
    $this->register_modifier("module_installed"  , array($this,"module_installed"));
    $this->register_modifier("module_active"     , array($this,"module_active"));
    $this->register_modifier("JSAttribute"       , array($this,"JSAttribute"));
    
    $modules = CModule::getActive();
    foreach ($modules as $mod) {
      $mod->canDo();
    }
    
    // Standard data assignment
    $this->assign("style", $uistyle);
    $this->assign("app", CAppUI::$instance);
    $this->assign("conf", CAppUI::conf());
    $this->assign("user", CAppUI::$instance->user_id); // shouldn't be necessary
    $this->assign("version", $version); 
    $this->assign("suppressHeaders", $suppressHeaders);
    $this->assign("can", $can);
    $this->assign("m", $m);
    $this->assign("a", $a);
    $this->assign("tab", $tab);
    $this->assign("action", $action);
    $this->assign("actionType", $actionType);
    $this->assign("g", $g);
    $this->assign("dialog", $dialog);
    $this->assign("ajax", $ajax);
    $this->assign("modules", $modules);
    $this->assign("base_url", CApp::getBaseUrl());
    $this->assign("current_group", CGroups::loadCurrent());
  }
  
  
  /**
   * Assign a template var to default value if undefined
   * @param array params 
   * - name  : Name of the var
   * - value : Default value of the var
   * @return void
   */
  function mb_default($params, &$smarty) {
    $var   = CMbArray::extract($params, "var"  , true);
    $value = CMbArray::extract($params, "value", true);
    
    if (!isset($smarty->_tpl_vars[$var])) {
      $smarty->assign($var, $value);
    }
  }
  
  /**
   * Show a value if different from previous cached one
   * @param array params Smarty parameters
   * - name  : Name of the cached value
   * - value : Value to show, empty string to clear out cache
   */
  function mb_ditto($params, &$smarty) {
    static $cache = array();
    $name   = CMbArray::extract($params, "name",  null, true);
    $value  = CMbArray::extract($params, "value", null, true);
    $reset  = CMbArray::extract($params, "reset", false, false);
    $old = '';
    if (!$reset) {
      $old = CMbArray::get($cache, $name, "");
    }
    $cache[$name] = $value;
    return $old != $value ? $value : "|";
  }
  
  /**
   * @param array params tableau des parametres
   * Cette fonction prend les mêmes paramètres que mb_field, mais seul object est requis.
   */
  function mb_class($params, &$smarty) {
    if (null == $object = CMbArray::extract($params, "object")) {
      $class = CMbArray::extract($params, "class" , null, true);
    } 
    else {
      $class = $object->_class;
    }
    
    return "<input type=\"hidden\" name=\"@class\" value=\"$class\" />";
  }
  
  
  /**
   * Get the value of a given field (property)
   * 
   */
  function mb_value($params, &$smarty) {
    $object = CMbArray::extract($params, "object",  null, true);
    $field  = CMbArray::extract($params, "field");
    
    if (!$field) {
      return "<span onmouseover=\"ObjectTooltip.createEx(this, '$object->_guid')\">$object->_view</span>";
    }  
    
    if (null !== $value = CMbArray::extract($params, "value")) {
      
      $object->$field = $value;
      
      // Empties cache for forward references
      if (isset($object->_fwd[$field])) {
        unset($object->_fwd[$field]);  
      }
    }
  
    $spec = $object->_specs[$field];
    return $spec->getHtmlValue($object, $smarty, $params);
  }
  
  /**
   * Get a concrete filename for automagically created content
   *
   * @param string $auto_base
   * @param string $auto_source
   * @param string $auto_id
   * @return string
   * @staticvar string|null
   * @staticvar string|null
   */
  function _get_auto_filename($auto_base, $auto_source = null, $auto_id = null){
    $_compile_dir_sep =  $this->use_sub_dirs ? DIRECTORY_SEPARATOR : '^';
    $_return = $auto_base . DIRECTORY_SEPARATOR;

    if (isset($auto_id)) {
      // make auto_id safe for directory names
      $auto_id = str_replace('%7C',$_compile_dir_sep,(urlencode($auto_id)));
      // split into separate directories
      $_return .= $auto_id . $_compile_dir_sep;
    }

    if (isset($auto_source)) {
      // make source name safe for filename
      $_filename = urlencode(basename($auto_source));
      $_crc32 = sprintf('%08X', crc32($auto_source));
      // prepend %% to avoid name conflicts with
      // with $params['auto_id'] names
      
      // increment this value at dev time to enforce template recompilation
      static $increment = 3;
      $_return .=  "$_filename.$increment.%$_crc32%";
    }

    return $_return;
  }
  
  /**
   * Show debug spans
   *
   * @param string $tpl_file
   * @param string $vars
   */
  function showDebugSpans($tpl_file, $params) {
    // The span
    echo "\n<span class='smarty-include ".(empty($params['ajax']) ? '' : 'ajax')."'>\n$tpl_file";
    
    $vars = isset($params["smarty_include_vars"]) ? $params["smarty_include_vars"] : array();
    
    foreach ($vars as $var => $value) {
      $show = $value;
      if ($value instanceof CMbObject) {
        $show = $value->_guid;
      }
  
      if (is_array($value)) {
        $count = count($value);
        $show = "array ($count)";
      }
       
      echo "\n<br />$var: $show";
    }

    echo "\n</span>\n";
  }
  
  /**
   * called for included templates
   *
   * @param string $params["smarty_include_tpl_file"]
   * @param string $params["smarty_include_vars"]
   */
  function _smarty_include($params) {
    $tpl_file = $params["smarty_include_tpl_file"];
    $vars     = $params["smarty_include_vars"];
    
    // Only at debug time
    if (!CAppUI::pref("showTemplateSpans") || 
        isset($params["smarty_include_vars"]['nodebug']) ||
        in_array(basename($tpl_file), array("login.tpl", "common.tpl", "header.tpl", "footer.tpl", "tabbox.tpl", "ajax_errors.tpl"))) {
      parent::_smarty_include($params);
      return;
    }
    
    $this->showDebugSpans($tpl_file, $params);
    
    echo "\n<!-- Start include: $tpl_file -->\n";
    parent::_smarty_include($params);
    echo "\n<!-- Stop include: $tpl_file -->\n";
  }
    
  /**
   * Delegates the actual translation to CAppUI framework object
   */
  function tr($params, $content, &$smarty, &$repeat) {
    if (isset($content)) {

      // check for the multiple translation
      $vars = array();
      foreach ($params as $key => $value) {
        if(preg_match("/^var\d+/", $key)) {
          $vars[]=$value;
        }
      }
      
      //CAppUI translation
      $content = CAppUI::tr($content,$vars);
      
      foreach ($params as $_key => $_val) {
        switch ($_key) {
          case "escape":
          if ($_val === "JSAttribute"){
            $content = $this->JSAttribute($content);
            break;
          }
  
          $content = smarty_modifier_escape($content, $_val);
          break;
            
          default:
        }
      }
      return $content;
    }
  }
  
  function main($params, $content, &$smarty, &$repeat){
    // Let the whitespace around $content
    return "
      <script type=\"text/javascript\">
        Main.add(function(){ $content });
      </script>";
  }
  
  /**
   * Render an image using phpThumb
   */
  function thumb($params, &$smarty) {
    $finUrl = "";
    foreach ($params as $_key => $_val) {
      if ($_key === "src") {
        $src = urlencode(CAppUI::conf("root_dir")."/".$_val);
      }
      else {
        $finUrl .= ("&amp;$_key=$_val");
      }
    }
    
    return "<img src=\"lib/phpThumb/phpThumb.php?src=$src$finUrl\" />";
  }
  
  /**
   * @author   Pablo Dias <pablo at grafia dot com dot br>
   * @abstract pad a string to a certain length with another string. like php/str_pad
   *
   * Example:  {$text|pad:20:'.':'both'}
   *    will pad $string with dots, in both sides
   *    until $text length equal to 20 characteres
   *    (assuming that $text has less than 20 characteres)
   *
   * @param string $string The string to be padded
   * @param int $length Desired string length
   * @param string $pad_string - string used to pad
   * @param enum $pad_type - both, left or right
   */
  function pad($string, $length, $pad_string = ' ', $pad_type = 'left') {
    static $pads = array(
      'left' => STR_PAD_LEFT, 
      'right'=> STR_PAD_RIGHT, 
      'both' => STR_PAD_BOTH
    );
    return str_pad($string, $length, $pad_string, $pads[$pad_type]);
  } 
  
  /**
   * @abstract JSON encode an object for Javascript use
   *
   * Example:  {$object|json}
   * @param any $object The object to be encoded
   */
  function json($object, $force_object = false) {
    // $options = $force_object ? JSON_FORCE_OBJECT : 0; // Only PHP 5.3 !!
    
    if ($force_object && is_array($object) && empty($object)) {
      return "{}";
    }
    
    return json_encode($object);
  }
  
  /**
   * @abstract Format to ISO DATE
   * Example:  {$datetime|iso_date}
   * @param datetime $datetime The date to format
   */
  function iso_date($datetime) {
    return strftime("%Y-%m-%d", strtotime($datetime));
  }
  
  /**
   * @abstract Format to ISO TIME
   * Example:  {$datetime|iso_time}
   * @param datetime $datetime The date to format
   */
  function iso_time($datetime) {
    return strftime("%H:%M:%S", strtotime($datetime));
  }
  
  /**
   * @abstract Format to ISO DATETIME
   * Example:  {$datetime|iso_datetime}
   * @param datetime $datetime The date to format
   */
  function iso_datetime($datetime) {
    return strftime("%Y-%m-%d %H:%M:%S", strtotime($datetime));
  }
  
  /**
   * @abstract Week number in month to ISO DATETIME
   * Example:  {$datetime|week_number_month}
   * @param datetime $datetime The date to format
   */
  function week_number_month($datetime) {
    return CMbDate::weekNumberInMonth($datetime);
  }
  
  /**
   * Configuration accessor
   * 
   * @param string $path    The configuration path
   * @param object $context The context
   */
  function conf($path, $context = null) {
    return CAppUI::conf($path, $context);
  }
  
  /**
   * Idex loader and accessor
   * 
   * @param CStoredObject $object  The configuration path
   * @param string        $tag     The context
   * 
   * @return string The idex scalar value, empty string if undefined
   */
  function idex($object, $tag = null) {
    return $object->loadLastId400($tag)->id400;
  }
  
  /**
   * @abstract Format to relative datetime 
   * Example:  {$datetime|rel_datetime:$now}
   * @param datetime $datetime The date to format
   */
  function rel_datetime($datetime, $reference = null) {
    if (!$datetime) {
      return;
    }
    $relative = CMbDate::relative(mbDateTime($reference), $datetime);
    return $relative["count"] . " " . CAppUI::tr($relative["unit"] . (abs($relative["count"]) > 1 ? "s" : ""));
  }
  
  /**
   * @abstract Currency format modifier
   *
   * Example:  {$value|currency}
   * @param float $value The value to format
   */
  function currency($value, $decimals = null, $precise = null, $empty = true) {
    if ($decimals == null) {
      $decimals = $precise ? 4 : 2;
    }
    
    // Formatage et symbole monétaire
    $value = ($value !== null && $value !== "") ? 
      number_format($value, $decimals, ",", " ")." ".CAppUI::conf("currency_symbol") : 
      "-";
      
    // Negativité
    $html = $value < 0 ?
      "<span class=\"negative\">$value</span>" :
      $value;
     
    // Nullité 
    $html = $empty && abs($value) < 0.001 ? 
      "<div class=\"empty\">$html</div>" :
      $html;
      
    return $html;
  }
  
  /**
   * @abstract Truncate a string, with a full string titled span if actually truncated 
   *
   * Example:  {$value|spancate}
   * @param float $value The value to format
   */
  function spancate($string, $length = 80, $etc = '...', $break_words = true, $middle = false) {
    CAppUI::requireLibraryFile("smarty/libs/plugins/modifier.truncate");
    $string = html_entity_decode($string);
    $truncated = smarty_modifier_truncate($string, $length, $etc, $break_words, $middle);
    $string = htmlentities($string);
    return strlen($string) > $length ? "<span title=\"$string\">$truncated</span>" : $truncated;
  }
  
  /**
   * @abstract Converts a value to decabinary format
   *
   * Example:  {$value|decabinary}
   * @param float $value The value to format
   */
  function decabinary($value) {
    $decabinary = CMbString::toDecaBinary($value);
    return "<span title=\"$value\">$decabinary</span>";
  }
  
  /**
   * @abstract Percentage 2-digit format modifier
   *
   * Example:  {$value|percent}
   * @param float $value The value to format
   */
  function percent($value) {
    return  !is_null($value) ? number_format($value*100, 2) . "%" : "";
  }
  
  function _const($object, $name) {
    // If the first arg is an instance, we get its class name
    if (!is_string($object)) {
      $object = get_class($object);
    }
    return constant("$object::$name");
  }
  
  function _static($object, $name) {
    if (!is_string($object)) {
      $object = get_class($object);
    }
    
    $class = new ReflectionClass($object);
    $statics = $class->getStaticProperties();
    if (!array_key_exists($name, $statics)) {
      trigger_error("Static variable '$name' for class '$class->name' does not exist", E_USER_WARNING);
      return;
    }
  
    return $statics[$name];
  }
  
  function static_call($callback, $args) {
    $args = func_get_args();
    $callback = array_shift($args);
    $callback = explode("::", $callback);
    return call_user_func_array($callback, $args);
  }
  
  /**
   * @abstract True if the module is installed
   * Example:  {"dPfiles"|module_installed}
   * @param string $module The module name
   */
  function module_installed($module) {
    return CModule::getInstalled($module);
  }
  
  /**
   * @abstract True if the module is active
   * Example:  {"dPfiles"|module_active}
   * @param string $module The module name
   */
  function module_active($module) {
    return CModule::getActive($module);
  }
  
  function JSAttribute($string){
    return str_replace(
      array('\\',   "'",   '"',      "\r",  "\n",  '</'), 
      array('\\\\', "\\'", '&quot;', '\\r', '\\n', '<\/'), 
      $string
    );
  }
  
  function cleanField($string){
    if (!is_scalar($string)) {
      return $string;
    }
  
    return htmlspecialchars($string, ENT_QUOTES);
  }
  
  function stripslashes($string){
    return stripslashes($string);
  }
  
  /**
   * @abstract Emphasize a text, putting <em> nodes around found tokens
   *
   * Example:  {$text|emphasize:$tokens}
   * @param string $text The text subject
   * @param array|string $tokens The string tokens to emphasize, space seperated if string
   */
  function emphasize($text, $tokens, $tag = "em") {
    if (!is_array($tokens)) {
      $tokens = explode(" ", $tokens);
    }
    CMbArray::removeValue("", $tokens);
    
    if (count($tokens) == 0) {
      return $text;
    }
  
    foreach ($tokens as &$token) {
      $token = preg_quote($token);
      $token = CMbString::allowDiacriticsInRegexp($token);
    }
  
    $regexp = str_replace("/", "\\/", implode("|", $tokens));
    return preg_replace("/($regexp)/i", "<$tag>$1</$tag>", $text);  
  }
  
  /**
   * A ternary operator
   * @todo Use this instead of mb_ternary
   * @param object $value The condition
   * @param object $option1 the value if the condition evaluates to true
   * @param object $option2 the value if the condition evaluates to false
   * @return object $option1 or $option2
   */
  function ternary($value, $option1, $option2) {
    return $value ? $option1 : $option2;
  }
  
  /**
   * Trace modifier
   * @param object $value The condition
   * @return void
   */
  function trace($value) {
    mbExport($value);
  }
  
  /**
   * @param array params tableau des parametres
   * Cette fonction prend les mêmes paramètres que mb_field, mais seul object est requis.
   */
  function mb_key($params, &$smarty) {
    $params['field'] = $params["object"]->_spec->key;
    $params['prop'] = 'ref';
    $params['hidden'] = true;
    return $this->mb_field($params, $smarty);
  }
  
  /**
   * Javascript HTML inclusion
   * @param array params 
   * - path   : Direct script file path with extension
   * - script : Script name, without extension, supersedes 'path' and depends on 'module'
   * - module : Module name to find script, if not provided, use global includes
   * @return HTML script node
   */
  function mb_script($params, &$smarty) {
    // Path provided
    $path = CMbArray::extract($params, "path");
    $ajax = CMbArray::extract($params, "ajax");
    
    // Script name providied
    if ($script = CMbArray::extract($params, "script")) {
      if ($module = CMbArray::extract($params, "module")) {
        // dP ugly prefix hack
        $root = CAppUI::conf("root_dir");
        if (!is_dir("$root/modules/$module") && substr($module, 0, 2) != "dP") {
          $module = "dP$module";
        }
      }
  
      $prefix = CMbArray::extract($params, "mobile") ? "mobile/" : "";
      $dir = $module ? $prefix . "modules/$module/javascript" : "includes/javascript";
      $path = "$dir/$script.js";
    }
    
    // Render HTML with build version
    if ($ajax && !empty($smarty->_tpl_vars["ajax"])) {
      $script = file_get_contents($path);
      return "<script type=\"text/javascript\">$script</script>";
    }
    else {
      global $version;
      $version_build = $version['build'];
      return "<script type=\"text/javascript\" src=\"$path?build=$version_build\"></script>";
    }
  }
  
  /**
   * Module/Style aware include alternative
   * @param array params 
   * - module    : Module where template is located, no dP ugly prefix required
   * - style     : Style where template is located
   * - $template : Template name (no extension)
   * @return void
   */
  function mb_include($params, &$smarty) {
    $template = CMbArray::extract($params, "template");
    
    // Module précisé
    if ($module = CMbArray::extract($params, "module")) {
      // dP ugly prefix hack
      $root = CAppUI::conf("root_dir");
      if (!is_dir("$root/modules/$module") && substr($module, 0, 2) != "dP") {
        $module = "dP$module";
      }
      
      $template = "../../../modules/$module/templates/$template";
    }
  
    // Style précisé
    if ($style = CMbArray::extract($params, "style")) {
      $template = "../../../style/$style/templates/$template";
    }
  
    $path = "$template.tpl";
    
    $tpl_vars = $smarty->_tpl_vars;
    $smarty->_smarty_include(array(
      'smarty_include_tpl_file' => $path,
      'smarty_include_vars'     => $params
    ));
    $smarty->_tpl_vars = $tpl_vars;
  }
  
  
  
  /**
   * Assigns a unique id to a variable
   * @param array params 
   * - var: Name of the var
   * @return void
   */
  function unique_id($params, &$smarty) {
    $var = CMbArray::extract($params, "var", null, true);
    // The dot is removed to get valide CSS ID identifiers
    $smarty->assign($var, str_replace(".", "", uniqid("", true)));
  }
  

  /**
   * executes & displays the template results
   *
   * @param string $resource_name
   * @param string $cache_id
   * @param string $compile_id
   */
  function display($resource_name, $cache_id = null, $compile_id = null) {
    // Only at debug time
    if (isset($this->_tpl_vars['nodebug']) ||
        !CAppUI::pref("showTemplateSpans") || 
        in_array(basename($resource_name), array("login.tpl", "common.tpl", "header.tpl", "footer.tpl", "tabbox.tpl", "ajax_errors.tpl"))) {
      parent::display($resource_name, $cache_id, $compile_id);
      return;
    }
    
    $this->showDebugSpans($resource_name, $this->_tpl_vars);
   
    echo "\n<!-- Start display: $resource_name -->\n";
    parent::display($resource_name, $cache_id, $compile_id);
    echo "\n<!-- Stop display: $resource_name -->\n";
  }
}

?>
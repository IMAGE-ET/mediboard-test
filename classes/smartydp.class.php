<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireLibraryFile("smarty/libs/Smarty.class");
CAppUI::requireLibraryFile("smarty/libs/plugins/modifier.escape");

/**
 * Delegates the actual translation to $AppUI framework object
 */
function do_translation($params, $content, &$smarty, &$repeat) {
  if (isset($content)) {
    $content = CAppUI::tr($content);
    
    foreach ($params as $_key => $_val) {
      switch ($_key) {
        case "escape":
        if ($_val=="JSAttribute"){
          $content = JSAttribute($content);
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

/**
 * Render an image using phpThumb
 */
function thumb($params, &$smarty) {
  $finUrl = "";
  foreach ($params as $_key => $_val) {
    if($_key == "src") {
      $src = urlencode(CAppUI::conf("root_dir")."/".$_val);
    } else {
      $finUrl .= ("&amp;$_key=$_val");
    }
  }
  
  return "<img src=\"lib/phpThumb/phpThumb.php?src=$src$finUrl\" alt=\"thumb\" />";
}

/**
  * Smarty plugin
  *
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

function smarty_modifier_pad($string, $length, $pad_string=' ', $pad_type='left') {
  static $pads = array(
    'left' => 0, 
    'right'=> 1, 
    'both' => 2
  );
  return str_pad($string, $length ,$pad_string,$pads[$pad_type]);
} 

function smarty_modifier_json($object) {
  return json_encode($object);
}

function smarty_modifier_const($object, $name) {
	// If the first arg is an instance, we get its class name
	if (!is_string($object)) {
		$object = get_class($object);
	}
  return constant("$object::$name");
}

function smarty_modifier_static($object, $name) {
  if (!is_string($object)) {
    $object = get_class($object);
  }
  
  $class = new ReflectionClass($object);
  $statics = $class->getStaticProperties();
  if (!isset($statics[$name])) {
    trigger_error("Static variable '$name' for class '$class->name' does not exist", E_USER_WARNING);
  }
  else {
  	$static = $statics[$name];
  }
  return $static;
}

function JSAttribute($string){
	return str_replace(
	  array('\\',   "'",   '"',      "\r",  "\n",  '</'), 
		array('\\\\', "\\'", '&quot;', '\\r', '\\n', '<\/'), 
		$string
	);
}

function smarty_modifier_cleanField($string){
  if (!is_scalar($string)) {
    return $string;
  }

  return htmlspecialchars($string,ENT_QUOTES);
}

function smarty_modifier_stripslashes($string){
  return stripslashes($string);
}


/**
 * @param array params tableau des parametres
 * - object          : Objet
 * - field           : Nom du champ a afficher (le champs doit avoir des specs sinon "spec" non optionnel) 
 * - prop            : {optionnel} Specification du champs, par defaut, celle de la classe
 * - separator       : {optionnel} Séparation entre les champs de type "radio" [default: ""]
 * - cycle           : {optionnel} Cycle de répétition du séparateur (pour les enums en type radio) [default: "1"]
 * - typeEnum        : {optionnel} Type d'affichage des enums (values : "select", "radio") [default: "select"]
 * - defaultOption   : {optionnel} Ajout d'un "option" en amont des valeurs ayant pour value ""
 * - class           : {optionnel} Permet de donner une classe aux champs
 * - hidden          : {optionnel} Permet de forcer le type "hidden"
 * - canNull         : {optionnel} Permet de passer outre le notNull de la spécification
 */
function smarty_function_mb_field($params, &$smarty) {
  require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');

  $object  = CMbArray::extract($params, "object", null, true);
  $field   = CMbArray::extract($params, "field" , null, true);
  $propKey = array_key_exists("prop", $params);
  $prop    = CMbArray::extract($params, "prop");
  $canNull = CMbArray::extract($params, "canNull");
 
  $spec = $propKey ?  CMbFieldSpecFact::getSpec($object, $field, $prop) : $object->_specs[$field];
  
  if ($canNull === "true" || $canNull === true) {
    $spec->notNull = 0;
    $tabSpec = split(" ",$spec->prop);
    CMbArray::removeValue("notNull", $tabSpec);
    $spec->prop = implode(" ", $tabSpec);
  }
  
  if ($canNull === "false" || $canNull === false) {
    $spec->notNull = 1;
    $spec->prop = "canNull $spec->prop";
  }
  
  return $spec->getFormElement($object, $params);
}

/**
 * @param array params tableau des parametres
 * Cette fonction prend les mêmes paramètres que smarty_function_mb_field, mais seul object est requis.
 */
function smarty_function_mb_key($params, &$smarty) {
	$params['field'] = $params["object"]->_spec->key;
	$params['prop'] = 'ref';
	$params['hidden'] = true;
  return smarty_function_mb_field($params, $smarty);
}

/**
 * Show a value if different from previous cached one
 * @param array params Smarty parameters
 * - name  : Name of the cached value
 * - value : Value to show, empty string to clear out cache
 */
function smarty_function_mb_ditto($params, &$smarty) {
  static $cache = array();
  $name   = CMbArray::extract($params, "name", null, true);
  $value  = CMbArray::extract($params, "value", null, true);
  $old = CMbArray::get($cache, $name, "");
  $cache[$name] = $value;
  return $old != $value ? $value : "|";
}

/**
 * Fonction that return the value of an object field
 */
function smarty_function_mb_value($params, &$smarty) {
  return $params["object"]->_specs[$params["field"]]->getValue($params["object"], $smarty, $params);
}

/**
 * Fonction d'écriture  des labels
 * @param array params tableau des parametres
 * - object      : Objet
 * - field       : Nom du champ a afficher (le champs doit avoir des specs sinon "spec" non optionnel) 
 * - defaultFor  : {optionnel} Ajout d'une valeur à cibler pour "select" ou "radio"
 * - typeEnum    : {optionnel} Type d'affichage des enums à cibler (values : "select", "radio") [default: "select"]
 */
function smarty_function_mb_label($params, &$smarty) {
  if (null == $object = CMbArray::extract($params, "object")) {
    $class = CMbArray::extract($params, "class" , null, true);
    $object = new $class;
  }
  
  $field = CMbArray::extract($params, "field" , null, true);
  
  if (!array_key_exists($field, $object->_specs)) {
     $object->_specs[$field] = CMbFieldSpecFact::getSpec($object, $field, "");
     trigger_error("Spec missing for class '$object->_class_name' field '$field'", E_USER_WARNING);
  }

  return $object->_specs[$field]->getLabelElement($object, $params);
}

/**
 * Fonction d'écriture  des labels de titre
 * @param array params tableau des parametres
 * - object      : Objet
 * - field       : Nom du champ a afficher (le champs doit avoir des specs sinon "spec" non optionnel) 
 */
function smarty_function_mb_title($params, &$smarty) {
  if (null == $object = CMbArray::extract($params, "object")) {
    $class = CMbArray::extract($params, "class" , null, true);
    $object = new $class;
  }
  
  $field = CMbArray::extract($params, "field" , null, true);

  return $object->_specs[$field]->getTitleElement($object, $params);
}

/**
 * Fonction d'écriture  des labels
 * @param array params 
 * - var   : Name of the new variable
 * - test  : Test for ternary operator 
 * - value : Value if test is true
 * - other : Value if test is false
 */
function smarty_function_mb_ternary($params, &$smarty) {
  $test  = CMbArray::extract($params, "test"  , null, true);
  $value = CMbArray::extract($params, "value" , null, true);
  $other = CMbArray::extract($params, "other" , null, true);
  
  $result =  $test ? $value : $other;
  
  if ($var = CMbArray::extract($params, "var", null)) {
    $smarty->assign($var, $result);
  }
  else {
    return $result;
  }
}

function smarty_function_mb_colonne($params, &$smarty) {
  $class     = CMbArray::extract($params, "class"     , null, true);
  $field     = CMbArray::extract($params, "field"     , null, true);
  $order_col = CMbArray::extract($params, "order_col" , null, true);
  $order_way = CMbArray::extract($params, "order_way" , null, true);
  $url       = CMbArray::extract($params, "url"       , null, true);
  
  $sHtml  = "<label for=\"$field\" title=\"".CAppUI::tr($class."-".$field."-desc")."\">";
  $sHtml .= CAppUI::tr($class."-".$field);
  $sHtml .= "</label>";
    
  $css_class = ($order_col == $field) ? "sorted" : "sortable";
  $order_way_inv = ($order_way == "ASC") ? "DESC" : "ASC";
   
  if($css_class == "sorted"){
  	return "<a class='$css_class $order_way' href='$url&amp;order_col=$order_col&amp;order_way=$order_way_inv'>$sHtml</a>";
  }
  if($css_class == "sortable"){
  	return "<a class='$css_class' href='$url&amp;order_col=$field&amp;order_way=ASC'>$sHtml</a>";
  }
}

function smarty_function_mb_include_script($params, &$smarty) {
  global $version;
  $version_build = $version['build'];
  
  // Dans le cas ou le path est fourni
  $path   = CMbArray::extract($params, "path"  );
  $module = CMbArray::extract($params, "module");
  $script = CMbArray::extract($params, "script");
    
  if($path){
    $path = "$path?build=$version_build";
  }
  if($module XOR $script){
    trigger_error("Module: $module Script: $script");
  }
  if($module && $script){
    $path = "modules/$module/javascript/$script.js?build=$version_build";  
  }
  
  return "<script type='text/javascript' src='$path'></script>";
}

function smarty_function_mb_include($params, &$smarty) {
  // Dans le cas ou le path est fourni
  $module   = CMbArray::extract($params, "module");
  $template = CMbArray::extract($params, "template");
  
  $path = $module ? "../../$module/templates/$template.tpl" : "$template.tpl";
  
	$tpl_vars = $smarty->_tpl_vars;
	$smarty->_smarty_include(array(
	  'smarty_include_tpl_file' => $path,
	  'smarty_include_vars' => $params
	));
	$smarty->_tpl_vars = $tpl_vars;
}

/**
 * dotProject integration of Smarty engine main class
 *
 * Provides an extension of smarty class with directory initialization
 * integrated to dotProject framework as well as standard data assignment
 */
class CSmartyDP extends Smarty {
  /**
   * Construction
   *
   * Directories initialisation
   * Standard data assignment
   */
  function CSmartyDP($rootDir = null) {
    global $AppUI, $version, $dPconfig, $can, $m, $a, $tab, $g, $action, $actionType, $dialog, $ajax, $suppressHeaders;
    
    $root = $dPconfig["root_dir"];

    if (!$rootDir) {
      $rootDir = "$root/modules/$m"; 
    }
    
    // Directories initialisation
    $this->template_dir = "$rootDir/templates/";
    $this->compile_dir  = "$rootDir/templates_c/";
    $this->config_dir   = "$rootDir/configs/";
    $this->cache_dir    = "$rootDir/cache/";
    
    // Debugginf directives
    $this->debug_tpl = "classes/smarty_debug.tpl";
    $this->debugging = false;
    
    // Delimiter definition
    $this->left_delimiter = "{{";
    $this->right_delimiter = "}}";
    
    // Default modifier for security reason
    $this->default_modifiers = array("@cleanField");
    
    // Register mediboard functions
    $this->register_block   ("tr"                , "do_translation"); 
    $this->register_function("thumb"             , "thumb");
    $this->register_function("mb_ditto"          , "smarty_function_mb_ditto");
    $this->register_function("mb_field"          , "smarty_function_mb_field");
    $this->register_function("mb_key"            , "smarty_function_mb_key");
    $this->register_function("mb_value"          , "smarty_function_mb_value");
    $this->register_function("mb_label"          , "smarty_function_mb_label");
    $this->register_function("mb_title"          , "smarty_function_mb_title");
    $this->register_function("mb_ternary"        , "smarty_function_mb_ternary");
    $this->register_function("mb_colonne"        , "smarty_function_mb_colonne");
    $this->register_function("mb_include"        , "smarty_function_mb_include");
    $this->register_function("mb_include_script" , "smarty_function_mb_include_script");
    $this->register_modifier("pad"               , "smarty_modifier_pad");
    $this->register_modifier("json"              , "smarty_modifier_json");
    $this->register_modifier("const"             , "smarty_modifier_const");
    $this->register_modifier("static"            , "smarty_modifier_static");
    $this->register_modifier("cleanField"        , "smarty_modifier_cleanField");
    $this->register_modifier("stripslashes"      , "smarty_modifier_stripslashes");
    $this->register_modifier("JSAttribute"       , "JSAttribute");
    
    $modules = CModule::getActive();
    foreach ($modules as $mod) {
    	$mod->canDo();
    }
    
    // Standard data assignment
    $this->assign("app", $AppUI);
    $this->assign("dPconfig", $dPconfig);
    $this->assign("user", $AppUI->user_id); // shouldn't be necessary
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
    $this->assign("modules",$modules);

  }
  
  /**
   * get a concrete filename for automagically created content
   *
   * @param string $auto_base
   * @param string $auto_source
   * @param string $auto_id
   * @return string
   * @staticvar string|null
   * @staticvar string|null
   */
  function _get_auto_filename($auto_base, $auto_source = null, $auto_id = null)
  {
      $_compile_dir_sep =  $this->use_sub_dirs ? DIRECTORY_SEPARATOR : '^';
      $_return = $auto_base . DIRECTORY_SEPARATOR;

      if(isset($auto_id)) {
          // make auto_id safe for directory names
          $auto_id = str_replace('%7C',$_compile_dir_sep,(urlencode($auto_id)));
          // split into separate directories
          $_return .= $auto_id . $_compile_dir_sep;
      }

      if(isset($auto_source)) {
          // make source name safe for filename
          $_filename = urlencode(basename($auto_source));
          $_crc32 = sprintf('%08X', crc32($auto_source));
          // prepend %% to avoid name conflicts with
          // with $params['auto_id'] names
          $_return .=  "$_filename.%$_crc32%";
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
	  echo "\n<span class='smarty-include ".(isset($params['ajax']) && $params['ajax'] == 1 ? 'ajax' : '')."'>\n$tpl_file";
	  
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
    if (!CAppUI::conf("debug") || 
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
   * executes & displays the template results
   *
   * @param string $resource_name
   * @param string $cache_id
   * @param string $compile_id
   */
  function display($resource_name, $cache_id = null, $compile_id = null) {
    // Only at debug time
    if (!CAppUI::conf("debug") || 
        isset($this->_tpl_vars['nodebug']) ||
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

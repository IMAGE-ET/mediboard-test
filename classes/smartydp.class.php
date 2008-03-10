<?php /* CLASSES $Id$ */
/**
 * @package dotproject
 * @subpackage classes
 * @author Thomas Despoix
 */

require_once($AppUI->getLibraryFile( "smarty/libs/Smarty.class"));
require_once($AppUI->getLibraryFile( "smarty/libs/plugins/modifier.escape"));
require_once($AppUI->getLibraryFile( "json/JSON"));

/**
 * Delegates the actual translation to $AppUI framework object
 */
function do_translation($params, $content, &$smarty, &$repeat) {
  global $dPconfig,$AppUI;

  if (isset($content)) {
    $content = $AppUI->_($content);
    
    foreach ($params as $_key => $_val) {
      switch ($_key) {
        case "escape":
          if($_val=="JSAttribute"){
            $content = JSAttribute($content);
          }else{
            $content = smarty_modifier_escape($content, $_val);
          }
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
  global $AppUI;
  $finUrl = "";
  foreach ($params as $_key => $_val) {
    if($_key == "src") {
      $src = urlencode($AppUI->getConfig("root_dir")."/".$_val);
    } else {
      $finUrl .= ("&amp;$_key=$_val");
    }
  }
  
  
  return "<img src=\"lib/phpThumb/phpThumb.php?src=$src$finUrl\" alt=\"thumb\" />";
}

function smarty_modifier_json($object) {
  if (function_exists("json_encode")) {
    return json_encode($object);
  }
  
  // create a new instance of Services_JSON
  $json = new Services_JSON();
  $sJson = html_entity_decode($json->encode($object),ENT_NOQUOTES);
  
  return strtr($sJson, array("&quot;"=>"\\\""));
}

function smarty_modifier_const($object, $constName) {
  $class = new ReflectionClass($object);
  if (null == $const = $class->getConstant($constName)) {
    trigger_error("Constant '$constName' for class '$class->name' does not exist", E_USER_WARNING);
  }
  
  return $const;
}

function JSAttribute($string){
  return strtr($string, array('\\'=>'\\\\',"'"=>"\\'",'"'=>'&quot;',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/'));
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
  global $AppUI;
  
  require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');
  //mbTrace($params);
  $object  = CMbArray::extract($params, "object", null, true);
  $field   = CMbArray::extract($params, "field" , null, true);
  $propKey = array_key_exists("prop", $params);
  $prop    = CMbArray::extract($params, "prop");
  $canNull = CMbArray::extract($params, "canNull");
 
  $spec = $propKey ?  CMbFieldSpecFact::getSpec($object, $field, $prop) : $object->_specs[$field];

  if ($canNull === true) {
    $spec->notNull = 0;
    $tabSpec = split(" ",$spec->prop);
    CMbArray::extract($tabSpec, "0");
    $spec->prop = join($tabSpec, " ");
  }
  
  if ($canNull === false) {
    $spec->notNull = 1;
    $spec->prop = "notNull $spec->prop";
  }
  
  return $spec->getFormElement($object, $params);
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
  $object     = CMbArray::extract($params, "object", null, true);
  $field      = CMbArray::extract($params, "field" , null, true);
  
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
  $object     = CMbArray::extract($params, "object", null, true);
  $field      = CMbArray::extract($params, "field" , null, true);

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
  global $AppUI;
  
  $var   = CMbArray::extract($params, "var"   , null, true);
  $test  = CMbArray::extract($params, "test"  , null, true);
  $value = CMbArray::extract($params, "value" , null, true);
  $other = CMbArray::extract($params, "other" , null, true);
  
  $smarty->assign($var, $test ? $value : $other);
  
}



function smarty_function_mb_colonne($params, &$smarty) {
  global $AppUI;
  
  $class     = CMbArray::extract($params, "class"     , null, true);
  $field     = CMbArray::extract($params, "field"     , null, true);
  $order_col = CMbArray::extract($params, "order_col" , null, true);
  $order_way = CMbArray::extract($params, "order_way" , null, true);
  $url       = CMbArray::extract($params, "url"       , null, true);
  
  $sHtml  = "<label for=\"$field\" title=\"".$AppUI->_($class."-".$field."-desc")."\">";
  $sHtml .= $AppUI->_($class."-".$field);
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
  global $AppUI, $version;
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
  function CSmartyDP($rootDir = null, $useDataSources = 1) {
    global $AppUI, $version, $dPconfig, $canRead, $can, $canEdit, $canAdmin, $m, $a, $tab, $g, $action, $actionType, $dialog, $ajax;

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
    $this->register_function("mb_field"          , "smarty_function_mb_field");
    $this->register_function("mb_value"          , "smarty_function_mb_value");
    $this->register_function("mb_label"          , "smarty_function_mb_label");
    $this->register_function("mb_title"          , "smarty_function_mb_title");
    $this->register_function("mb_ternary"        , "smarty_function_mb_ternary");
    $this->register_function("mb_colonne"        , "smarty_function_mb_colonne");
    $this->register_function("mb_include_script" , "smarty_function_mb_include_script");
    $this->register_modifier("json"              , "smarty_modifier_json");
    $this->register_modifier("const"             , "smarty_modifier_const");
    $this->register_modifier("cleanField"        , "smarty_modifier_cleanField");
    $this->register_modifier("stripslashes"      , "smarty_modifier_stripslashes");
    $this->register_modifier("JSAttribute"       , "JSAttribute");
    
    // Standard data assignment
    $this->assign("app", $AppUI);
    if($useDataSources) {
      $this->assign("dataSources", CSQLDataSource::$dataSources);
    }
    $this->assign("dPconfig", $dPconfig);
    $this->assign("user", $AppUI->user_id); // shouldn't be necessary
    $this->assign("version", $version); 
    $this->assign("canEdit", $canEdit);
    $this->assign("canRead", $canRead);
    $this->assign("canAdmin", $canAdmin);
    $this->assign("can", $can);
    $this->assign("m", $m);
    $this->assign("a", $a);
    $this->assign("tab", $tab);
    $this->assign("action", $action);
    $this->assign("actionType", $actionType);
    $this->assign("g", $g);
    $this->assign("dialog", $dialog);
    $this->assign("ajax", $ajax);
  }
}
?>

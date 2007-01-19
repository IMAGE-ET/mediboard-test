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
    if ($dPconfig["locale_warn"]){
    	$content = $dPconfig["locale_alert"] . $content . $dPconfig["locale_warn"];
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
      $src = $AppUI->getConfig("root_dir")."/".$_val;
    } else {
      $finUrl .= "&amp;$_key=$_val";
    }
  }
  return "<img src=\"lib/phpThumb/phpThumb.php?src=$src$finUrl\" alt=\"thumb\" />";
}

function smarty_modifier_json($object) {
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
  }else {
    return htmlspecialchars($string,ENT_QUOTES);
  }
}

function smarty_modifier_stripslashes($string){
  return stripslashes($string);
}


function include_script($params, &$smarty) {
    global $m;  

    require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');
    

    $module = $m;
    $source = null;

    foreach ($params as $_key => $_val) {
        switch($_key) {
            case 'module':
                $$_key = $_val;
                break;
            case 'source':
                $$_key = $_val;
                break;
        }
    }

    $_html_result = '';

    return $_html_result;
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
  function CSmartyDP($doubleTagMod = 0) {
    global $AppUI, $dbChronos, $dPconfig, $canRead, $canEdit, $canAdmin, $m, $a, $tab, $g, $action, $actionType, $dialog, $ajax, $mb_version_build;


    $root = $AppUI->getConfig( 'root_dir' );
    
    // Directories initialisation
    $this->template_dir = "$root/modules/$m/templates/";
    $this->compile_dir  = "$root/modules/$m/templates_c/";
    $this->config_dir   = "$root/modules/$m/configs/";
    $this->cache_dir    = "$root/modules/$m/cache/";
    
    // Debugginf directives
    $this->debug_tpl = "$root/classes/smarty_debug.tpl";
    $this->debugging = false;
    
    // Delimiter definition
    if($doubleTagMod) {
      $this->left_delimiter = "{{";
      $this->right_delimiter = "}}";
    }
    
    // Default modifier for security reason
    $this->default_modifiers = array("@cleanField");
    
    // Register mediboard functions
    $this->register_block   ("tr"              , "do_translation"); 
    $this->register_function("thumb"        , "thumb");
    $this->register_modifier("json"         , "smarty_modifier_json");
    $this->register_modifier("const"        , "smarty_modifier_const");
    $this->register_modifier("cleanField"   , "smarty_modifier_cleanField");
    $this->register_modifier("stripslashes" , "smarty_modifier_stripslashes");
    $this->register_modifier("JSAttribute"  , "JSAttribute");
    
    // Standard data assignment
    $this->assign("app", $AppUI);
    $this->assign("dbChronos", $dbChronos);
    $this->assign("dPconfig", $dPconfig);
    $this->assign("user", $AppUI->user_id); // shouldn't be necessary
    $this->assign("canEdit", $canEdit);
    $this->assign("canRead", $canRead);
    $this->assign("canAdmin", $canAdmin);
    $this->assign("m", $m);
    $this->assign("a", $a);
    $this->assign("tab", $tab);
    $this->assign("action", $action);
    $this->assign("actionType", $actionType);
    $this->assign("g", $g);
    $this->assign("dialog", $dialog);
    $this->assign("ajax", $ajax);
    $this->assign("mb_version_build", $mb_version_build);

  }

}
?>

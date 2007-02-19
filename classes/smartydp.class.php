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

function smarty_function_mb_field_spec($obj, $field, $propSpec){
  if(!isset($obj->_specs[$field])){
    $obj->_specs = $obj->getSpecsObj(array($field => $propSpec));
  }
  if($obj->_specs[$field]){
    return $obj->_specs[$field]->checkFieldType();
  }else{
    return null;
  }
}

/**
 * @param datatype paramname
 * 
 */
function smarty_function_mb_field($params, &$smarty){
  global $AppUI;
  
  require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');
  
  $className    = null;
  $extra        = "";
  $_html_result = "";
  $propSpec     = null;
  
  if(!isset($params["object"]) || !isset($params["field"])){
    $smarty->trigger_error("mb_select: attribut 'object' ou 'field' manquant", E_USER_NOTICE);
  }
  
  $value     = $params["object"]->$params["field"];
  $propSpec  = @$params["object"]->_props[$params["field"]];
  $objClass  = $params["object"]->_class_name;
  
  if(isset($params["spec"])) {  $propSpec = $params["spec"];      }
  if(isset($params["class"])){ $className = $params["class"]." "; }
  
  $attribute_oblig = array("type"            => smarty_function_mb_field_spec($params["object"], $params["field"], $propSpec),
                            "element"         => "field",
                            "typeEnum"        => "select",
                            "separator"       => "",
                            "cycle"           => 1,
                            "defaultOption"   => null,
                            "defaultSelected" => "");
  foreach($attribute_oblig as $attrib =>$default){
    if(!isset($params[$attrib])){
      $params[$attrib] = $default;
    }
  }
  if($params["cycle"]<=0){
    $params["cycle"] = 1;
  } 
  // Creation des extra
  foreach($params as $_key =>$_val){
    switch($_key) {
      case "element":
      case "object":
      case "field":
      case "spec":
      case "separator":
      case "cycle":
      case "typeEnum":
      case "defaultOption":
      case "defaultSelected":
        break;
      case "type":
        if(($className !== "" && $className !== null) || ($propSpec !== "" && $propSpec!== null)){
          $extra .= 'class="'.smarty_function_escape_special_chars($className.$propSpec).'" ';
        }
        if(is_scalar($_val) && ($_val == "textarea" || $_val == "enum")){
          break;
        }
      default:
        $extra .= $_key.'="'.smarty_function_escape_special_chars($_val).'" ';
    }
  }

  switch($params["element"]) {
    // Ecriture des champs
    case "field":
      switch($params["type"]){
        case "textarea":
          $_html_result = "<textarea name=\"".smarty_function_escape_special_chars($params["field"])."\" $extra>".smarty_function_escape_special_chars($value)."</textarea>";
          break;
        case "hidden":
        case "text":
          $_html_result = "<input name=\"".smarty_function_escape_special_chars($params["field"])."\" value=\"".smarty_function_escape_special_chars($value)."\" $extra/>";
          break;
        case "radio":
          $iMax = 1;
          for($i=$iMax; $i>=0; $i--){
            $selected = "";
            if(($value && $value == $i) || (!$value && $i == $params["defaultSelected"])){
              $selected = "checked=\"checked\"";
            }
            $_html_result .= "<input name=\"".smarty_function_escape_special_chars($params["field"])."\" value=\"$i\" $selected $extra/>";
            $_html_result .= "<label for=\"".$params["field"]."_$i\">".$AppUI->_("$objClass.".$params["field"].".$i")."</label> ";
            if($i != 0){
              $_html_result .= $params["separator"];
            }
          }
          break;
          
        case "enum":
          $enumsTrans = $params["object"]->_enumsTrans[$params["field"]];
              
          switch($params["typeEnum"]){
            case "select":
              $_html_result = "<select name=\"".smarty_function_escape_special_chars($params["field"])."\" $extra>";
              if($params["defaultOption"] && $params["defaultOption"]!=""){
                $_html_result .= "<option value=\"\">".smarty_function_escape_special_chars($params["defaultOption"])."</option>";
              }
              foreach($enumsTrans as $key => $item){
                if(($value && $value == $key) || (!$value && $key == $params["defaultSelected"])){
                 $selected = " selected=\"selected\""; 
                }else{
                  $selected = "";
                }
                $_html_result .= "<option value=\"$key\"$selected>$item</option>";
              }
              $_html_result .= "</select>";
              break;
              
            case "radio":
              $compteur = 0;
              foreach($enumsTrans as $key => $item){
                if(($value && $value == $key) || (!$value && $key == $params["defaultSelected"])){
                 $selected = " checked=\"checked\""; 
                }else{
                  $selected = "";
                }
                $_html_result .= "<input type=\"radio\" name=\"".smarty_function_escape_special_chars($params["field"])."\" value=\"$key\" $selected $extra/>";
                $_html_result .= "<label for=\"".$params["field"]."_$key\">$item</label> ";
                $compteur++;
                if($compteur % $params["cycle"] == 0){
                  $_html_result .= $params["separator"];
                }
              }
              break;
              
            default:
              $smarty->trigger_error("mb_select: Type d'enumeration '".$params["typeEnum"]."' non pris en charge", E_USER_NOTICE);
          }
          break;
          
        default:
          $smarty->trigger_error("mb_select: Specification '$propSpec' non prise en charge", E_USER_NOTICE);
          break;
      }
      break;

    // Ecriture des labels
    case "label":
      $selected = ""; 
      if($params["defaultSelected"] != ""){
        $selected = "_".$params["defaultSelected"]; 
      }
      $_html_result = "<label for=\"".$params["field"].$selected."\" title=\"".$AppUI->_("$objClass-".$params["field"]."-desc")."\"$extra>$objClass-".$params["field"]."</label>";
      break;
      
    default :
      $smarty->trigger_error("mb_select: attribut 'type' incorrect", E_USER_NOTICE);
  }
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
  function CSmartyDP($rootDir = null) {
    global $AppUI, $dbChronos, $dPconfig, $canRead, $canEdit, $canAdmin, $m, $a, $tab, $g, $action, $actionType, $dialog, $ajax, $mb_version_build;

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
    $this->register_block   ("tr"           , "do_translation"); 
    $this->register_function("thumb"        , "thumb");
    $this->register_function("mb_field"     , "smarty_function_mb_field");
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

<?php /* CLASSES $Id$ */
/**
 * @package dotproject
 * @subpackage classes
 * @author Thomas Despoix
 */

require_once($AppUI->getLibraryClass( "smarty/libs/Smarty.class"));

/**
 * Delegates the actual translation to $AppUI framework object
 */
function do_translation($params, $content, &$smarty, &$repeat) {
  global $AppUI;

  if (isset($content)) {
    return $AppUI->_($content);
  }
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
  function CSmartyDP() {
    global $AppUI, $dbChronos, $dPconfig, $canRead, $canEdit, $m, $a, $tab, $dialog, $mb_version_build;
    $root = $AppUI->getConfig( 'root_dir' );
    
    // Directories initialisation
    $this->template_dir = "$root/modules/$m/templates/";
    $this->compile_dir  = "$root/modules/$m/templates_c/";
    $this->config_dir   = "$root/modules/$m/configs/";
    $this->cache_dir    = "$root/modules/$m/cache/";
    
    // Debugginf directives
    $this->debug_tpl = "$root/classes/smarty_debug.tpl";
    $this->debugging = false;

    // Standard data assignment
    $this->assign("app", $AppUI);
    $this->assign("dbChronos", $dbChronos);
    $this->assign("user", $AppUI->user_id); // shouldn't be necessary
    $this->assign("canEdit", $canEdit);
    $this->assign("canRead", $canRead);
    $this->assign("m", $m);
    $this->assign("a", $a);
    $this->assign("tab", $tab);
    $this->assign("dialog", $dialog);
    $this->assign("mb_version_build", $mb_version_build);
    
    // Configure dotProject localisation framework
    $this->register_block("tr", "do_translation"); 
  }

}
?>

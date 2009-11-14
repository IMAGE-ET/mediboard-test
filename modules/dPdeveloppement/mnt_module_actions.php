<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPdeveloppement
 * @version $Revision$
 * @author openXtrem
 */

global $can, $locales;
$can->needsRead();

$tabs = array();
foreach ($modules = CModule::getInstalled() as $module) {
  CAppUI::requireModuleFile($module->mod_name, "index");
  if (is_array($module->_tabs)) {
    foreach ($module->_tabs as $tab) {
      $tabs[$tab]["name"] = "mod-$module->mod_name-tab-" . $tab;
	    $tabs[$tab]["locale"] = isset($locales[$tabs[$tab]["name"]]) ? 
	    	$locales[$tabs[$tab]["name"]] : null; 
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("module", $modules);
$smarty->assign("tabs"  , $tabs);

$smarty->display("mnt_module_actions.tpl");
?>
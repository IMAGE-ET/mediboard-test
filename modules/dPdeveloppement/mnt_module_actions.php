<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPdeveloppement
 * @version $Revision$
 * @author openXtrem
 */

global $can, $locales;
$can->needsRead();

foreach ($modules = CModule::getInstalled() as $module) {
  CAppUI::requireModuleFile($module->mod_name, "index");
  foreach ($module->_tabs as &$tab) {
    $tab["name"] = "mod-$module->mod_name-tab-" . $tab[0];
    $tab["locale"] = isset($locales[$tab["name"]]) ? 
      $locales[$tab["name"]] : null; 
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("module", $modules);

$smarty->display("mnt_module_actions.tpl");
?>
<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $m;

$can->needsEdit();

require_once("install/libs.php");

CAppUI::getAllClasses();
CModule::loadModules();

$setupClasses = getChildClasses("CSetup");
$mbmodules = array(
  "notInstalled" => array(),
  "installed" => array(),
);
$coreModules = array();
$upgradable = false;

foreach($setupClasses as $setupClass) {
  $setup = new $setupClass;
  $mbmodule = new CModule();
  $mbmodule->compareToSetup($setup);
  $mbmodule->checkModuleFiles();
  
  if ($mbmodule->mod_ui_order == 100) {
    $mbmodules["notInstalled"][$mbmodule->mod_name] = $mbmodule;
  } 
  else {
    $mbmodules["installed"][$mbmodule->mod_name] = $mbmodule;
    if ($mbmodule->_upgradable) {
      $upgradable = true;
    }
  }
  if ($mbmodule->mod_type == "core" && $mbmodule->_upgradable) {
    $coreModules[$mbmodule->mod_name] = $mbmodule;
  }
}

foreach($mbmodules as $typeModules) {
  foreach($typeModules as $module) {
    foreach($module->_dependencies as $version => $dependencies) {
      foreach($dependencies as $dependency) {
      	$installed = $mbmodules["installed"];
        $dependency->verified = isset($installed[$dependency->module]) && $installed[$dependency->module]->mod_version >= $dependency->revision;
      }
    }
  }
}


// Ajout des modules installés dont les fichiers ne sont pas présents
if (count(CModule::$absent)) {
  $mbmodules["installed"] += CModule::$absent;
}

array_multisort(CMbArray::pluck($mbmodules["installed"], "mod_ui_order"), SORT_ASC, $mbmodules["installed"]);

$obsoleteLibs = array();
foreach(CLibrary::$all as $library) { 
  if ($library->getUpdateState() != 1) {
    $obsoleteLibs[] = $library->name;
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("upgradable"  , $upgradable);
$smarty->assign("mbmodules"   , $mbmodules);
$smarty->assign("coreModules" , $coreModules);
$smarty->assign("obsoleteLibs", $obsoleteLibs);

$smarty->display("view_modules.tpl");

?>
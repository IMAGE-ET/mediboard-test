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
	
  if ($mbmodule->mod_ui_order == 100) {
    $mbmodules["notInstalled"][] = $mbmodule;
  } 
  else {
    $mbmodules["installed"][] = $mbmodule;
    if ($mbmodule->_upgradable) {
      $upgradable = true;
    }
  }
  if ($mbmodule->mod_type == "core" && $mbmodule->_upgradable) {
    $coreModules[] = $mbmodule;
  }
}

// Ajout des modules install�s dont les fichiers ne sont pas pr�sents
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

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("upgradable"  , $upgradable);
$smarty->assign("mbmodules"   , $mbmodules);
$smarty->assign("coreModules" , $coreModules);
$smarty->assign("obsoleteLibs", $obsoleteLibs);

$smarty->display("view_modules.tpl");

?>
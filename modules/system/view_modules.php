<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$can->needsEdit();

require_once("install/libs.php");

CAppUI::getAllClasses();
$setupClasses = getChildClasses("CSetup");
$mbmodules = array();
$coreModules = array();
$upgradable = false;
foreach($setupClasses as $setupClass) {
  $setup = new $setupClass;
  $mbmodule = new CModule();
  $mbmodule->compareToSetup($setup);
	
  if ($mbmodule->mod_ui_order == 100) {
    $mbmodules["aInstaller"][] = $mbmodule;
  } else {
    $mbmodules["installe"][] = $mbmodule;
    if ($mbmodule->_upgradable) {
      $upgradable = true;
    }
  }
  if ($mbmodule->mod_type == "core" && $mbmodule->_upgradable) {
    $coreModules[] = $mbmodule;
  }
}

array_multisort(CMbArray::pluck($mbmodules["installe"], "mod_ui_order"), SORT_ASC, $mbmodules["installe"]);

$majLibs = array();
foreach(CLibrary::$all as $library) { 
  if ($library->getUpdateState() != 1) {
    $majLibs[]=$library->name;
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("upgradable"  , $upgradable);
$smarty->assign("mbmodules"   , $mbmodules);
$smarty->assign("coreModules" , $coreModules);

$smarty->assign("majLibs"     , $majLibs);

$smarty->display("view_modules.tpl");

?>
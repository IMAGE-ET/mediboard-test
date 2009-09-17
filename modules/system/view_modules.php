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

CAppUI::getAllClasses();
$setupClasses = getChildClasses("CSetup");
$mbmodules = array();
$coreModules = array();
foreach($setupClasses as $setupClass) {
  $setup = new $setupClass;
  $mbmodule = new CModule();
  $mbmodule->compareToSetup($setup);
	
  if ($mbmodule->mod_ui_order == 100) {
    $mbmodules["aInstaller"][] = $mbmodule;
  } else {
    $mbmodules["installe"][] = $mbmodule;
  }
  if ($mbmodule->mod_type == "core" && $mbmodule->_upgradable) {
    $coreModules[] = $mbmodule;
  }
}

array_multisort(CMbArray::pluck($mbmodules["installe"], "mod_ui_order"), SORT_ASC, $mbmodules["installe"]);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("mbmodules"   , $mbmodules);
$smarty->assign("coreModules" , $coreModules);

$smarty->display("view_modules.tpl");

?>
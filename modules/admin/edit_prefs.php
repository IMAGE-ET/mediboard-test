<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can;

$ds = CSQLDataSource::get("std");

// Utilisateur demandé
$user_id = CValue::get("user_id" , 0);

// Vérification des droit
if ($can->edit){
  $user_id = CValue::getOrSession("user_id", $AppUI->user_id);
  $user_id = intval($user_id);
}
else{
  $user_id = $AppUI->user_id;
}

// Load the preferences
$user = null;
if($user_id !== null){
  $user = new CUser;
  $user->load($user_id);
  $prefs = array_merge(CPreferences::get(0), CPreferences::get($user_id));
}

$prefsUser = array();

// Préférences Globales
$array_list_pref_common = array (
  "LOCALE",
  "UISTYLE",
  "MenuPosition",
  "DEFMODULE",
  "touchscreen",
  "tooltipAppearenceTimeout",
  "showLastUpdate"
);

foreach ($array_list_pref_common as $namePref){
  if (!array_key_exists($namePref,$prefs)) {
    $prefs[$namePref] = null;
  }
  $prefsUser["common"][$namePref] = $prefs[$namePref];
}

// Préférences par Module
$array_list_module_pref = array (
  "dPpatients" => array (
    "DEPARTEMENT",
    "GestionFSE", 
    "InterMaxDir",
    "VitaleVisionDir", 
    "VitaleVision",
    "vCardExport",
  ),
  "dPcabinet" => array (
    "AFFCONSULT",
    "MODCONSULT",
    "AUTOADDSIGN",
    "DefaultPeriod",
    "DossierCabinet",
    "simpleCabinet",
    "ccam_consultation",
    "view_traitement",
    "autoCloseConsult",
    "resumeCompta",
    "showDatesAntecedents",
		"dPcabinet_show_program",
		"pratOnlyForConsult"
  ),
  "dPplanningOp" => array (
    "mode_dhe",
    "dPplanningOp_listeCompacte",
  ),
  "dPhospi" => array (
    "ccam_sejour",
  ),
  "dPcompteRendu" => array(
    "saveOnPrint",
    "choicepratcab"
  ),
  "system" => array (
    "INFOSYSTEM",
		"showTemplateSpans"
  ),
  "dPprescription" => array (
		"easy_mode",
    "show_transmissions_form"
  ),
  "dPurgences" => array (
    "defaultRPUSort",
    "showMissingRPU",
  )
);

foreach($array_list_module_pref as $modulename => $listPrefs){
  $prefsUser[$modulename] = array();	
  $prefModule = CModule::getInstalled($modulename);
  if (($user_id !== 0 && $prefModule && CPermModule::getPermModule($prefModule->mod_id, PERM_READ, $user_id)) || $user_id === 0){
    foreach ($listPrefs as $namePref){
    	if(!array_key_exists($namePref,$prefs)){
    	  $prefs[$namePref] = null;
    	}
      $prefsUser[$modulename][$namePref] = $prefs[$namePref];
    }
  }
}

// Warning: user clone necessary!
// Some module index change $user global
$user_clone = $user;
// Chargement des modules
$modules = CPermModule::getVisibleModules();
foreach ($modules as $module) {
  include("./modules/$module->mod_name/index.php");
}
$user = $user_clone;

// Chargement des languages
$locales = CAppUI::readDirs("locales");
CMbArray::removeValue(".svn", $locales);

// Chargement des styles
$styles = CAppUI::readDirs("style");
CMbArray::removeValue(".svn", $styles);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("user"     , $user);
$smarty->assign("user_id"  , $user_id);
$smarty->assign("locales"  , $locales);
$smarty->assign("styles"   , $styles);
$smarty->assign("modules"  , $modules);
$smarty->assign("prefsUser", $prefsUser);
$smarty->display("edit_prefs.tpl");

?>
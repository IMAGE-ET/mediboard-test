<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Préférences par Module
$prefnames_by_modname = array (
  "common" => array (
    "LOCALE",
    "UISTYLE",
    "MenuPosition",
    "DEFMODULE",
    "touchscreen",
    "tooltipAppearenceTimeout",
    "showLastUpdate",
    "directory_to_watch",
    "debug_yoplet"
  ),
  
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
    "choicepratcab",
    "listDefault",
    "listBrPrefix",
    "listInlineSeparator",
  ),
  
  "dPfiles" => array(
    "directory_to_watch",
    "debug_yoplet"
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
  ),
  
  "ssr" => array (
    "ssr_planning_dragndrop",
    "ssr_planning_resize",
    "ssr_planification_duree",
    "ssr_planification_show_equipement",
  ),
);

global $can;
$user_id = $can->edit ? CValue::getOrSession("user_id", "default") : null;
$user =  CUser::get($user_id);
$prof = $user->profile_id ? CUser::get($user->profile_id) : new CUser;

if ($user_id == "default") {
	$user->_id = "0";
}

$prefvalues = array(
  "default"  => CPreferences::get(0),
  "template" => $user->profile_id ? CPreferences::get($user->profile_id) : array(),
  "user"     => $user->_id !== "" ? CPreferences::get($user->_id       ) : array(),
);

// Classement par module et par préférences
foreach ($prefnames_by_modname as $modname => $prefnames){
  $prefs[$modname] = array();	
  $module = CModule::getActive($modname);
  if ($modname == "common" || $user_id == "default" || CPermModule::getPermModule($module->_id, PERM_READ, $user_id)){
    foreach ($prefnames as $prefname){
      $prefs[$modname][$prefname] = array(
        "default"  => CMbArray::extract($prefvalues["default" ], $prefname),
        "template" => CMbArray::extract($prefvalues["template"], $prefname),
        "user"     => CMbArray::extract($prefvalues["user"    ], $prefname),
			);
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
$smarty->assign("user"   , $user);
$smarty->assign("prof"   , $prof);
$smarty->assign("user_id", $user_id);
$smarty->assign("locales", $locales);
$smarty->assign("styles" , $styles);
$smarty->assign("modules", $modules);
$smarty->assign("prefs"  , $prefs);
$smarty->display("edit_prefs.tpl");

?>
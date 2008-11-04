<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage admin
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $can;
$ds = CSQLDataSource::get("std");
// Utilisateur demandé
$user_id = mbGetValueFromGet("user_id" , 0);

// Vérification des droit
if ($can->edit){
  $user_id = mbGetValueFromGetOrSession("user_id", $AppUI->user_id);
  $user_id = intval($user_id);
}else{
  $user_id = $AppUI->user_id;
}  

// Chargement User demandé
$user = null;
if($user_id!==null){
  $user = new CUser;
  $user->load($user_id);
  
  if($user_id == $AppUI->user_id){
    $prefs = $AppUI->user_prefs;
  }else{
    $sql = "SELECT pref_name, pref_value
          FROM user_preferences
          WHERE pref_user = $user_id";
    $prefs = $ds->loadHashList( $sql );
  }
}

// load the preferences

$prefsUser = array();

// Préférences Globales
$array_list_pref_generale = array (
  "LOCALE",
  "UISTYLE",
  "MenuPosition",
  "DEFMODULE",
);

foreach ($array_list_pref_generale as $namePref){
  if (!array_key_exists($namePref,$prefs)) {
    $prefs[$namePref] = null;
  }
  $prefsUser["GENERALE"][$namePref] = $prefs[$namePref];
}


// Préférences par Module
$array_list_module_pref = array (
  "dPpatients" => array (
    "DEPARTEMENT",
  ),
  "dPcabinet" => array (
    "AFFCONSULT",
    "MODCONSULT",
    "AUTOADDSIGN",
    "GestionFSE", 
    "InterMaxDir", 
    "DefaultPeriod",
    "DossierCabinet",
    "simpleCabinet",
    "ccam_consultation",
    "view_traitement",
    "autoCloseConsult",
    "resumeCompta",
  ),
  "dPplanningOp" => array (
    "mode_dhe",
  ),
  "dPhospi" => array (
    "ccam_sejour",
  ),
  "system" => array (
    "INFOSYSTEM",
  ),
);

foreach($array_list_module_pref as $modulename => $listPrefs){
  $prefsUser[$modulename] = array();	
  $prefModule = CModule::getInstalled($modulename);
  if (($user_id!==0 && $prefModule && CPermModule::getInfoModule("view", $prefModule->mod_id, PERM_READ, $user_id)) || $user_id===0){
    foreach ($listPrefs as $namePref){
    	if(!array_key_exists($namePref,$prefs)){
    	  $prefs[$namePref] = null;
    	}
      $prefsUser[$modulename][$namePref] = $prefs[$namePref];
    }    
  }
}

// Chargement des modules
$modules = CPermModule::getVisibleModules();

// Chargement des languages
$locales = $AppUI->readDirs("locales");
CMbArray::removeValue(".svn", $locales);

// Chargement des styles
$styles = $AppUI->readDirs("style");
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

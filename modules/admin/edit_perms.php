<?php /* $Id: vw_idx_patients.php 783 2006-09-14 12:44:01Z rhum1 $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 783 $
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

$user_id = mbGetValueFromGetOrSession("user_id", $AppUI->user_id);

if(!$user_id) {
  $AppUI->setMsg("Vous devez selectionner un utilisateur");
  $AppUI->redirect("m=$m&tab=vw_edit_users");
}

$modulesInstalled = CModule::getInstalled();
$isAdminPermSet   = false;

$AppUI->getAllClasses();
$listClasses = getChildClasses("CMbObject");

// Récuperation de l'utilisateur sélectionné
$user = new CUser;
$user->load($user_id);

$profile = new CUser();
if($user->profile_id){
  $where["user_id"] = "= '$user->profile_id'";
  $profile->loadObject($where);
}

$order = "mod_id";

//Droit de l'utilisateur sur les modules
$whereUser = array();
$whereUser["user_id"] = "= '$user->user_id'";

$whereProfil = array();
$whereProfil["user_id"] = "= '$user->profile_id'";

// DROITS SUR LES MODULES

// Tabeau recapitulatif des droit sur les modules
$listPermsModuleComplet = array();

// Droits sur l'utilisateur
$permModule = new CPermModule;

// Droit du profil sur les modules
$listPermsModulesProfil = $permModule->loadList($whereProfil, $order);
foreach($listPermsModulesProfil as $keyMod => $mod) {
  $listPermsModulesProfil[$keyMod]->loadRefDBModule();
  $listPermsModuleComplet[$mod->mod_id]["profil"] = $mod;
}


$listPermsModulesUser = $permModule->loadList($whereUser, $order);
foreach($listPermsModulesUser as $keyMod => $mod) {  
  if(!$listPermsModulesUser[$keyMod]->mod_id) {
    $isAdminPermSet = true;
  }
  $listPermsModulesUser[$keyMod]->loadRefDBModule();
  $listPermsModuleComplet[$mod->mod_id]["user"] = $mod;
  
  if(isset($modulesInstalled[$mod->_ref_db_module->mod_name])) {
    unset($modulesInstalled[$mod->_ref_db_module->mod_name]);
  }
}

// DROITS SUR LES OBJETS
$listPermsObjectComplet = array();

$listPermsObjectsUser = array();
$listPermsObjectsProfil = array();
$listPermsObjectsResultat = array();

$order = "object_class";
$permObject = new CPermObject;


// Droit sur le profil
$listPermsObjectsProfil = $permObject->loadList($whereProfil, $order);
foreach($listPermsObjectsProfil as $keyObj => $obj) {
  $listPermsObjectsProfil[$keyObj]->loadRefDBObject();
  $listPermsObjectComplet[$obj->object_id.$obj->object_class]["profil"] = $obj;
}

// Droit sur l'utilisateur
$listPermsObjectsUser = $permObject->loadList($whereUser, $order);
foreach($listPermsObjectsUser as $keyObj => $obj) {
  $listPermsObjectsUser[$keyObj]->loadRefDBObject();
  $listPermsObjectComplet[$obj->object_id.$obj->object_class]["user"] = $obj;
}

$permission = array(PERM_DENY => "interdit",
                     PERM_READ => "lecture",
                     PERM_EDIT => "ecriture");

$visibility = array(PERM_DENY => "caché",
                    PERM_READ => "vue",
                    PERM_EDIT => "administration");

$profileUser = new CUser();
$profilesList = array();
if ($user->profile_id) {
	$profileUser->profile_id = $user->profile_id;
	$profilesList = $profileUser->loadMatchingList('user_first_name');
} else if ($user->template) {
	$profileUser->profile_id = $user->_id;
	$profilesList = $profileUser->loadMatchingList('user_first_name');
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("user"                     , $user                     );
$smarty->assign("modulesInstalled"         , $modulesInstalled         );
$smarty->assign("isAdminPermSet"           , $isAdminPermSet           );
$smarty->assign("listClasses"              , $listClasses              );
$smarty->assign("listPermsModulesUser"     , $listPermsModulesUser     );
$smarty->assign("listPermsModulesProfil"   , $listPermsModulesProfil   );
$smarty->assign("listPermsObjectsUser"     , $listPermsObjectsUser     );
$smarty->assign("listPermsObjectsProfil"   , $listPermsObjectsProfil   );
$smarty->assign("listPermsModuleComplet"   , $listPermsModuleComplet   );
$smarty->assign("listPermsObjectComplet"   , $listPermsObjectComplet   );
$smarty->assign("permission"               , $permission               );
$smarty->assign("visibility"               , $visibility               );
$smarty->assign("profile"                  , $profile                  );
$smarty->assign("profilesList"             , $profilesList             );

$smarty->display("edit_perms.tpl");

?>
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
$typeVue = mbGetValueFromPostOrSession("typeVue","resultat");

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
// Droits sur l'utilisateur
$permModule = new CPermModule;
$listPermsModulesUser = $permModule->loadList($whereUser, $order);
foreach($listPermsModulesUser as $keyMod => $mod) {
  if(!$listPermsModulesUser[$keyMod]->mod_id) {
    $isAdminPermSet = true;
  }
  $listPermsModulesUser[$keyMod]->loadRefDBModule();
  if(isset($modulesInstalled[$mod->_ref_db_module->mod_name])) {
    unset($modulesInstalled[$mod->_ref_db_module->mod_name]);
  }
}

// Droit du profil sur les modules
$listPermsModulesProfil = $permModule->loadList($whereProfil, $order);
foreach($listPermsModulesProfil as $keyMod => $mod) {
  $listPermsModulesProfil[$keyMod]->loadRefDBModule();
}

// Droits resultants de la fusion des droit precedents
$listPermsModulesResultat = $permModule->loadUserPerms($user->_id);
foreach($listPermsModulesResultat as $keyMod => $mod) {
  $listPermsModulesResultat[$keyMod]->loadRefDBModule();
}


// DROITS SUR LES OBJETS
$listPermsObjectsUser = array();
$listPermsObjectsProfil = array();
$listPermsObjectsResultat = array();

$order = "object_class";
$permObject = new CPermObject;

// Droit sur l'utilisateur
$listPermsObjectsUser = $permObject->loadList($whereUser, $order);
foreach($listPermsObjectsUser as $keyObj => $obj) {
  $listPermsObjectsUser[$keyObj]->loadRefDBObject();
}

// Droit sur le profil
$listPermsObjectsProfil = $permObject->loadList($whereProfil, $order);
foreach($listPermsObjectsProfil as $keyObj => $obj) {
  $listPermsObjectsProfil[$keyObj]->loadRefDBObject();
}


// Droits resultants de la fusion des droit precedents
$listPermsObjectsResultat_ = $permObject->loadUserPerms($user->_id);
foreach($listPermsObjectsResultat_ as $classe => $object){
  foreach($object as $key => $object){
    $listPermsObjectsResultat[$object->perm_object_id] = $object;
  }
}

foreach($listPermsObjectsResultat as $keyObj => $obj) {
  $listPermsObjectsResultat[$keyObj]->loadRefDBObject();
}



$permission = array(PERM_DENY => "interdit",
                     PERM_READ => "lecture",
                     PERM_EDIT => "ecriture");

$visibility = array(PERM_DENY => "caché",
                    PERM_READ => "vue",
                    PERM_EDIT => "administration");

                    
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("user"                     , $user                     );
$smarty->assign("modulesInstalled"         , $modulesInstalled         );
$smarty->assign("isAdminPermSet"           , $isAdminPermSet           );
$smarty->assign("listClasses"              , $listClasses              );
$smarty->assign("listPermsModulesUser"     , $listPermsModulesUser     );
$smarty->assign("listPermsModulesProfil"   , $listPermsModulesProfil   );
$smarty->assign("listPermsModulesResultat" ,$listPermsModulesResultat  );
$smarty->assign("listPermsObjectsUser"     , $listPermsObjectsUser     );
$smarty->assign("listPermsObjectsProfil"   , $listPermsObjectsProfil   );
$smarty->assign("listPermsObjectsResultat" , $listPermsObjectsResultat );
$smarty->assign("permission"               , $permission               );
$smarty->assign("visibility"               , $visibility               );
$smarty->assign("typeVue"                  , $typeVue                  );
$smarty->assign("profile"                  , $profile                  );
$smarty->display("edit_perms.tpl");

?>
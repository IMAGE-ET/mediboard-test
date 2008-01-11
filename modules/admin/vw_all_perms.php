<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage admin
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $utypes;

$can->needsAdmin();

// Liste des modules
$listModules = CModule::getActive();

// Liste des utilisateurs
$function = new CFunctions();
$listFunctions = $function->loadListWithPerms(PERM_READ, null, "text");
foreach($listFunctions as &$curr_function) {
  $curr_function->loadRefsUsers();
}

// Matrice des droits
$permission = array(PERM_DENY => "interdit",
                    PERM_READ => "lecture",
                    PERM_EDIT => "ecriture");

$visibility = array(PERM_DENY => "caché",
                    PERM_READ => "vue",
                    PERM_EDIT => "administration");

$where = array();
$whereGeneral = array("mod_id" => "IS NULL");
foreach($listFunctions as $curr_func) {
  foreach($curr_func->_ref_users as $curr_user) {
    $permModule = new CPermModule();
    $whereGeneral["user_id"] = "= '$curr_user->user_id'";
    $where["user_id"]        = "= '$curr_user->user_id'";
    $listPermsModules        = $permModule->loadList($where);
    $where["user_id"]        = "= '$curr_user->_profile_id'";
    $listPermsModulesProfil  = $permModule->loadList($where);
    $permModule->loadObject($whereGeneral);
    if($permModule->_id) {
      $permGeneralPermission = $permModule->permission;
      $permGeneralView       = $permModule->view;
    } else {
      $permGeneralPermission = PERM_DENY;
      $permGeneralView       = PERM_DENY;
    }
    foreach($listModules as $curr_mod) {
      $matrice[$curr_user->_id][$curr_mod->_id] = $permission[$permGeneralPermission]."/".$visibility[$permGeneralView]."\n(général)";
    }
    foreach($listPermsModulesProfil as $curr_perm) {
      $matrice[$curr_user->_id][$curr_perm->mod_id] = $permission[$curr_perm->permission]."/".$visibility[$curr_perm->view]."\n(profil)";
    }
    foreach($listPermsModules as $curr_perm) {
      $matrice[$curr_user->_id][$curr_perm->mod_id] = $permission[$curr_perm->permission]."/".$visibility[$curr_perm->view]."\n(spécifique)";
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listModules"  , $listModules  );
$smarty->assign("listFunctions", $listFunctions);
$smarty->assign("matrice"      , $matrice      );

$smarty->display("vw_all_perms.tpl");

?>
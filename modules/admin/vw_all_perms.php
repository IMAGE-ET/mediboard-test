<?php

/**
 * $Id$
 *
 * @category Admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();
CView::enforceSlave();

// Liste des modules
$listModules = CModule::getActive();

// Liste des utilisateurs
$listFunctions = CGroups::loadCurrent()->loadFunctions(PERM_READ);
foreach ($listFunctions as &$curr_function) {
  $curr_function->loadRefsUsers();
}

// Matrice des droits
$perms = array(
  PERM_DENY => "interdit",
  PERM_READ => "lecture",
  PERM_EDIT => "ecriture"
);

$views = array(
  PERM_DENY => "caché",
  PERM_READ => "menu",
  PERM_EDIT => "administration"
);
              
$icons = array(
  PERM_DENY => "empty",
  PERM_READ => "read",
  PERM_EDIT => "edit"
);

$where = array();
$whereGeneral = array("mod_id" => "IS NULL");
$matrice = array();
foreach ($listFunctions as $curr_func) {
  foreach ($curr_func->_ref_users as $curr_user) {
    $curr_user->loadRefProfile();
    $permModule = new CPermModule();
    
    $whereGeneral["user_id"] = "= '$curr_user->user_id'";
    $where["user_id"]        = "= '$curr_user->user_id'";
    $listPermsModules        = $permModule->loadList($where);
    
    $where["user_id"]        = "= '$curr_user->_profile_id'";
    $listPermsModulesProfil  = $permModule->loadList($where);
    
    $permModule->loadObject($whereGeneral);
    
    if ($permModule->_id) {
      $permGeneralPermission = $permModule->permission;
      $permGeneralView       = $permModule->view;
    }
    else {
      $permGeneralPermission = PERM_DENY;
      $permGeneralView       = PERM_DENY;
    }
    foreach ($listModules as $curr_mod) {
      $matrice[$curr_user->_id][$curr_mod->_id] = array(
        "text"     => $perms[$permGeneralPermission]."/".$views[$permGeneralView],
        "type"     => "général",
        "permIcon" => $icons[$permGeneralPermission],
        "viewIcon" => $icons[$permGeneralView],
      );
    }
    foreach ($listPermsModulesProfil as $curr_perm) {
      $matrice[$curr_user->_id][$curr_perm->mod_id] = array(
        "text"     => $perms[$curr_perm->permission]."/".$views[$curr_perm->view],
        "type"     => "profil",
        "permIcon" => $icons[$curr_perm->permission],
        "viewIcon" => $icons[$curr_perm->view],
      );
    }
    foreach ($listPermsModules as $curr_perm) {
      $matrice[$curr_user->_id][$curr_perm->mod_id] = array(
        "text"     => $perms[$curr_perm->permission]."/".$views[$curr_perm->view],
        "type"     => "spécifique",
        "permIcon" => $icons[$curr_perm->permission],
        "viewIcon" => $icons[$curr_perm->view],
      );
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listModules"  , $listModules  );
$smarty->assign("listFunctions", $listFunctions);
$smarty->assign("matrice"      , $matrice      );

$smarty->display("vw_all_perms.tpl");

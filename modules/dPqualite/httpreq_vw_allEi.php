<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPqualite
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $can, $AppUI;
$can->needsRead();

$type              = mbGetValueFromGetOrSession("type");
$first             = mbGetValueFromGetOrSession("first");
$selected_user_id  = mbGetValueFromGetOrSession("selected_user_id");
$selected_service_valid_user_id = mbGetValueFromGetOrSession("selected_service_valid_user_id");
$selected_fiche_id = mbGetValueFromGetOrSession("selected_fiche_id");
$elem_concerne     = mbGetValueFromGet("elem_concerne");
$evenements        = mbGetValueFromGet("evenements");

$listUsersTermine = new CMediusers;

$user_id = null;
$where = array();
if ($elem_concerne) {
  $where["elem_concerne"] = "= '$elem_concerne'";
}

if($type == "AUTHOR" || ($can->edit && !$can->admin)){
  $user_id = $AppUI->user_id;
}

$list_service_valid_users = new CMediusers;
if($type == "ALL_TERM" && $can->admin){
  $listUsersTermine = $listUsersTermine->loadListFromType();
  if($selected_user_id){
    $where["fiches_ei.user_id"] = "= '$selected_user_id'";
  }
  
  if($selected_service_valid_user_id){
    $where["fiches_ei.service_valid_user_id"] = "= '$selected_service_valid_user_id'";
  }
  
  // Chargement de la liste des Chef de services / utilisateur
  $module = CModule::getInstalled("dPqualite");
  $perm = new CPermModule;
  $list_service_valid_users = $list_service_valid_users->loadListFromType(null, PERM_READ);
  foreach($list_service_valid_users as $keyUser => $infoUser){
    if(!$perm->getInfoModule("permission", $module->mod_id, PERM_EDIT, $keyUser)){
      unset($list_service_valid_users[$keyUser]);
    }
  }
}

$countFiches = CFicheEi::loadFichesEtat($type, $user_id, $where, 0, true);
$listeFiches = CFicheEi::loadFichesEtat($type, $user_id, $where, 0, false, $countFiches > 20 ? $first : null);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listUsersTermine" , $listUsersTermine);
$smarty->assign("list_service_valid_users", $list_service_valid_users);
$smarty->assign("listeFiches"      , $listeFiches);
$smarty->assign("countFiches"      , $countFiches);
$smarty->assign("type"             , $type);
$smarty->assign("first"            , $first);
$smarty->assign("selected_user_id" , $selected_user_id);
$smarty->assign("selected_service_valid_user_id" , $selected_service_valid_user_id);
$smarty->assign("selected_fiche_id", $selected_fiche_id);

$smarty->display("inc_ei_liste.tpl");

?>
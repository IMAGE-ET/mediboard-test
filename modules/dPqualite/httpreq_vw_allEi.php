<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPqualite
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $can, $AppUI;
$can->needsEdit();

$selected_user_id = mbGetValueFromGetOrSession("selected_user_id");
$type = mbGetValueFromGetOrSession("type");
$first = mbGetValueFromGetOrSession("first");

$user_id = null;
$listUsersTermine = new CMediusers;
if ($type == "ALL_TERM") {
  $listUsersTermine = $listUsersTermine->loadListFromType();
  $user_id = $selected_user_id;
}

$userNotAdmin = ($can->edit && !$can->admin);

if ($userNotAdmin || $type == "AUTHOR") {
  $user_id = $AppUI->user_id;
}

$where = array();
if($user_id && $userNotAdmin){
  $where["fiches_ei.user_id"] = "= '$user_id'";
}

$countFiches = CFicheEi::loadFichesEtat($type, $user_id, $where, 0, true);
$listeFiches = CFicheEi::loadFichesEtat($type, $user_id, $where, 0, false, $countFiches > 20 ? $first : null);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listUsersTermine" , $listUsersTermine);
$smarty->assign("listeFiches"      , $listeFiches);
$smarty->assign("countFiches"      , $countFiches);
$smarty->assign("type"             , $type);
$smarty->assign("first"            , $first);
$smarty->assign("selected_user_id" , $selected_user_id);

$smarty->display("inc_ei_liste.tpl");

?>
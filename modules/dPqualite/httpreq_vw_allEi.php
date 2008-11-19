<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPqualite
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $can;
$can->needsAdmin();

$allEi_user_id   = mbGetValueFromGetOrSession("allEi_user_id", null);

$listUsersTermine = new CMediusers;
$listUsersTermine = $listUsersTermine->loadListFromType();

$where_allei = array();
if($allEi_user_id){
  $where_allei["fiches_ei.user_id"] = "= '$allEi_user_id'";
}

$listeFiches = CFicheEi::loadFichesEtat("ALL_TERM", null, $where_allei);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listUsersTermine" , $listUsersTermine);
$smarty->assign("listeFiches"      , $listeFiches);
$smarty->assign("allEi_user_id"    , $allEi_user_id);
$smarty->assign("voletAcc"         , "ALL_TERM");
$smarty->assign("listeFichesTitle" , null);
$smarty->assign("reloadAjax"       , true);

$smarty->display("inc_ei_liste.tpl");


?>
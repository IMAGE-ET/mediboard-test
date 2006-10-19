<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPqualite
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $canAdmin, $m, $g;

if (!$canAdmin) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$allEi_user_id   = mbGetValueFromGetOrSession("allEi_user_id",null);

$listUsersTermine = new CMediusers;
$listUsersTermine = $listUsersTermine->loadListFromType();

$where_allei = array();
if($allEi_user_id){
  $where_allei["user_id"] = db_prepare("= %",$allEi_user_id);
}

$listeFiches = CFicheEi::loadFichesEtat("ALL_TERM",null,$where_allei);


// Création du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("listUsersTermine" , $listUsersTermine);
$smarty->assign("listeFiches"      , $listeFiches);
$smarty->assign("allEi_user_id"    , $allEi_user_id);
$smarty->assign("voletAcc"         , "ALL_TERM");
$smarty->assign("listeFichesTitle" , null);
$smarty->assign("reloadAjax"       , true);

$smarty->display("inc_ei_liste.tpl");


?>
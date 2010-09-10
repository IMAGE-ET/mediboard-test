<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain OLLIVIER
*/


CCanDo::checkRead();

// Utilisateur sélectionné ou utilisateur courant
$user_id = CValue::getOrSession("filter_user_id", CAppUI::$user->_id);
$filter_class = CValue::getOrSession("filter_class");

$userSel = new CMediusers;
$userSel->load($user_id ? $user_id : CAppUI::$user->_id);
$userSel->loadRefs();
$userSel->_ref_function->loadRefsFwd();

if (!$userSel->isPraticien()) {
  $userSel->load(null);
}

// pack sélectionné
$pack_id = CValue::getOrSession("pack_id");


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("user_id"       , $user_id);
$smarty->assign("filter_class"  , $filter_class);
$smarty->assign("pack_id"       , $pack_id);

$smarty->display("vw_idx_packs.tpl");

?>
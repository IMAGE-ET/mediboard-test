<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Thomas Despoix
*/

global $dialog;
if ($dialog) {
  CCanDo::checkRead();
} 
else {
  CCanDo::checkEdit();
}

// L'utilisateur est-il chirurgien ?
$mediuser      = CAppUI::$instance->_ref_user;
$is_praticien  = $mediuser->isPraticien();
$listPrat      = $mediuser->loadPraticiens(PERM_EDIT);
$chir_id       = CValue::getOrSession("chir_id", $is_praticien ? $mediuser->user_id : reset($listPrat)->_id);
$_function     = new CFunctions();
$listFunc      = $_function->loadSpecialites(PERM_EDIT);
$type          = CValue::getOrSession("type", "interv");
$page          = CValue::get("page", array(
  "sejour" => 0,
  "interv" => 0,
));

// Protocoles disponibles
$_prat = new CMediusers();
foreach($listPrat as $_prat) {
  $_prat->loadProtocoles();
}
foreach($listFunc as $_function) {
  $_function->loadProtocoles();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("page"        , $page);
$smarty->assign("listPrat"    , $listPrat);
$smarty->assign("listFunc"    , $listFunc);
$smarty->assign("chir_id"     , $chir_id);
$smarty->assign("mediuser"    , $mediuser);
$smarty->assign("is_praticien", $is_praticien);

$smarty->display("vw_protocoles.tpl");

?>
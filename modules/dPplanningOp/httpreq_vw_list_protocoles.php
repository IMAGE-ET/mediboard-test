<?php /* $Id: vw_protocoles.php 7210 2009-11-03 12:18:57Z rhum1 $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: 7210 $
* @author Thomas Despoix
*/

global $dialog;

if($dialog) {
  CCanDo::checkRead();
} else {
  CCanDo::checkEdit();
}

// L'utilisateur est-il chirurgien?
$mediuser = CMediusers::get();
$chir_id  = CValue::getOrSession("chir_id", $mediuser->isPraticien() ? $mediuser->user_id : null);
$chir     = new CMediusers();
$chir->load($chir_id);

$type         = CValue::getOrSession("type", "interv"); 
$page         = CValue::get("page");

$protocole = new CProtocole;
$where = array();

if($chir->_id) {
  $where[] = "protocole.chir_id = '$chir->_id' OR protocole.function_id = '$chir->function_id'";
}

if ($type == 'interv') {
  $where["for_sejour"] = "= '0'";
}
else {
	$where["for_sejour"] = "= '1'";
}

$ljoin = array(
  "users" => "users.user_id = protocole.chir_id"
);

$order = "libelle, libelle_sejour, codes_ccam";

$list_protocoles       = $protocole->loadList($where, $order, "{$page[$type]},40", null, $ljoin);
$list_protocoles_total = $protocole->loadList($where, $order, null, null, $ljoin);

$total_protocoles = $protocole->countList($where, null, $ljoin);

foreach ($list_protocoles as $_prot){
  $_prot->loadRefsFwd();
}

foreach ($list_protocoles_total as $_prot) {
  $_prot->loadRefsFwd();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("list_protocoles"      , $list_protocoles);
$smarty->assign("list_protocoles_total", $list_protocoles_total);
$smarty->assign("total_protocoles"     , $total_protocoles);
$smarty->assign("page"                 , $page);
$smarty->assign("chir_id"              , $chir_id);
$smarty->assign("type"                 , $type);

$smarty->display("inc_list_protocoles.tpl");

?>
<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

// Utilisateur sélectionné ou utilisateur courant
$user_id = mbGetValueFromGetOrSession("filter_user_id", $AppUI->user_id);

$userSel = new CMediusers;
$userSel->load($user_id ? $user_id : $AppUI->user_id);
$userSel->loadRefs();

if (!$userSel->isPraticien()) {
  $userSel->load(null);
}

// Utilisateurs modifiables
$users = new CMediusers;
$users = $users->loadPraticiens(PERM_EDIT);

// Filtres sur la liste des packs
$where["chir_id"] = $userSel->user_id ? 
  "= '$userSel->user_id'" : 
  db_prepare_in(array_keys($users));

$packs = new CPack();
$packs = $packs->loadList($where);
foreach($packs as $key => $value) {
  $packs[$key]->loadRefsFwd();
}

// Récupération des modèles
$whereCommon = array();
$whereCommon = array();
$whereCommon["object_id"] = "IS NULL";
$whereCommon["object_class"] = "= 'COperation'";
$order = "nom";

// Modèles de l'utilisateur
$listModelePrat = array();
if ($userSel->user_id) {
  $where = $whereCommon;
  $where["chir_id"] = db_prepare("= %", $userSel->user_id);
  $listModelePrat = new CCompteRendu;
  $listModelePrat = $listModelePrat->loadlist($where, $order);
}

// Modèles de la fonction
$listModeleFunc = array();
if ($userSel->user_id) {
  $where = $whereCommon;
  $where["function_id"] = db_prepare("= %", $userSel->function_id);
  $listModeleFunc = new CCompteRendu;
  $listModeleFunc = $listModeleFunc->loadlist($where, $order);
}

// pack sélectionné
$pack_id = mbGetValueFromGetOrSession("pack_id");
$pack = new CPack();
$pack->load($pack_id);
if($pack_id) {
  $pack->loadRefsFwd();
} else {
  $pack->chir_id = $userSel->user_id;
}

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("users"         , $users);
$smarty->assign("user_id"       , $user_id);
$smarty->assign("listModelePrat", $listModelePrat);
$smarty->assign("listModeleFunc", $listModeleFunc);
$smarty->assign("packs"         , $packs);
$smarty->assign("pack"          , $pack);

$smarty->display("vw_idx_packs.tpl");

?>
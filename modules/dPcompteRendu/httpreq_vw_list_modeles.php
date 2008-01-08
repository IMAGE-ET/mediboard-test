<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision:  $
* @author Alexis Granger
*/

global $AppUI, $can, $m;

$ds = CSQLDataSource::get("std");

$object_class = mbGetValueFromGetOrSession("object_class");
$user_id = mbGetValueFromGetOrSession("user_id", $AppUI->user_id);
$pack_id = mbGetValueFromGetOrSession("pack_id");

$pack = new CPack();
$pack->load($pack_id);
if($pack_id) {
  $pack->loadRefsFwd();
} else {
  $pack->chir_id = $userSel->user_id;
}

$userSel = new CMediusers;
$userSel->load($user_id);
$userSel->loadRefs();

$whereCommon["object_id"] = "IS NULL";
$whereCommon["object_class"] = "= '$object_class'";
$order = "nom";

// Modèles de l'utilisateur
$listModelePrat = array();
if ($userSel->user_id) {
  $where = $whereCommon;
  $where["chir_id"] = $ds->prepare("= %", $userSel->user_id);
  $listModelePrat = new CCompteRendu;
  $listModelePrat = $listModelePrat->loadlist($where, $order);
}

// Modèles de la fonction
$listModeleFunc = array();
if ($userSel->user_id) {
  $where = $whereCommon;
  $where["function_id"] = $ds->prepare("= %", $userSel->function_id);
  $listModeleFunc = new CCompteRendu;
  $listModeleFunc = $listModeleFunc->loadlist($where, $order);
}

// Création du template

$smarty = new CSmartyDP();

$smarty->assign("listModelePrat"   , $listModelePrat);
$smarty->assign("listModeleFunc"   , $listModeleFunc);
$smarty->assign("pack"             , $pack          );

$smarty->display("inc_list_modeles.tpl");

?>
<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision:  $
* @author Alexis Granger
* @abstract Permet de choisir des modèles pour constituer des packs
*/

global $AppUI, $can, $m;

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

// Modèles de l'utilisateur
$listModelePrat = array();
$listModeleFunc = array();
if ($userSel->user_id) {
  $listModelePrat = CCompteRendu::loadModelesForPrat($object_class, $userSel->user_id);
  $listModeleFunc = CCompteRendu::loadModelesForFunc($object_class, $userSel->function_id);
  
}

// Création du template

$smarty = new CSmartyDP();

$smarty->assign("listModelePrat"   , $listModelePrat);
$smarty->assign("listModeleFunc"   , $listModeleFunc);
$smarty->assign("pack"             , $pack          );

$smarty->display("inc_list_modeles.tpl");

?>
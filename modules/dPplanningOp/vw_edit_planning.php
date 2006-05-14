<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('dPplanningOp', 'planning') );
require_once( $AppUI->getModuleClass('mediusers') );
require_once( $AppUI->getModuleClass('dPpatients', 'patients') );
require_once( $AppUI->getModuleClass('dPcompteRendu', 'pack') );

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

$operation_id = mbGetValueFromGetOrSession("operation_id", 0);
$chir_id = mbGetValueFromGetOrSession("chir_id", null);
$pat_id = dPgetParam($_GET, "pat_id");
$trans = dPgetParam($_GET, "trans", 0);
$chir = new CMediusers;
$pat = new CPatient;

// L'utilisateur est-il un praticiens
$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);
if ($mediuser->isPraticien()) {
  $chir = $mediuser;
}

// Vérification des droits sur les praticiens
$listChir = $mediuser->loadPraticiens(PERM_EDIT);
// A t'on fourni l'id du patient et du chirurgien?
if ($chir_id) {
  $chir->load($chir_id);
}
if ($pat_id) {
  $pat->load($pat_id);
}

// On récupère l'opération
$op = new COperation;
if ($operation_id) {
  $op->load($operation_id);
  // On vérifie qu'il y a bien une intervention
  if(!$op->plageop_id && !$trans) {
  	mbSetValueToSession("operation_id", 0);
    $AppUI->redirect( "m=$m&tab=vw_edit_hospi&hospitalisation_id=$op->operation_id" );
  }
  // On vérifie que l'utilisateur a les droits sur l'operation
  $right = false;
  foreach($listChir as $key => $value) {
    if($value->user_id == $op->chir_id)
      $right = true;
  }
  if(!$right) {
    $AppUI->setMsg("Vous n'avez pas accès à cette intervention", UI_MSG_ALERT);
    $AppUI->redirect( "m=dPpatients&tab=0&id=$op->pat_id");
  }
  $op->loadRefs();
}

// Récupération des modèles
$whereCommon = array();
$whereCommon["type"] = "= 'hospitalisation'";
$order = "nom";

// Modèles de l'utilisateur
$listModelePrat = array();
if ($op->chir_id) {
  $where = $whereCommon;
  $where["chir_id"] = "= '".$op->_ref_chir->user_id."'";
  $listModelePrat = new CCompteRendu;
  $listModelePrat = $listModelePrat->loadlist($where, $order);
}

// Modèles de la fonction
$listModeleFunc = array();
if ($op->chir_id) {
  $where = $whereCommon;
  $where["function_id"] = "= '".$op->_ref_chir->function_id."'";
  $listModeleFunc = new CCompteRendu;
  $listModeleFunc = $listModeleFunc->loadlist($where, $order);
}

// Packs d'hospitalisation
$listPack = array();
if($op->chir_id) {
  $where = array();
  $where["chir_id"] = "= '".$op->_ref_chir->user_id."'";
  $listPack = new CPack;
  $listPack = $listPack->loadlist($where, $order);
}

// Heures & minutes
$start = 7;
$stop = 20;
$step = 15;

for ($i = $start; $i < $stop; $i++) {
    $hours[] = $i;
}

for ($i = 0; $i < 60; $i += $step) {
    $mins[] = $i;
}

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('protocole', false);
$smarty->assign('hospitalisation', false);

$smarty->assign('op', $op);
$smarty->assign('chir' , $op->chir_id    ? $op->_ref_chir    : $chir);
$smarty->assign('pat'  , $op->pat_id     ? $op->_ref_pat     : $pat );
$smarty->assign('plage', $op->plageop_id ? $op->_ref_plageop : new CPlageop );

$smarty->assign('listModelePrat', $listModelePrat);
$smarty->assign('listModeleFunc', $listModeleFunc);
$smarty->assign('listPack'      , $listPack      );

$smarty->assign('hours', $hours);
$smarty->assign('mins', $mins);

$smarty->display('vw_addedit_planning.tpl');

?>
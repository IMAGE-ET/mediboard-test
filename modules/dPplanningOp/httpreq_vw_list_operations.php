<?php /* $Id: $*/

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: $
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m, $g, $dPconfig;

$can->needsRead();

$date = mbGetValueFromGetOrSession("date", mbDate());
$chirSel = mbGetValueFromGetOrSession("chirSel", $AppUI->user_id);
$userSel = new CMediusers;
$userSel->load($chirSel);
$board = mbGetValueFromGet("board", 0);
$boardItem = mbGetValueFromGet("boardItem", 0);

// Urgences du jour
$listUrgences = array();
$operation = new COperation();
if($userSel->_id){
	$operation->date = $date;
	$operation->chir_id = $userSel->_id;
  $listUrgences = $operation->loadMatchingList("date");
	foreach($listUrgences as &$curr_urg) {
	  $curr_urg->loadRefsFwd();
		$curr_urg->loadRefsDocs();
	  $curr_urg->_ref_sejour->loadRefsFwd();
		$curr_urg->_ref_sejour->loadRefsDocs();
	}
}
// Liste des opérations du jour sélectionné
$listDay = array();
$plageOp = new CPlageOp();
if($userSel->_id){
	$plageOp->date = $date;
	$plageOp->chir_id = $userSel->_id;
  $listDay = $plageOp->loadMatchingList("debut");
	foreach ($listDay as &$curr_plage) {
	  $curr_plage->loadRefs();
	  foreach ($curr_plage->_ref_operations as &$curr_op) {
	    $curr_op->loadRefsFwd();
			$curr_op->loadRefsDocs();
	    $curr_op->_ref_sejour->loadRefsFwd();
			$curr_op->_ref_sejour->loadRefsDocs();
	  }
	}
}

// Praticien concerné
if ($AppUI->_ref_user->isPraticien()) {
  $praticien = $AppUI->_ref_user;
}
else {
  $praticien = new CMediusers();
  $praticien->load(mbGetValueFromGetOrSession("chirSel", mbGetValueFromGetOrSession("praticien_id")));
}

$praticien->loadRefFunction();
$praticien->_ref_function->loadRefGroup();
$praticien->canDo();

// Modèles du praticien
$modelesByOwner = array(
  'COperation' => array(),
  'CSejour' => array()
);
$packs = array(
  'COperation' => array(),
  'CSejour' => array()
);
if ($praticien->_can->edit) {
	foreach($modelesByOwner as $object_class => &$modeles) {
	  $modeles = CCompteRendu::loadAllModelesFor($praticien->_id, 'prat', $object_class, "body");

	  // Chargement des packs
	  $pack = new CPack();
	  $pack->object_class = $object_class;
	  $pack->chir_id = $praticien->_id;
	  $packs[$object_class] = $pack->loadMatchingList("nom");
	}
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("modelesByOwner", $modelesByOwner);
$smarty->assign("packs"         , $packs);
$smarty->assign("praticien"     , $praticien);
$smarty->assign("boardItem"   , $boardItem);
$smarty->assign("date"        , $date);
$smarty->assign("listUrgences", $listUrgences);
$smarty->assign("listDay"     , $listDay);
$smarty->assign("board"       , $board);

$smarty->display("inc_list_operations.tpl");

?>
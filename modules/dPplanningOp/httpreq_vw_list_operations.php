<?php /* $Id$*/

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m, $g;

$can->needsRead();
$date = CValue::getOrSession("date", mbDate());
$pratSel = CValue::getOrSession("pratSel", $AppUI->user_id);
$userSel = new CMediusers;
$userSel->load($pratSel);
$board = CValue::get("board", 0);
$boardItem = CValue::get("boardItem", 0);

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
  $userSel->loadBackRefs("secondary_functions");
  $secondary_specs = array();
  foreach($userSel->_back["secondary_functions"] as  $curr_sec_spec) {
    $curr_sec_spec->loadRefsFwd();
    $curr_function = $curr_sec_spec->_ref_function;
    $secondary_specs[$curr_function->_id] = $curr_function;
  }
  $where = array();
  $where["date"] = "= '$date'";
  $where[] = "plagesop.chir_id = '$userSel->_id' OR plagesop.spec_id = '$userSel->function_id' OR plagesop.spec_id ".CSQLDataSource::prepareIn(array_keys($secondary_specs));
  $order = "debut, salle_id";
  $listDay = $plageOp->loadList($where, $order);
  foreach ($listDay as &$curr_plage) {
    $curr_plage->loadRefs();
    foreach ($curr_plage->_ref_operations as $key_op => &$curr_op) {
      if($curr_op->chir_id != $userSel->_id) {
        unset($curr_plage->_ref_operations[$key_op]);
      } else {
        $curr_op->loadRefsFwd();
        $curr_op->loadRefsDocs();
        $curr_op->_ref_sejour->loadRefsFwd();
        $curr_op->_ref_sejour->loadRefsDocs();
      }
    }
  }
}

// Praticien concerné
if ($AppUI->_ref_user->isPraticien()) {
  $praticien = $AppUI->_ref_user;
}
else {
  $praticien = new CMediusers();
  $praticien->load(CValue::getOrSession("pratSel", CValue::getOrSession("praticien_id")));
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
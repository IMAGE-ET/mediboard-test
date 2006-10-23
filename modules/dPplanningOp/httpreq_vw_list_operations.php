<?php /* $Id: $*/

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: $
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m, $g, $dPconfig;

if(!$canRead) {
  $AppUI->redirect("m=system&a=access_denied");
}

$date = mbGetValueFromGetOrSession("date", mbDate());
$userSel = new CMediusers;
$userSel->load($AppUI->user_id);
$urgences = mbGetValueFromGetOrSession("urgences", 0);
$board = mbGetValueFromGet("board", 0);

if($urgences) {
  $listDay = array();
  // Urgences du mois
  $listUrgences = new COperation;
  $where = array();
  $where["date"] = "LIKE '".mbTranformTime("+ 0 day", $date, "%Y-%m")."-__'";
  $where["chir_id"] = "= '$selChirLogin'";
  $order = "date";
  $listUrgences = $listUrgences->loadList($where, $order);
  if($urgences) {
    foreach($listUrgences as $keyUrg => $curr_urg) {
      $listUrgences[$keyUrg]->loadRefs();
      $listUrgences[$keyUrg]->_ref_sejour->loadRefsFwd();
    }
  }
} else {
  $listUrgences = array();
  // Liste des opérations du jour sélectionné
  $listDay = new CPlageOp;
  $where = array();
  $where["date"] = "= '$date'";
  $where["chir_id"] = "= '$userSel->user_id'";
  $order = "debut";
  $listDay = $listDay->loadList($where, $order);
  foreach($listDay as $key => $value) {
    $listDay[$key]->loadRefs();
    foreach($listDay[$key]->_ref_operations as $key2 => $value2) {
      $listDay[$key]->_ref_operations[$key2]->loadRefs();
      $listDay[$key]->_ref_operations[$key2]->_ref_sejour->loadRefsFwd();
    }
  }
}

// récupération des modèles de compte-rendu disponibles
$where = array();
$order = "nom";
$where["object_class"] = "= 'COperation'";
$where["chir_id"] = "= '$userSel->user_id'";
$crList    = CCompteRendu::loadModeleByCat("Opération", $where, $order, true);
$hospiList = CCompteRendu::loadModeleByCat("Hospitalisation", $where, $order, true);

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("date"        , $date);
$smarty->assign("crList"      , $crList);
$smarty->assign("hospiList"   , $hospiList);
$smarty->assign("listUrgences", $listUrgences);
$smarty->assign("listDay"     , $listDay);
$smarty->assign("urgences"    , $urgences);
$smarty->assign("board"       , $board);

$smarty->display("inc_list_operations.tpl");

?>
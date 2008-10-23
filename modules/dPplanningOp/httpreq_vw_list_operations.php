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

$listUrgences = array();
// Urgences du jour
$listUrgences = new COperation;
$where = array();
$where["date"] = "= '$date'";
$where["chir_id"] = "= '$userSel->user_id'";
$order = "date";
$listUrgences = $listUrgences->loadList($where, $order);
foreach($listUrgences as &$curr_urg) {
  $curr_urg->loadRefsFwd();
  $curr_urg->_ref_sejour->loadRefsFwd();
}
// Liste des opérations du jour sélectionné
$listDay = new CPlageOp;
$where = array();
$where["date"] = "= '$date'";
$where["chir_id"] = "= '$userSel->user_id'";
$order = "debut";
$listDay = $listDay->loadList($where, $order);
foreach ($listDay as &$curr_plage) {
  $curr_plage->loadRefs();
  foreach ($curr_plage->_ref_operations as &$curr_op) {
    $curr_op->loadRefsFwd();
    $curr_op->_ref_sejour->loadRefsFwd();
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("boardItem"   , $boardItem);
$smarty->assign("date"        , $date);
$smarty->assign("listUrgences", $listUrgences);
$smarty->assign("listDay"     , $listDay);
$smarty->assign("board"       , $board);

$smarty->display("inc_list_operations.tpl");

?>
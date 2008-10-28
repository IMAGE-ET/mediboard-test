<?php /* $Id: vw_bloc.php 783 2006-09-14 12:44:01Z rhum1 $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: 783 $
* @author Romain Ollivier
*/

global $can;
$can->needsEdit();

$deblist = mbGetValueFromGet("deblist", mbDate("-1 WEEK"));
$finlist = mbDate("+1 DAY", $deblist);
$bloc_id = mbGetValueFromGetOrSession("bloc_id");

$user = new CMediusers;
$listPrats = $user->loadPraticiens(PERM_READ);

$listBlocs = CGroups::loadCurrent()->loadBlocs();
$bloc = new CBlocOperatoire();
if (!$bloc->load($bloc_id)) {
  $bloc = reset($listBlocs);
}

$where = array(
  "stats"   => "= '1'",
  "bloc_id" => "= '$bloc->_id'",
);
$order = "nom";
$salle = new CSalle;
$listSalles = $salle->loadList($where, $order);

// Récupération des plages
$where = array(
  "date"     => "BETWEEN '$deblist' AND '$finlist'",
  "salle_id" => CSQLDataSource::prepareIn(array_keys($listSalles)),
);
$order = "date, salle_id, debut, chir_id";

$plage = new CPlageOp;
$listPlages = $plage->loadList($where, $order);

// Récupération des interventions
foreach($listPlages as &$curr_plage) {
  $curr_plage->loadRefs(0);
  $curr_plage->loadRefsFwd();
  $curr_plage->loadRefsBack(0, "entree_salle");
  
  $i = 1;
  foreach($curr_plage->_ref_operations as &$curr_op) {
    $curr_op->_rank_reel = $i++;
    $next = next($curr_plage->_ref_operations);
    $curr_op->_pat_next = (($next !== false) ? $next->entree_salle : null);
    $curr_op->loadRefs();
    $curr_op->loadLogs();
    $curr_op->_ref_sejour->loadRefs();
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("deblist",    $deblist);
$smarty->assign("listPlages", $listPlages);
$smarty->assign("listBlocs",  $listBlocs);
$smarty->assign("bloc",       $bloc);

$smarty->display("vw_bloc2.tpl");

?>
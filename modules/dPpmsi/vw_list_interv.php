<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

$date = mbGetValueFromGetOrSession("date", mbDate());

// Selection des salles
$listSalles = new CSalle;
$listSalles = $listSalles->loadGroupList();

$totalOp = 0;

$plages = new CPlageOp;
$where = array();
$where["date"] = "= '$date'";
$where["salle_id"] = $plages->_spec->ds->prepareIn(array_keys($listSalles));
$order = "debut";
$plages = $plages->loadList($where, $order);
foreach($plages as &$curr_plage) {
  $curr_plage->loadRefs(0);
  $totalOp += count($curr_plage->_ref_operations);
  foreach($curr_plage->_ref_operations as &$curr_op) {
    $curr_op->loadRefsFwd();
    $curr_op->_ref_sejour->loadNumDossier();
    $curr_op->_ref_sejour->loadRefPatient();
    $curr_op->_ref_sejour->_ref_patient->loadIPP();
    $curr_op->loadExtCodesCCAM();
    $curr_op->loadHprimFiles();
  }
}

$urgences = new COperation;
$where = array();
$where["date"]     = "= '$date'";
$where["salle_id"] = $urgences->_spec->ds->prepareIn(array_keys($listSalles));
$order = "chir_id";
$urgences = $urgences->loadList($where, $order);
$totalOp += count($urgences);
foreach($urgences as &$curr_op) {
  $curr_op->loadRefsFwd();
  $curr_op->_ref_sejour->loadNumDossier();
  $curr_op->_ref_sejour->loadRefPatient();
  $curr_op->_ref_sejour->_ref_patient->loadIPP();
  $curr_op->loadExtCodesCCAM();
  $curr_op->loadHprimFiles();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"    , $date  );
$smarty->assign("plages"  , $plages);
$smarty->assign("urgences", $urgences);
$smarty->assign("totalOp" , $totalOp);

$smarty->display("vw_list_interv.tpl");

?>

<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
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
  foreach($curr_plage->_ref_operations as $key_op => &$curr_op) {
    if($curr_op->annulee) {
      unset($curr_plage[$key_op]);
    } else {
      $curr_op->loadRefsFwd();
      $curr_op->_ref_sejour->loadNumDossier();
      $curr_op->_ref_sejour->loadRefPatient();
      $curr_op->_ref_sejour->_ref_patient->loadIPP();
      $curr_op->loadExtCodesCCAM();
      $curr_op->loadHprimFiles();
    }
  }
  $totalOp += count($curr_plage->_ref_operations);
}

$operation = new COperation;
$where = array();
$where["date"]     = "= '$date'";
$where["salle_id"] = $operation->_spec->ds->prepareIn(array_keys($listSalles));
$order = "chir_id";
$urgences = $operation->loadList($where, $order);
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

$smarty->assign("date"     , $date);
$smarty->assign("operation", $operation);
$smarty->assign("plages"   , $plages);
$smarty->assign("urgences" , $urgences);
$smarty->assign("totalOp"  , $totalOp);

$smarty->display("vw_list_interv.tpl");

?>

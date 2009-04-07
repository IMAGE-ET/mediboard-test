<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

$date  = mbGetValueFromGetOrSession("date", mbDate());
$operation_id = mbGetValueFromGetOrSession("operation_id");
$hide_finished = mbGetValueFromGetOrSession("hide_finished", 0);

// Selection des salles
$listBlocs = CGroups::loadCurrent()->loadBlocs(PERM_READ);

// Chargement des chirurgiens
$listPermPrats = new CMediusers;
$listPermPrats = $listPermPrats->loadPraticiens(PERM_READ);
$listPrats  = array();
$plagesJour = new CPlageOp();
$where = array();
$where["date"] = "= '$date'";
$groupby = "chir_id";
$plagesJour = $plagesJour->loadList($where, null, null, $groupby);
foreach($plagesJour as $curr_plage) {
  if(key_exists($curr_plage->chir_id, $listPermPrats)) {
    $listPrats[$curr_plage->chir_id] = $listPermPrats[$curr_plage->chir_id];
  }
}
$opsJour = new COperation();
$where = array();
$where["date"] = "= '$date'";
$where["annulee"] = "= '0'";
$groupby = "chir_id";
$opsJour = $opsJour->loadList($where, null, null, $groupby);
foreach($opsJour as $curr_op) {
  if(key_exists($curr_op->chir_id, $listPermPrats)) {
    $listPrats[$curr_op->chir_id] = $listPermPrats[$curr_op->chir_id];
  }
}
$listPrats = CMbArray::pluck($listPrats, "_view");
asort($listPrats);

// Selection des plages opératoires de la journée
$praticien = new CMediusers;
if ($praticien->load(mbGetValueFromGetOrSession("praticien_id"))) {
  $praticien->loadRefsForDay($date); 
}

if ($hide_finished == 1) {
  foreach($praticien->_ref_plages as &$plage) {
    foreach($plage->_ref_operations as $key => $op){
      if ($op->sortie_salle) unset($plage->_ref_operations[$key]);
    }
    foreach($plage->_unordered_operations as $key => $op){
      if ($op->sortie_salle) unset($plage->_unordered_operations[$key]);
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("vueReduite"    , false        );
$smarty->assign("hide_finished" , $hide_finished);
$smarty->assign("praticien"     , $praticien   );
$smarty->assign("salle"         , new CSalle   );
$smarty->assign("listBlocs"     , $listBlocs   );
$smarty->assign("listPrats"     , $listPrats   );
$smarty->assign("date"          , $date        );
$smarty->assign("operation_id"  , $operation_id);

$smarty->display("inc_liste_op_prat.tpl");
?>
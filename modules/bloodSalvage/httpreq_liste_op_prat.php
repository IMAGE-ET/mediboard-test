<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m, $g;

$can->needsRead();

$date  = CValue::getOrSession("date", mbDate());
$operation_id = CValue::getOrSession("operation_id");

// Liste des blocs
$listBlocs = new CBlocOperatoire();
$listBlocs = $listBlocs->loadGroupList();

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
if ($praticien->load(CValue::getOrSession("praticien_id"))) {
  $praticien->loadRefsForDay($date); 
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("vueReduite"    , false        );
$smarty->assign("praticien"     , $praticien   );
$smarty->assign("salle"         , new CSalle   );
$smarty->assign("listBlocs"     , $listBlocs   );
$smarty->assign("listPrats"     , $listPrats   );
$smarty->assign("date"          , $date        );
$smarty->assign("operation_id"  , $operation_id);

$smarty->display("inc_liste_op_prat.tpl");
?>
<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m, $g;

$type    = CValue::get("type", "semaine");
$date    = CValue::get("date", mbDate());
$bloc_id = CValue::get("bloc_id");

if($type == "semaine") {
  $date = mbDate("last sunday", $date);
  $fin  = mbDate("next sunday", $date);
  $date = mbDate("+1 day", $date);
} else {
  $fin = $date;
}

$bloc = new CBlocOperatoire();
$bloc->load($bloc_id);
$bloc->loadRefsSalles();
$bloc->loadRefsAlertesIntervs();
foreach($bloc->_alertes_intervs as &$_alerte) {
  $_alerte->loadTargetObject();
  $_alerte->_ref_object->loadExtCodesCCAM();
  $_alerte->_ref_object->loadRefPlageOp();
  $_alerte->_ref_object->loadRefPraticien();
  $_alerte->_ref_object->updateSalle();
  $_alerte->_ref_object->_ref_chir->loadRefFunction();
}

$operation = new COperation();

// Liste des alertes sur les interventions

// Liste des interventions non validées
$ljoin = array();
$ljoin["plagesop"] = "operations.plageop_id = plagesop.plageop_id";
$where = array();
$where["plagesop.date"]      = "BETWEEN '$date' AND '$fin'";
$where["plagesop.salle_id"]  = CSQLDataSource::prepareIn(array_keys($bloc->_ref_salles));
$where["operations.annulee"] = "= '0'";
$where["operations.rank"]    = "= '0'";
$order = "plagesop.date, plagesop.chir_id";

$listNonValidees = $operation->loadList($where, $order, null, null, $ljoin);

foreach($listNonValidees as &$op) {
  $op->loadExtCodesCCAM();
  $op->loadRefPlageOp();
  $op->loadRefPraticien();
  $op->updateSalle();
  $op->_ref_chir->loadRefFunction();
}

// Liste des interventions hors plage
$ljoin = array();
$ljoin["sejour"] = "sejour.sejour_id = operations.sejour_id";
$where = array();
$where["operations.date"]    = "BETWEEN '$date' AND '$fin'";
$where["operations.annulee"] = "= '0'";
$where[]                     = "operations.salle_id IS NULL OR operations.salle_id ". CSQLDataSource::prepareIn(array_keys($bloc->_ref_salles));
$where["sejour.group_id"]    = "= '".CGroups::loadCurrent()->_id."'";
$order = "operations.date, operations.chir_id";

$listHorsPlage = $operation->loadList($where, $order, null, null, $ljoin);

foreach($listHorsPlage as &$op) {
  $op->loadRefPlageOp();
  $op->loadExtCodesCCAM();
  $op->loadRefPraticien();
  $op->updateSalle();
  $op->_ref_chir->loadRefFunction();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listAlertes"    , $bloc->_alertes_intervs);
$smarty->assign("listNonValidees", $listNonValidees       );
$smarty->assign("listHorsPlage"  , $listHorsPlage         );

$smarty->display("vw_alertes.tpl");

?>
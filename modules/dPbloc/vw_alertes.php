<?php /** $Id: $ **/

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$type    = CValue::get("type", "week");
$date    = CValue::get("date", CMbDT::date());
$bloc_id = CValue::get("bloc_id");

if ($type == "week") {
  $date = CMbDT::date("last sunday", $date);
  $fin  = CMbDT::date("next sunday", $date);
  $date = CMbDT::date("+1 day", $date);
}
else {
  $fin = $date;
}


//alerts
$nbAlertes = 0;
$bloc = new CBlocOperatoire();
$bloc->load($bloc_id);
$where = array();
if ($bloc->_id) {
  $where["bloc_operatoire_id"] = "= '$bloc->_id'";
}
/** @var CBlocOperatoire[] $listBlocs */
$listBlocs = $bloc->loadGroupList($where);

foreach ($listBlocs as $_bloc) {
  $_bloc->loadRefsSalles();
  $_bloc->loadRefsAlertesIntervs();

  foreach ($_bloc->_alertes_intervs  as &$_alerte) {
    $nbAlertes++;
    $_alerte->loadTargetObject();
    $_alerte->_ref_object->loadExtCodesCCAM();
    $_alerte->_ref_object->loadRefPlageOp();
    $_alerte->_ref_object->loadRefPraticien();
    $_alerte->_ref_object->loadRefPatient();
    $_alerte->_ref_object->updateSalle();
    $_alerte->_ref_object->_ref_chir->loadRefFunction();
  }
}

$operation = new COperation();

// Liste des interventions non validées
$ljoin = array();
$ljoin["plagesop"] = "operations.plageop_id = plagesop.plageop_id";
$where = array();
$where["plagesop.date"]      = "BETWEEN '$date' AND '$fin'";
if ($bloc->_id) {
  $bloc->loadRefsSalles();
  $where["plagesop.salle_id"]  = CSQLDataSource::prepareIn(array_keys($bloc->_ref_salles));
}
$where["operations.annulee"] = "= '0'";
$where["operations.rank"]    = "= '0'";
$order = "plagesop.date, plagesop.chir_id";

/** @var COperation[] $listNonValidees */
$listNonValidees = $operation->loadList($where, $order, null, null, $ljoin);

foreach ($listNonValidees as &$op) {
  $op->loadExtCodesCCAM();
  $op->loadRefPlageOp();
  $op->loadRefPraticien();
  $op->loadRefPatient();
  $op->updateSalle();
  $op->_ref_chir->loadRefFunction();
}

// Liste des interventions hors plage
$ljoin = array();
$ljoin["sejour"] = "sejour.sejour_id = operations.sejour_id";
$where = array();
$where["operations.date"]    = "BETWEEN '$date' AND '$fin'";
$where["operations.annulee"] = "= '0'";
if ($bloc->_id) {
  $where[]                   = "operations.salle_id IS NULL OR operations.salle_id ".
    CSQLDataSource::prepareIn(array_keys($bloc->_ref_salles));
}
$where["sejour.group_id"]    = "= '".CGroups::loadCurrent()->_id."'";
$order = "operations.date, operations.chir_id";

/** @var COperation[] $listHorsPlage */
$listHorsPlage = $operation->loadList($where, $order, null, null, $ljoin);

foreach ($listHorsPlage as &$op) {
  $op->loadRefPlageOp();
  $op->loadExtCodesCCAM();
  $op->loadRefPraticien();
  $op->loadRefPatient();
  $op->updateSalle();
  $op->_ref_chir->loadRefFunction();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("blocs"    ,       $listBlocs);
$smarty->assign("nbAlertes"    ,       $nbAlertes);
$smarty->assign("listNonValidees", $listNonValidees       );
$smarty->assign("listHorsPlage"  , $listHorsPlage         );

$smarty->display("vw_alertes.tpl");
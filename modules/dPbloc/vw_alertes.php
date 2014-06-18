<?php /** $Id: $ **/

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$type       = CValue::get("type", "week");
$date       = CValue::get("date", CMbDT::date());
$bloc_id    = CValue::get("bloc_id");
$edit_mode  = CValue::get("edit_mode", false);

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

/** @var CBlocOperatoire[] $blocs */
$blocs = $bloc->loadGroupList($where);
foreach ($blocs as $_bloc) {
  $_bloc->loadRefsSalles();
  $alertes = $_bloc->loadRefsAlertesIntervs();
  foreach ($alertes as $_alerte) {
    $nbAlertes++;
    /** @var COperation $operation */
    $operation = $_alerte->loadTargetObject();
    $operation->loadExtCodesCCAM();
    $operation->loadRefPlageOp();
    $operation->loadRefPraticien()->loadRefFunction();
    $operation->loadRefPatient();
  }
}

$operation = new COperation();

// Liste des interventions non validées
$ljoin = array();
$ljoin["plagesop"] = "operations.plageop_id = plagesop.plageop_id";
$where = array();
$where["plagesop.date"] = "BETWEEN '$date' AND '$fin'";
if ($bloc->_id) {
  $salles = $bloc->loadRefsSalles();
  $where["plagesop.salle_id"]  = CSQLDataSource::prepareIn(array_keys($salles));
}
$where["operations.annulee"] = "= '0'";
$where["operations.rank"]    = "= '0'";
$order = "plagesop.date, plagesop.chir_id";

/** @var COperation[] $listNonValidees */
$listNonValidees = $operation->loadList($where, $order, null, null, $ljoin);

foreach ($listNonValidees as $_operation) {
  $_operation->loadRefPlageOp();
  $_operation->loadExtCodesCCAM();
  $_operation->loadRefPraticien()->loadRefFunction();
  $_operation->loadRefPatient();
}

// Liste des interventions hors plage
$ljoin = array();
$ljoin["sejour"] = "sejour.sejour_id = operations.sejour_id";
$where = array();
$where["operations.plageop_id"] = "IS NULL";
$where["operations.date"]       = "BETWEEN '$date' AND '$fin'";
$where["operations.annulee"]    = "= '0'";
if ($bloc->_id) {
  $where[] = "operations.salle_id IS NULL OR operations.salle_id ".
    CSQLDataSource::prepareIn(array_keys($bloc->_ref_salles));
}
$where["sejour.group_id"]    = "= '".CGroups::loadCurrent()->_id."'";
$order = "operations.date, operations.chir_id";

/** @var COperation[] $listHorsPlage */
$listHorsPlage = $operation->loadList($where, $order, null, null, $ljoin);

foreach ($listHorsPlage as $_operation) {
  $_operation->loadRefPlageOp();
  $_operation->loadExtCodesCCAM();
  $_operation->loadRefPraticien()->loadRefFunction();
  $_operation->loadRefPatient();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("blocs"           , $blocs);
$smarty->assign("nbAlertes"       , $nbAlertes);
$smarty->assign("listNonValidees" , $listNonValidees);
$smarty->assign("listHorsPlage"   , $listHorsPlage);
$smarty->assign("edit_mode"       , $edit_mode);

$smarty->display("vw_alertes.tpl");
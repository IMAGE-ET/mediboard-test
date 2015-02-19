<?php

/**
 * $Id$
 *
 * @category Pmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$date = CValue::getOrSession("date", CMbDT::date());
$page = CValue::get("pageOp", 0);

// Selection des salles
$listSalles        = new CSalle;
$listSalles        = $listSalles->loadGroupList();
$plage             = new CPlageOp;
$where             = array();
$where["date"]     = "= '$date'";
$where["salle_id"] = CSQLDataSource::prepareIn(array_keys($listSalles));
$order             = "debut";
$step              = 30;
$limit             = "$page,$step";

/** @var CPlageOp[] $plages */
$plages              = $plage->loadList($where, $order);
$operation           = new COperation();

$where               = array();
$where["plageop_id"] = CSQLDataSource::prepareIn(array_keys($plages));
$where["annulee"]    = "= '0'";

$count               = $operation->countList($where);
$operations          = $operation->loadList($where, null, $limit);

/** @var CSejour[] $sejours */
$sejours = COperation::massLoadFwdRef($operations, "sejour_id");

/** @var CPatient[] $patients */
$patients = CSejour::massLoadFwdRef($sejours, "patient_id");

CSejour::massLoadNDA($sejours);
CPatient::massLoadIPP($patients);
CSejour::massCountDocItems($sejours);
COperation::massCountDocItems($operations);
$chirurgiens = COperation::massLoadFwdRef($operations, "chir_id");
CMediusers::massLoadFwdRef($chirurgiens, "function_id");

/** @var COperation[] $operations */
foreach ($operations as $_operation) {
  // Détails de l'opérations
  $_operation->loadRefChir()->loadRefFunction();
  $_operation->loadExtCodesCCAM();
  $_operation->loadRefsDocItems();

  // Détails du séjour
  $_operation->_ref_sejour               = $sejours[$_operation->sejour_id];
  $_operation->_ref_sejour->_ref_patient = $patients[$_operation->_ref_sejour->patient_id];
  $_operation->_ref_sejour->loadRefsDocItems();
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date", $date);
$smarty->assign("operations", $operations);
$smarty->assign("pageOp", $page);
$smarty->assign("countOp", $count);
$smarty->assign("step", $step);

$smarty->display("current_dossiers/inc_current_operations.tpl");
<?php 

/**
 * $Id$
 *  
 * @category Pmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

CCanDo::checkRead();

$date = CValue::getOrSession("date", CMbDT::date());
$page = CValue::get("pageUrg", 0);

$operation = new COperation;
$where = array();
$ljoin["sejour"] = "sejour.sejour_id = operations.sejour_id";
$where["operations.date"]       = "= '$date'";
$where["operations.plageop_id"] = "IS NULL";
$where["operations.annulee"]    = "= '0'";
$where["sejour.group_id"]       = "= '".CGroups::loadCurrent()->_id."'";
$order = "operations.chir_id";
$step              = 30;
$limit             = "$page,$step";

/** @var COperation[] $horsplages */
$count               = $operation->countList($where, null, $ljoin);
$horsplages = $operation->loadList($where, $order, $limit, null, $ljoin);
/** @var CSejour[] $sejours */
$sejours = COperation::massLoadFwdRef($horsplages, "sejour_id");
/** @var CPatient[] $patients */
$patients = CSejour::massLoadFwdRef($sejours, "patient_id");
CSejour::massLoadNDA($sejours);
CPatient::massLoadIPP($patients);
CSejour::massCountDocItems($sejours);
COperation::massCountDocItems($horsplages);
$chirurgiens = COperation::massLoadFwdRef($horsplages, "chir_id");
CMediusers::massLoadFwdRef($chirurgiens, "function_id");

foreach ($horsplages as $_operation) {
  $_operation->loadRefChir()->loadRefFunction();
  $_operation->loadExtCodesCCAM();
  $_operation->loadRefsDocItems();

  $_operation->_ref_sejour = $sejours[$_operation->sejour_id];
  $_operation->_ref_sejour->_ref_patient = $patients[$_operation->_ref_sejour->patient_id];
  $_operation->_ref_sejour->loadRefsDocItems();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"     , $date);
$smarty->assign("urgences" , $horsplages);
$smarty->assign("pageUrg", $page);
$smarty->assign("countUrg", $count);
$smarty->assign("step", $step);

$smarty->display("current_dossiers/inc_current_urgences.tpl");

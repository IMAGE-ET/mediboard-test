<?php
/**
 * $Id$
 *
 * @category Admissions
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$ds = CSQLDataSource::get("std");

// Initialisation de variables
$date = CValue::getOrSession("date", CMbDT::date());

$month_min     = CMbDT::date("first day of +0 month", $date);
$lastmonth     = CMbDT::date("last day of -1 month" , $date);
$nextmonth     = CMbDT::date("first day of +1 month", $date);

$selAdmis      = CValue::getOrSession("selAdmis", "0");
$selSaisis     = CValue::getOrSession("selSaisis", "0");
$type          = CValue::getOrSession("type");
$services_ids  = CValue::getOrSession("services_ids");
$prat_id       = CValue::getOrSession("prat_id");
$type_pec      = CValue::get("type_pec", array("M", "C", "O"));
$bank_holidays = CMbDate::getHolidays($date);

if (is_array($services_ids)) {
  CMbArray::removeValue("", $services_ids);
}

$hier   = CMbDT::date("- 1 day", $date);
$demain = CMbDT::date("+ 1 day", $date);

// Initialisation du tableau de jours
$days = array();
for ($day = $month_min; $day < $nextmonth; $day = CMbDT::date("+1 DAY", $day)) {
  $days[$day] = array(
    "admissions" => "0",
    "admissions_non_effectuee" => "0",
    "admissions_non_preparee" => "0",
  );
}

$group = CGroups::loadCurrent();
$where = array();
$leftjoin = array();

// filtre sur les services
if (count($services_ids)) {
  $leftjoin["affectation"] = " affectation.sejour_id = sejour.sejour_id AND affectation.sortie = sejour.sortie";
  $where["affectation.service_id"] = $ds->prepareIn($services_ids);
}

// filtre sur les types pec des sejours
$where["sejour.type_pec"] = CSQLDataSource::prepareIn($type_pec);

// filtre sur les types d'admission
if ($type == "ambucomp") {
  $where["sejour.type"] = " = 'ambu' OR `sejour`.`type` = 'comp'";
}
elseif ($type) {
  if ($type !== 'tous') {
    $where["sejour.type"] = " = '$type'";
  }
}
else {
  $where["sejour.type"] = "!= 'urg' AND `sejour`.`type` != 'seances'";
}

// filtre sur le praticien
if ($prat_id) {
  $where["sejour.praticien_id"] = "= '$prat_id'";
}

$where["sejour.entree"] = " BETWEEN '$month_min' AND '$nextmonth'";
$where["sejour.group_id"] = " = '$group->_id'";
$where["sejour.annule"] = " = '0'";

// Liste des admissions par jour
$request = new CRequest();
$request->addSelect(array("DATE_FORMAT(sejour.entree, '%Y-%m-%d') AS 'date'", "COUNT(sejour.sejour_id) AS 'num'"));
$request->addTable("sejour");
$request->addWhere($where);
$request->addLJoin($leftjoin);
$request->addGroup("date");
$request->addOrder("date");
foreach ($ds->loadHashList($request->makeSelect()) as $day => $num1) {
  $days[$day]["admissions"] = $num1;
}

// Liste des admissions non préparées
$where["sejour.entree_preparee"] = " = '0'";
$request->addWhere($where);
foreach ($ds->loadHashList($request->makeSelect()) as $day => $num3) {
  $days[$day]["admissions_non_preparee"] = $num3;
}

// Liste des admissions non effectuées par jour
unset($where['sejour.entree']);
unset($where['sejour.entree_preparee']);
$request->where = array();
$where["sejour.entree_prevue"] = " BETWEEN '$month_min' AND '$nextmonth'";
$where["sejour.entree_reelle"] = " IS NULL";
$request->addWhere($where);
foreach ($ds->loadHashList($request->makeSelect()) as $day => $num2) {
  $days[$day]["admissions_non_effectuee"] = $num2;
}

$totaux = array(
  "admissions" => "0",
  "admissions_non_effectuee" => "0",
  "admissions_non_preparee" => "0",
);

foreach ($days as $day) {
  $totaux["admissions"] += $day["admissions"];
  $totaux["admissions_non_effectuee"] += $day["admissions_non_effectuee"];
  $totaux["admissions_non_preparee"] += $day["admissions_non_preparee"];
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("hier"         , $hier);
$smarty->assign("demain"       , $demain);

$smarty->assign("selAdmis"     , $selAdmis);
$smarty->assign("selSaisis"    , $selSaisis);

$smarty->assign("bank_holidays", $bank_holidays);
$smarty->assign('date'         , $date);
$smarty->assign('lastmonth'    , $lastmonth);
$smarty->assign('nextmonth'    , $nextmonth);
$smarty->assign('days'         , $days);
$smarty->assign('totaux'       , $totaux);

$smarty->display('inc_vw_all_admissions.tpl');

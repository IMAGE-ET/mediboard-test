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

if (phpversion() >= "5.3") {
  $month_min     = CMbDT::date("first day of +0 month", $date);
  $lastmonth     = CMbDT::date("last day of -1 month" , $date);
  $nextmonth     = CMbDT::date("first day of +1 month", $date);
}
else {
  $month_min     = CMbDT::transform("+ 0 month", $date, "%Y-%m-01");
  $lastmonth     = CMbDT::date("-1 month", $date);
  $nextmonth     = CMbDT::date("+1 month", $date);
  if (CMbDT::transform(null, $date, "%m-%d") == "08-31") {
    $nextmonth = CMbDT::transform("+0 month", $nextmonth, "%Y-09-%d");
  }
  else {
    $nextmonth     = CMbDT::transform("+0 month", $nextmonth, "%Y-%m-01");
  }
}

$bank_holidays = CMbDate::getHolidays($date);
$hier = CMbDT::date("- 1 day", $date);
$demain = CMbDT::date("+ 1 day", $date);

// Initialisation du tableau de jours
$days = array();
for ($day = $month_min; $day < $nextmonth; $day = CMbDT::date("+1 DAY", $day)) {
  $days[$day] = array(
    "total" => "0",
  );
}

// Récupération de la liste des anesthésistes
$mediuser = new CMediusers;
$anesthesistes = $mediuser->loadAnesthesistes(PERM_READ);

$consult = new CConsultation();

// Liste des admissions par jour
//foreach ($ds->loadHashList($sql) as $day => $num1) {
//  $days[$day]["num1"] = $num1;
//}

// Récupération du nombre de consultations par jour
$ljoin = array();
$ljoin["plageconsult"] = "consultation.plageconsult_id = plageconsult.plageconsult_id";
$where = array();
$where["consultation.patient_id"] = "IS NOT NULL";
$where["consultation.annule"] = "= '0'";
$where["plageconsult.chir_id"] = CSQLDataSource::prepareIn(array_keys($anesthesistes));
$where["plageconsult.date"] = "BETWEEN '$month_min' AND '$nextmonth'";
$order = "plageconsult.date";
$groupby = "plageconsult.date";

$fields = array("plageconsult.date");

$listMonth = $consult->countMultipleList($where, $order, $groupby, $ljoin, $fields);
foreach ($listMonth as $_day) {
  $days[$_day["date"]]["total"] = $_day["total"];
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("hier"         , $hier);
$smarty->assign("demain"       , $demain);
$smarty->assign("date"         , $date);
$smarty->assign("lastmonth"    , $lastmonth);
$smarty->assign("nextmonth"    , $nextmonth);
$smarty->assign("bank_holidays", $bank_holidays);
$smarty->assign("days"         , $days);

$smarty->display('inc_vw_all_preadmissions.tpl');

<?php

/**
 * maternite
 *  
 * @category maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$date = CValue::getOrSession("date", CMbDT::date());
$view = CValue::getOrSession("view", "all");

$ds = CSQLDataSource::get("std");
$group = CGroups::loadCurrent();
$bank_holidays = mbBankHolidays($date);
$next          = CMbDT::date("+1 DAY", $date);
$month_min     = CMbDT::transform("+ 0 month", $date, "%Y-%m-01");
$month_max     = CMbDT::transform("+ 1 month", $month_min, "%Y-%m-01");
$prev_month    = CMbDT::date("-1 month", $date);
$next_month    = CMbDT::date("+1 month", $date);
$date_before   = CMbDT::date("-1 day", $date);
$date_after    = CMbDT::date("+1 day", $date);

// Initialisation du tableau de jours
$days = array();
for ($day = $month_min; $day < $month_max; $day = CMbDT::date("+1 DAY", $day)) {
  $days[$day] = array(
    "num1" => "0",
    "num2" => "0",
    "num3" => "0",
  );
}

// Liste des admissions par jour
$query = "SELECT DATE_FORMAT(`sejour`.`entree`, '%Y-%m-%d') AS `date`, COUNT(`sejour`.`sejour_id`) AS `num`
  FROM `sejour`
  WHERE `sejour`.`entree` BETWEEN '$month_min' AND '$month_max'
    AND `sejour`.`group_id` = '$group->_id'
    AND `sejour`.`annule` = '0'
    AND `sejour`.`type_pec` = 'O'
  GROUP BY `date`
  ORDER BY `date`";
foreach ($ds->loadHashList($query) as $day => $num1) {
  $days[$day]["num1"] = $num1;
}

// Liste des admissions non effectuées par jour
$query = "SELECT DATE_FORMAT(`sejour`.`entree`, '%Y-%m-%d') AS `date`, COUNT(`sejour`.`sejour_id`) AS `num`
  FROM `sejour`
  WHERE `sejour`.`entree_prevue` BETWEEN '$month_min' AND '$month_max'
    AND `sejour`.`group_id` = '$group->_id'
    AND `sejour`.`entree_reelle` IS NULL
    AND `sejour`.`annule` = '0'
    AND `sejour`.`type_pec` = 'O'
  GROUP BY `date`
  ORDER BY `date`";
foreach ($ds->loadHashList($query) as $day => $num2) {
  $days[$day]["num2"] = $num2;
}

// Liste des admissions non préparées
$query = "SELECT DATE_FORMAT(`sejour`.`entree`, '%Y-%m-%d') AS `date`, COUNT(`sejour`.`sejour_id`) AS `num`
    FROM `sejour`
  WHERE `sejour`.`entree` BETWEEN '$month_min' AND '$month_max'
    AND `sejour`.`group_id` = '$group->_id'
    AND `sejour`.`entree_preparee` = '0'
    AND `sejour`.`annule` = '0'
    AND `sejour`.`type_pec` = 'O'
  GROUP BY `date`
  ORDER BY `date`";
foreach ($ds->loadHashList($query) as $day => $num3) {
  $days[$day]["num3"] = $num3;
}

$sejour = new CSejour;

$sejour->type_pec = "O";
$ljoin = array();
$ljoin["patients"] = "sejour.patient_id = patients.patient_id";

$where = array();
$where["entree"] = "BETWEEN '$date' AND '$next'";
$where["sejour.annule"]   = "= '0'";
$where["group_id"] = "= '$group->_id'";
$where["type_pec"] = "= 'O'";
if ($view == "non_eff") {
  $where[] = "(entree_reelle IS NULL OR entree_reelle = '0000-00-00 00:00:00')";
}

if ($view == "non_prep") {
  $where["sejour.entree_preparee"] = "= '0'";
}

$sejours = $sejour->loadList($where, "patients.nom ASC", null, null, $ljoin);

CMbObject::massLoadFwdRef($sejours, "patient_id");
CMbObject::massLoadFwdRef($sejours, "grossesse_id");

foreach ($sejours as $_sejour) {
  $_sejour->loadRefPatient();
  $_sejour->loadRefsOperations();
  
  $grossesse = $_sejour->loadRefGrossesse();
  $grossesse->_praticiens = CMbObject::massLoadFwdRef($grossesse->loadRefsSejours(), "praticien_id");
  $grossesse->_praticiens+= CMbObject::massLoadFwdRef($grossesse->loadRefsConsultations(), "_prat_id");
  $naissances = $grossesse->loadRefsNaissances();
  
  $sejours_enfant = CMbObject::massLoadFwdRef($naissances, "sejour_enfant_id");
  CMbObject::massLoadFwdRef($sejours_enfant, "patient_id");
  
  foreach ($naissances as $_naissance) {
    $_naissance->loadRefSejourEnfant()->loadRefPatient();
  }
  
  CMbObject::massLoadFwdRef($grossesse->_praticiens, "function_id");
  foreach ($grossesse->_praticiens as $_praticien) {
    $_praticien->loadRefFunction();
  }
}

$smarty = new CSmartyDP;

$smarty->assign("sejours", $sejours);
$smarty->assign("days"   , $days);
$smarty->assign("date"   , $date);
$smarty->assign("view"   , $view);
$smarty->assign("date_before", $date_before);
$smarty->assign("date_after" , $date_after);
$smarty->assign("prev_month" , $prev_month);
$smarty->assign("next_month" , $next_month);
$smarty->assign("bank_holidays", $bank_holidays);

$smarty->display("vw_admissions.tpl");
?>
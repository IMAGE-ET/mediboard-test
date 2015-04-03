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

$month_min = CMbDT::date("first day of +0 month", $date);
$lastmonth = CMbDT::date("last day of -1 month", $date);
$nextmonth = CMbDT::date("first day of +1 month", $date);

$current_m = CValue::get("current_m");

$selSortis      = CValue::getOrSession("selSortis", "0");
$type           = CValue::getOrSession("type");
$service_id     = CValue::getOrSession("service_id");
$prat_id        = CValue::getOrSession("prat_id");
$only_confirmed = CValue::getOrSession("only_confirmed");
$bank_holidays  = CMbDate::getHolidays($date);
$service_id     = explode(",", $service_id);
CMbArray::removeValue("", $service_id);

$hier   = CMbDT::date("- 1 day", $date);
$demain = CMbDT::date("+ 1 day", $date);

// Initialisation des totaux
$days = array();
for ($day = $month_min; $day < $nextmonth; $day = CMbDT::date("+1 DAY", $day)) {
  $days[$day]["sorties"]                = 0;
  $days[$day]["sorties_non_effectuees"] = 0;
  $days[$day]["sorties_non_preparees"]  = 0;
  $days[$day]["sorties_non_facturees"]  = 0;
}

// filtre sur les types de sortie
if ($type == "ambucomp") {
  $filterType = "AND (`sejour`.`type` = 'ambu' OR `sejour`.`type` = 'comp')";
}
elseif ($type) {
  $filterType = "AND `sejour`.`type` = '$type'";
}
else {
  $filterType = "AND `sejour`.`type` != 'urg' AND `sejour`.`type` != 'seances'";
}

// filtre sur les services
if (count($service_id)) {
  $leftjoinService = "LEFT JOIN affectation
                        ON affectation.sejour_id = sejour.sejour_id AND affectation.sortie = sejour.sortie";
  $filterService   = "AND affectation.service_id " . CSQLDataSource::prepareIn($service_id);
}
else {
  $leftjoinService = $filterService = "";
}

// filtre sur le praticiens
if ($prat_id) {
  $filterPrat = "AND sejour.praticien_id = '$prat_id'";
}
else {
  $filterPrat = "";
}

if ($only_confirmed) {
  $filterConfirmed = "AND sejour.confirme IS NOT NULL";
}
else {
  $filterConfirmed = "";
}

$group = CGroups::loadCurrent();

// Listes des sorties par jour
$query = "SELECT DATE_FORMAT(`sejour`.`sortie`, '%Y-%m-%d') AS `date`, COUNT(`sejour`.`sejour_id`) AS `num`
          FROM `sejour`
          $leftjoinService
          WHERE `sejour`.`sortie` BETWEEN '$month_min' AND '$nextmonth'
            AND `sejour`.`group_id` = '$group->_id'
            AND `sejour`.`annule` = '0'
            $filterType
            $filterService
            $filterPrat
            $filterConfirmed
          GROUP BY `date`
          ORDER BY `date`";

foreach ($ds->loadHashList($query) as $day => $num1) {
  $days[$day]["sorties"] = $num1;
}

// Liste des sorties non effectuées par jour
$query = "SELECT DATE_FORMAT(`sejour`.`sortie`, '%Y-%m-%d') AS `date`, COUNT(`sejour`.`sejour_id`) AS `num`
          FROM `sejour`
          $leftjoinService
          WHERE `sejour`.`sortie` BETWEEN '$month_min' AND '$nextmonth'
            AND `sejour`.`group_id` = '$group->_id'
            AND `sejour`.`sortie_reelle` IS NULL
            AND `sejour`.`annule` = '0'
            $filterType
            $filterService
            $filterPrat
            $filterConfirmed
          GROUP BY `date`
          ORDER BY `date`";

foreach ($ds->loadHashList($query) as $day => $num2) {
  $days[$day]["sorties_non_effectuees"] = $num2;
}

// Liste des séjours non facturés par jour
if (CAppUI::conf("ref_pays") == "2") {
  $query = "SELECT DATE_FORMAT(`sejour`.`sortie`, '%Y-%m-%d') AS `date`, COUNT(`sejour`.`sejour_id`) AS `num`
            FROM `sejour`
            $leftjoinService
            WHERE `sejour`.`sortie` BETWEEN '$month_min' AND '$nextmonth'
              AND `sejour`.`group_id` = '$group->_id'
              AND `sejour`.`sortie_reelle` IS NULL
              AND `sejour`.`annule` = '0'
              AND `sejour`.`facture` = '0'
              $filterType
              $filterService
              $filterPrat
            $filterConfirmed
            GROUP BY `date`
            ORDER BY `date`";

  foreach ($ds->loadHashList($query) as $day => $num5) {
    $days[$day]["sorties_non_facturees"] = $num5;
  }
}

// Unprepared discharges
$query = "SELECT DATE_FORMAT(`sejour`.`sortie`, '%Y-%m-%d') AS `date`, COUNT(`sejour`.`sejour_id`) AS `num`
          FROM `sejour`
          $leftjoinService
          WHERE `sejour`.`sortie` BETWEEN '$month_min' AND '$nextmonth'
            AND `sejour`.`group_id` = '$group->_id'
            AND `sejour`.`sortie_preparee` = '0'
            AND `sejour`.`annule` = '0'
            $filterType
            $filterService
            $filterPrat
            $filterConfirmed
          GROUP BY `date`
          ORDER BY `date`";
foreach ($ds->loadHashList($query) as $day => $_nb_non_preparees) {
  $days[$day]["sorties_non_preparees"] = $_nb_non_preparees;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("current_m", $current_m);

$smarty->assign("hier", $hier);
$smarty->assign("demain", $demain);

$smarty->assign("selSortis", $selSortis);

$smarty->assign("bank_holidays", $bank_holidays);
$smarty->assign('date', $date);
$smarty->assign('lastmonth', $lastmonth);
$smarty->assign('nextmonth', $nextmonth);
$smarty->assign('days', $days);

$smarty->display('inc_vw_all_sorties.tpl');

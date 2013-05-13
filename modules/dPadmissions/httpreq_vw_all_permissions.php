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

$type          = CValue::getOrSession("type");
$type_externe  = CValue::getOrSession("type_externe", "depart");

$bank_holidays = CGroups::loadCurrent()->getHolidays($date);

$hier   = CMbDT::date("- 1 day", $date);
$demain = CMbDT::date("+ 1 day", $date);

// Initialisation du tableau de jours
$days = array();
for ($day = $month_min; $day < $nextmonth; $day = CMbDT::date("+1 DAY", $day)) {
  $days[$day] = array(
    "num1" => "0",
    "num2" => "0",
    "num3" => "0",
  );
}

// Récupération de la liste des services
$where = array();
$where["externe"]   = "= '1'";
$where["cancelled"] = "= '0'";
$service = new CService();
$services = $service->loadGroupList($where);

// filtre sur les types d'admission
if ($type == "ambucomp") {
  $filterType = "AND (`sejour`.`type` = 'ambu' OR `sejour`.`type` = 'comp')";
}
elseif($type) {
  $filterType = "AND `sejour`.`type` = '$type'";
}
else {
  $filterType = "AND `sejour`.`type` != 'urg' AND `sejour`.`type` != 'seances'";
}

// filtre sur les services
$filterService = "AND service.service_id ". CSQLDataSource::prepareIn(array_keys($services));

$group = CGroups::loadCurrent();

// Liste des départs par jour
$query = "SELECT DATE_FORMAT(`affectation`.`entree`, '%Y-%m-%d') AS `date`, COUNT(`affectation`.`affectation_id`) AS `num`
  FROM `affectation`
  LEFT JOIN sejour
    ON affectation.sejour_id = sejour.sejour_id
  LEFT JOIN lit
    ON affectation.lit_id = lit.lit_id
  LEFT JOIN chambre
    ON lit.chambre_id = chambre.chambre_id
  LEFT JOIN service
    ON chambre.service_id = service.service_id
  WHERE `affectation`.`entree` BETWEEN '$month_min' AND '$nextmonth'
    AND `sejour`.`group_id` = '$group->_id'
    AND `sejour`.`annule` = '0'
    $filterService
    $filterType
  GROUP BY `date`
  ORDER BY `date`";

foreach ($ds->loadHashList($query) as $day => $num1) {
  $days[$day]["num1"] = $num1;
}

// Liste des retours par jour
$query = "SELECT DATE_FORMAT(`affectation`.`sortie`, '%Y-%m-%d') AS `date`, COUNT(`affectation`.`affectation_id`) AS `num`
  FROM `affectation`
  LEFT JOIN sejour
    ON affectation.sejour_id = sejour.sejour_id
  LEFT JOIN lit
    ON affectation.lit_id = lit.lit_id
  LEFT JOIN chambre
    ON lit.chambre_id = chambre.chambre_id
  LEFT JOIN service
    ON chambre.service_id = service.service_id
  WHERE `affectation`.`sortie` BETWEEN '$month_min' AND '$nextmonth'
    AND `sejour`.`group_id` = '$group->_id'
    AND `sejour`.`annule` = '0'
    $filterService
    $filterType
  GROUP BY `date`
  ORDER BY `date`";
foreach ($ds->loadHashList($query) as $day => $num2) {
  $days[$day]["num2"] = $num2;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("hier"         , $hier);
$smarty->assign("demain"       , $demain);

$smarty->assign("type_externe" , $type_externe);

$smarty->assign("bank_holidays", $bank_holidays);
$smarty->assign('date'         , $date);
$smarty->assign('lastmonth'    , $lastmonth);
$smarty->assign('nextmonth'    , $nextmonth);
$smarty->assign('days'         , $days);

$smarty->display('inc_vw_all_permissions.tpl');

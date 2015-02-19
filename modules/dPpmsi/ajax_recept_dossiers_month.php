<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PMSI
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();
$ds = CSQLDataSource::get("std");

$filterFunction = CValue::getOrSession("filterFunction");
$type           = CValue::getOrSession("type");
$service_id     = CValue::getOrSession("service_id");
$service_id     = explode(",", $service_id);
CMbArray::removeValue("", $service_id);
$prat_id        = CValue::getOrSession("prat_id");
$order_way      = CValue::getOrSession("order_way", "ASC");
$order_col      = CValue::getOrSession("order_col", "patient_id");
$tri_recept     = CValue::getOrSession("tri_recept");
$tri_complet    = CValue::getOrSession("tri_complet");
$date           = CValue::getOrSession("date", CMbDT::date());

$month_min  = CMbDT::date("first day of +0 month", $date);
$lastmonth  = CMbDT::date("last day of -1 month" , $date);
$nextmonth  = CMbDT::date("first day of +1 month", $date);
$bank_holidays = CMbDate::getHolidays($date);

$group = CGroups::loadCurrent();

// Initialisation du tableau de jours
$days = array();
for ($day = $month_min; $day < $nextmonth; $day = CMbDT::date("+1 DAY", $day)) {
  $days[$day] = array(
    "num1" => "0",
    "num2" => "0",
    "num3" => "0",
  );
}

// filtre sur les types d'admission
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
                        ON affectation.sejour_id = sejour.sejour_id AND affectation.sortie = sejour.sortie
                      LEFT JOIN lit
                        ON affectation.lit_id = lit.lit_id
                      LEFT JOIN chambre
                        ON lit.chambre_id = chambre.chambre_id
                      LEFT JOIN service
                        ON chambre.service_id = service.service_id";
  $in_services = CSQLDataSource::prepareIn($service_id);
  $filterService = "AND (sejour.service_id $in_services OR affectation.service_id $in_services)";
}
else {
  $leftjoinService = $filterService = "";
}

// filtre sur le praticien
if ($prat_id) {
  $filterPrat = "AND sejour.praticien_id = '$prat_id'";
}
else {
  $filterPrat = "";
}

$month_min  = CMbDT::dateTime(null, $month_min);
$nextmonth  = CMbDT::dateTime(null, $nextmonth);

// Liste des sorties par jour
$query = "SELECT DATE_FORMAT(`sejour`.`sortie_reelle`, '%Y-%m-%d') AS `date`, COUNT(`sejour`.`sejour_id`) AS `num`
  FROM `sejour`
  $leftjoinService
  WHERE `sejour`.`sortie_reelle` BETWEEN '$month_min' AND '$nextmonth'
    AND `sejour`.`group_id` = '$group->_id'
    AND `sejour`.`annule` = '0'
    $filterType
    $filterService
    $filterPrat
  GROUP BY `date`
  ORDER BY `date`";

foreach ($ds->loadHashList($query) as $day => $num1) {
  $days[$day]["num1"] = $num1;
}

// Liste des sorties dont le dossier n'a pas été reçu
$query = "SELECT DATE_FORMAT(`sejour`.`sortie_reelle`, '%Y-%m-%d') AS `date`, COUNT(`sejour`.`sejour_id`) AS `num`
  FROM `sejour`
  $leftjoinService
  WHERE `sejour`.`sortie_reelle` BETWEEN '$month_min' AND '$nextmonth'
    AND `sejour`.`group_id` = '$group->_id'
    AND `sejour`.`reception_sortie` IS NOT NULL
    AND `sejour`.`annule` = '0'
    $filterType
    $filterService
    $filterPrat
  GROUP BY `date`
  ORDER BY `date`";
foreach ($ds->loadHashList($query) as $day => $num2) {
  $days[$day]["num2"] = $num2;
}

// Liste des sorties dont le dossier est traité
$query = "SELECT DATE_FORMAT(`sejour`.`sortie_reelle`, '%Y-%m-%d') AS `date`, COUNT(`sejour`.`sejour_id`) AS `num`
    FROM `sejour`
  $leftjoinService
  WHERE `sejour`.`sortie_reelle` BETWEEN '$month_min' AND '$nextmonth'
    AND `sejour`.`group_id` = '$group->_id'
    AND `sejour`.`completion_sortie` IS NOT NULL
    AND `sejour`.`annule` = '0'
    $filterType
    $filterService
    $filterPrat
  GROUP BY `date`
  ORDER BY `date`";
foreach ($ds->loadHashList($query) as $day => $num3) {
  $days[$day]["num3"] = $num3;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filterFunction", $filterFunction);
$smarty->assign("order_way"    , $order_way);
$smarty->assign("order_col"    , $order_col);
$smarty->assign("tri_recept"   , $tri_recept);
$smarty->assign("tri_complet"  , $tri_complet);
$smarty->assign('date'         , $date);
$smarty->assign('lastmonth'    , $lastmonth);
$smarty->assign('nextmonth'    , $nextmonth);
$smarty->assign('bank_holidays', $bank_holidays);
$smarty->assign('days'         , $days);

$smarty->display("reception_dossiers/inc_recept_dossiers_month.tpl");
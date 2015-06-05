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

// Initialisation de variables
$date = CValue::getOrSession("date", CMbDT::date());

$month_min       = CMbDT::date("first day of +0 month", $date);
$lastmonth       = CMbDT::date("last day of -1 month" , $date);
$nextmonth       = CMbDT::date("first day of +1 month", $date);
$enabled_service = CValue::getOrSession("active_filter_services", 0);
$type            = CValue::getOrSession("type");
$services_ids    = CValue::getOrSession("services_ids");
$prat_id         = CValue::getOrSession("prat_id");
$bank_holidays   = CMbDate::getHolidays($date);

CMbArray::removeValue("", $services_ids);

$hier   = CMbDT::date("- 1 day", $date);
$demain = CMbDT::date("+ 1 day", $date);

// Initialisation du tableau de jours
$days = array();
for ($day = $month_min; $day < $nextmonth; $day = CMbDT::date("+1 DAY", $day)) {
  $days[$day] = "0";
}

$where = array();
$ljoin = array();

// filtre sur les types d'admission
if ($type == "ambucomp") {
  $where[] = "`sejour`.`type` = 'ambu' OR `sejour`.`type` = 'comp'";
}
elseif ($type) {
    $where["sejour.type"] = "= '$type'";
  }
else {
  $where[] = "`sejour`.`type` != 'urg' AND `sejour`.`type` != 'seances'";
}

// filtre sur les services
if (count($services_ids) && $enabled_service) {
  $ljoin["affectation"]        = "affectation.sejour_id = sejour.sejour_id AND affectation.sortie = sejour.sortie";
  $where["affectation.service_id"] = CSQLDataSource::prepareIn($services_ids);
}

// filtre sur le praticien
if ($prat_id) {
  $where["sejour.praticien_id"] = "= '$prat_id'";
}

$group = CGroups::loadCurrent();
$sejour = new CSejour();

$where["sejour.annule"] = "= '0'";
$where["sejour.group_id"] = "= '$group->_id'";

// Liste des admissions par jour
foreach ($days as $_date => $num) {
  $date_min = CMbDT::dateTime("00:00:00", $_date);
  $date_max = CMbDT::dateTime("23:59:00", $_date);
  $where["sejour.entree"] = "<= '$date_max'";
  $where["sejour.sortie"] = ">= '$date_min'";
  $days[$_date] = $sejour->countList($where, null, $ljoin);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("hier"            , $hier);
$smarty->assign("demain"          , $demain);
$smarty->assign("bank_holidays"   , $bank_holidays);
$smarty->assign('date'            , $date);
$smarty->assign('lastmonth'       , $lastmonth);
$smarty->assign('nextmonth'       , $nextmonth);
$smarty->assign('days'            , $days);
$smarty->assign('enabled_service' , $enabled_service);

$smarty->display('inc_vw_all_presents.tpl');

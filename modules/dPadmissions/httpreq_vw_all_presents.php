<?php /* $Id: httpreq_vw_all_admissions.php 12978 2011-08-29 10:11:28Z flaviencrochard $ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: 12978 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Initialisation de variables
$date = CValue::getOrSession("date", mbDate());
$month_min     = mbDate("first day of +0 month", $date);
$month_max     = mbDate("last day of +0 month" , $date);
$lastmonth     = mbDate("last day of -1 month" , $date);
$nextmonth     = mbDate("first day of +1 month", $date);

$type          = CValue::getOrSession("type");
$service_id    = CValue::getOrSession("service_id");
$prat_id       = CValue::getOrSession("prat_id");
$bank_holidays = mbBankHolidays($date);
$service_id    = explode(",", $service_id);
CMbArray::removeValue("", $service_id);

$hier   = mbDate("- 1 day", $date);
$demain = mbDate("+ 1 day", $date);

// Initialisation du tableau de jours
$days = array();
for ($day = $month_min; $day <= $month_max; $day = mbDate("+1 DAY", $day)) {
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
if (count($service_id)) {
  $ljoin["affectation"]        = "affectation.sejour_id = sejour.sejour_id AND affectation.sortie = sejour.sortie_prevue";
  $ljoin["lit"]                = "affectation.lit_id = lit.lit_id";
  $ljoin["chambre"]            = "lit.chambre_id = chambre.chambre_id";
  $ljoin["service"]            = "chambre.service_id = service.service_id";
  $where["service.service_id"] = CSQLDataSource::prepareIn($service_id);
}

// filtre sur le praticien
if ($prat_id) {
  $where["praticien_id"] = "= '$prat_id'";
}

$group = CGroups::loadCurrent();
$sejour = new CSejour();

$where["sejour.annule"] = "= '0'";
$where["sejour.group_id"] = "= '$group->_id'";

// Liste des admissions par jour
foreach($days as $_date => $num) {
  $date_min = mbDateTime("00:00:00", $_date);
  $date_max = mbDateTime("23:59:00", $_date);
  $where["sejour.entree"] = "<= '$date_max'";
  $where["sejour.sortie"] = ">= '$date_min'";
  $days[$_date] = $sejour->countList($where, null, $ljoin);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("hier"         , $hier);
$smarty->assign("demain"       , $demain);

$smarty->assign("bank_holidays", $bank_holidays);
$smarty->assign('date'         , $date);
$smarty->assign('lastmonth'    , $lastmonth);
$smarty->assign('nextmonth'    , $nextmonth);
$smarty->assign('days'         , $days);

$smarty->display('inc_vw_all_presents.tpl');

?>
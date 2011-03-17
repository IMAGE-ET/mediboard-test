<?php /* $Id: httpreq_vw_all_admissions.php 11536 2011-03-08 15:02:14Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: 11536 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$ds = CSQLDataSource::get("std");

// Initialisation de variables
$date         = CValue::getOrSession("date", mbDate());
$month_min    = mbTransformTime("+ 0 month", $date, "%Y-%m-01");
$month_max    = mbTransformTime("+ 1 month", $month_min, "%Y-%m-01");
$lastmonth    = mbDate("-1 month", $date);
$nextmonth    = mbDate("+1 month", $date);
$type_sejour  = CValue::getOrSession("type_sejour", "ambu");
$bank_holidays = mbBankHolidays($date);

$hier = mbDate("- 1 day", $date);
$demain = mbDate("+ 1 day", $date);

// Initialisation du tableau de jours
$types = array(
  "ambu",
	"comp",
	"exte",
	"consult"
);

// Initialisation des totaux
$days = array();
for ($day = $month_min; $day < $month_max; $day = mbDate("+1 DAY", $day)) {
	foreach($types as $_type) {
    $days[$day][$_type] = 0;		
	}
}

// Comptage des totaux
$group = CGroups::loadCurrent();
foreach ($types as $_type) {
	$query = "SELECT DATE_FORMAT(`sejour`.`sortie`, '%Y-%m-%d') AS `date`, COUNT(`sejour`.`sejour_id`) AS `num`
		FROM `sejour`
		WHERE `sejour`.`sortie` BETWEEN '$month_min' AND '$month_max'
		  AND `sejour`.`group_id` = '$group->_id'
		  AND `sejour`.`type` = '$_type'
		  AND `sejour`.`annule` = '0'
		GROUP BY `sejour`.`type`, `date`
		ORDER BY `date`";
			
	foreach ($ds->loadHashList($query) as $day => $count) {
	  $days[$day][$_type] = $count;
	}
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("types"        , $types);
$smarty->assign("hier"         , $hier);
$smarty->assign("demain"       , $demain);

$smarty->assign("type_sejour"  , $type_sejour);

$smarty->assign("bank_holidays", $bank_holidays);
$smarty->assign('date'         , $date);
$smarty->assign('lastmonth'    , $lastmonth);
$smarty->assign('nextmonth'    , $nextmonth);
$smarty->assign('days'         , $days);

$smarty->display('inc_vw_all_sorties.tpl');

?>
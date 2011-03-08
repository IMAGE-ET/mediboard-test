<?php /* $Id: httpreq_vw_all_admissions.php 11536 2011-03-08 15:02:14Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: 11536 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $g;

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
$days = array();
for ($day = $month_min; $day < $month_max; $day = mbDate("+1 DAY", $day)) {
  $days[$day] = array(
    "ambu"  => "0",
    "comp" => "0",
    "exte" => "0",
  );
}

// Liste des ambulatoires par jour
$sql = "SELECT DATE_FORMAT(`sejour`.`sortie`, '%Y-%m-%d') AS `date`, COUNT(`sejour`.`sejour_id`) AS `num`
    FROM `sejour`
    WHERE `sejour`.`sortie` BETWEEN '$month_min' AND '$month_max'
      AND `sejour`.`group_id` = '$g'
      AND `sejour`.`type` = 'ambu'
      AND `sejour`.`annule` = '0'
    GROUP BY `sejour`.`type`, `date`
    ORDER BY `date`";
foreach ($ds->loadHashList($sql) as $day => $num1) {
  $days[$day]["ambu"] = $num1;
}
// Liste des hospi complètes par jour
$sql = "SELECT DATE_FORMAT(`sejour`.`sortie`, '%Y-%m-%d') AS `date`, COUNT(`sejour`.`sejour_id`) AS `num`
    FROM `sejour`
    WHERE `sejour`.`sortie` BETWEEN '$month_min' AND '$month_max'
      AND `sejour`.`group_id` = '$g'
      AND `sejour`.`type` = 'comp'
      AND `sejour`.`annule` = '0'
    GROUP BY `sejour`.`type`, `date`
    ORDER BY `date`";
foreach ($ds->loadHashList($sql) as $day => $num1) {
  $days[$day]["comp"] = $num1;
}
// Liste des externes par jour
$sql = "SELECT DATE_FORMAT(`sejour`.`sortie`, '%Y-%m-%d') AS `date`, COUNT(`sejour`.`sejour_id`) AS `num`
    FROM `sejour`
    WHERE `sejour`.`sortie` BETWEEN '$month_min' AND '$month_max'
      AND `sejour`.`group_id` = '$g'
      AND `sejour`.`type` = 'exte'
      AND `sejour`.`annule` = '0'
    GROUP BY `sejour`.`type`, `date`
    ORDER BY `date`";
foreach ($ds->loadHashList($sql) as $day => $num1) {
  $days[$day]["exte"] = $num1;
}

// Création du template
$smarty = new CSmartyDP();

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
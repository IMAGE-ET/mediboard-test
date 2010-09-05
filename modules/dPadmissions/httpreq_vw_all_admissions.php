<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $g;

CCanDo::checkRead();

$ds = CSQLDataSource::get("std");

// Initialisation de variables
$date = CValue::getOrSession("date", mbDate());
$month_min = mbTransformTime("+ 0 month", $date, "%Y-%m-01");
$month_max = mbTransformTime("+ 1 month", $month_min, "%Y-%m-01");
$lastmonth = mbDate("-1 month", $date);
$nextmonth = mbDate("+1 month", $date);
$selAdmis  = CValue::getOrSession("selAdmis", "0");
$selSaisis = CValue::getOrSession("selSaisis", "0");
$type      = CValue::getOrSession("type", "ambucomp");

$hier = mbDate("- 1 day", $date);
$demain = mbDate("+ 1 day", $date);

// Initialisation du tableau de jours
$days = array();
for ($day = $month_min; $day < $month_max; $day = mbDate("+1 DAY", $day)) {
  $days[$day] = array(
    "num1" => "0",
    "num2" => "0",
    "num3" => "0",
  );
}

// filtre sur les types d'admission
if($type == "ambucomp") {
  $filterType = "`sejour`.`type` = 'ambu' OR `sejour`.`type` = 'comp'";
} elseif($type) {
  $filterType = "`sejour`.`type` = '$type'";
} else {
  $filterType = "`sejour`.`type` != 'urg'";
}

// Liste des admissions par jour
$sql = "SELECT DATE_FORMAT(`sejour`.`entree`, '%Y-%m-%d') AS `date`, COUNT(`sejour`.`sejour_id`) AS `num`
    FROM `sejour`
    WHERE `sejour`.`entree` BETWEEN '$month_min' AND '$month_max'
      AND `sejour`.`group_id` = '$g'
      AND $filterType
    GROUP BY `date`
    ORDER BY `date`";
foreach ($ds->loadHashList($sql) as $day => $num1) {
	$days[$day]["num1"] = $num1;
}

// Liste des admissions non effectuées par jour
$sql = "SELECT DATE_FORMAT(`sejour`.`entree`, '%Y-%m-%d') AS `date`, COUNT(`sejour`.`sejour_id`) AS `num`
    FROM `sejour`
    WHERE `sejour`.`entree_prevue` BETWEEN '$month_min' AND '$month_max'
      AND `sejour`.`group_id` = '$g'
      AND `sejour`.`entree_reelle` IS NULL
      AND `sejour`.`annule` = '0'
      AND $filterType
    GROUP BY `date`
    ORDER BY `date`";
foreach ($ds->loadHashList($sql) as $day => $num2) {
  $days[$day]["num2"] = $num2;
}

// Liste des admissions non préparées
$sql = "SELECT DATE_FORMAT(`sejour`.`entree`, '%Y-%m-%d') AS `date`, COUNT(`sejour`.`sejour_id`) AS `num`
    FROM `sejour`
    WHERE `sejour`.`entree` BETWEEN '$month_min' AND '$month_max'
      AND `sejour`.`group_id` = '$g'
      AND `sejour`.`saisi_SHS` = '0'
      AND `sejour`.`annule` = '0'
      AND $filterType
    GROUP BY `date`
    ORDER BY `date`";
foreach ($ds->loadHashList($sql) as $day => $num3) {
  $days[$day]["num3"] = $num3;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("hier", $hier);
$smarty->assign("demain", $demain);

$smarty->assign("selAdmis", $selAdmis);
$smarty->assign("selSaisis", $selSaisis);

$smarty->assign('date', $date);
$smarty->assign('lastmonth', $lastmonth);
$smarty->assign('nextmonth', $nextmonth);
$smarty->assign('days', $days);

$smarty->display('inc_vw_all_admissions.tpl');

?>
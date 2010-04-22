<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m, $g;
$ds = CSQLDataSource::get("std");

$can->needsRead();

// Initialisation de variables
$date = CValue::getOrSession("date", mbDate());
$month = mbTransformTime("+ 0 day", $date, "%Y-%m-__ __:__:__");
$lastmonth = mbDate("-1 month", $date);
$nextmonth = mbDate("+1 month", $date);
$selAdmis = CValue::getOrSession("selAdmis", "0");
$selSaisis = CValue::getOrSession("selSaisis", "0");

$hier = mbDate("- 1 day", $date);
$demain = mbDate("+ 1 day", $date);

// Liste des admissions par jour
$sql = "SELECT COUNT(`sejour`.`sejour_id`) AS `num`, DATE_FORMAT(`sejour`.`entree`, '%Y-%m-%d') AS `date`" .
    "\nFROM `sejour`" .
    "\nWHERE `sejour`.`entree` LIKE '$month' AND `sejour`.`group_id` = '$g'" .
    "\nAND `sejour`.`type` != 'urg'" .
    "\nGROUP BY `date`" .
    "\nORDER BY `date`";
$list1 = $ds->loadlist($sql);

// Liste des admissions non effectuées par jour
$sql = "SELECT COUNT(`sejour`.`sejour_id`) AS `num`, DATE_FORMAT(`sejour`.`entree_prevue`, '%Y-%m-%d') AS `date`" .
    "\nFROM `sejour`" .
    "\nWHERE `sejour`.`entree_prevue` LIKE '$month' AND `sejour`.`group_id` = '$g'" .
    "\nAND `sejour`.`entree_reelle` IS NULL" .
    "\nAND `sejour`.`annule` = '0'" .
    "\nAND `sejour`.`type` != 'urg'" .
    "\nGROUP BY `date`" .
    "\nORDER BY `date`";
$list2 = $ds->loadlist($sql);

// Liste des admissions non préparées
$sql = "SELECT COUNT(`sejour`.`sejour_id`) AS `num`, DATE_FORMAT(`sejour`.`entree`, '%Y-%m-%d') AS `date`" .
    "\nFROM `sejour`" .
    "\nWHERE `sejour`.`entree` LIKE '$month' AND `sejour`.`group_id` = '$g'" .
    "\nAND `sejour`.`saisi_SHS` = '0'" .
    "\nAND `sejour`.`annule` = '0'" .
    "\nAND `sejour`.`type` != 'urg'" .
    "\nGROUP BY `date`" .
    "\nORDER BY `date`";
$list3 = $ds->loadlist($sql);

// On met toutes les sommes d'intervention dans le même tableau
foreach($list1 as $key => $value) {
  $i2 = 0;
  $i2fin = sizeof($list2);
  while((@$list2[$i2]["date"] != $value["date"]) && ($i2 < $i2fin)) {
    $i2++;
  }
  if(@$list2[$i2]["date"] == $value["date"])
    $list1[$key]["num2"] = $list2[$i2]["num"];
  else
    $list1[$key]["num2"] = 0;
  $i3 = 0;
  $i3fin = sizeof($list3);
  while((@$list3[$i3]["date"] != $value["date"]) && ($i3 < $i3fin)) {
    $i3++;
  }
  if(@$list3[$i3]["date"] == $value["date"])
    $list1[$key]["num3"] = $list3[$i3]["num"];
  else
    $list1[$key]["num3"] = 0;
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
$smarty->assign('list1', $list1);

$smarty->display('inc_vw_all_admissions.tpl');

?>
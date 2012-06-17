<?php /* $Id: print_select_docs.php 14587 2012-02-08 08:33:51Z alexis_granger $ */

 /**
  * @package Mediboard
  * @subpackage dPcabinet
  * @version $Revision: 14587 $
  * @author Sébastien Fillonneau
  */

CCanDo::checkAdmin();

$consult = new CConsultation;
$ds = $consult->_spec->ds;

$date = CValue::get("date");
$period = CValue::get("period", "month");
switch ($period) {
  case "day": 
    $php_period = "days";
    $sql_cast = "date";
    break;
  case "week":
    $date = mbDate("next monday", $date);
    $php_period = "weeks";
    $sql_cast = "DATE_ADD( date, INTERVAL (2 - DAYOFWEEK(date)) DAY)";
    break;
  case "month":
    $date = mbDate("first day of +0 month", $date);
    $php_period = "months";
    $sql_cast = "DATE_ADD( date, INTERVAL (1 - DAYOFMONTH(date)) DAY)";
    break;
  case "year":
    $date = mbTransformTime(null, $date, "%Y-01-01");
    $php_period = "years";
    $sql_cast = "DATE_ADD( date, INTERVAL (1 - DAYOFYEAR(date)) DAY)";
    break;
} 

$dates = array();
foreach (range(0, 29) as $n) {
  $dates[] = $min = mbDate("- $n $php_period", $date);
}

$dates = array_reverse($dates);

$query = "SELECT COUNT(*) total, chir_id, $sql_cast AS refdate
  FROM `consultation`
  LEFT JOIN plageconsult AS plage ON plage.plageconsult_id = consultation.plageconsult_id
  WHERE $sql_cast >= '$min'
  AND annule != '1'
  AND patient_id IS NOT NULL
  GROUP BY chir_id, refdate
  ORDER BY refdate DESC
";

$totaux = array();
foreach ($result = $ds->loadList($query) as $_row) {
  $totaux[$_row["chir_id"]][$_row["refdate"]] = $_row["total"];
}

$user = CMediusers::get();
$users = $user->loadAll(array_keys($totaux));
foreach ($users as $_user) {
  $_user->loadRefFunction();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("period", $period);
$smarty->assign("dates" , $dates );
$smarty->assign("users" , $users );
$smarty->assign("totaux", $totaux);

$smarty->display("macro_stats.tpl");
?>

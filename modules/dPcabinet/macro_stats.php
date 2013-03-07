<?php

/**
 * $Id: $
 *
 * @package    Mediboard
 * @subpackage cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: $
 */

CCanDo::checkAdmin();

$consult = new CConsultation();
$group = CGroups::loadCurrent();
$ds = $consult->_spec->ds;

$type_stats = CValue::get("type_stats", "RDV");
$date       = CValue::get("date");
$period     = CValue::get("period", "month");

switch ($period) {
  case "day": 
    $php_period = "days";
    $sql_cast = "date";
    break;
  case "week":
    $date = CMbDT::date("next monday", $date);
    $php_period = "weeks";
    $sql_cast = "DATE_ADD( date, INTERVAL (2 - DAYOFWEEK(date)) DAY)";
    break;
  case "month":
    $date = CMbDT::date("first day of +0 month", $date);
    $php_period = "months";
    $sql_cast = "DATE_ADD( date, INTERVAL (1 - DAYOFMONTH(date)) DAY)";
    break;
  case "year":
    $date = CMbDT::transform(null, $date, "%Y-01-01");
    $php_period = "years";
    $sql_cast = "DATE_ADD( date, INTERVAL (1 - DAYOFYEAR(date)) DAY)";
    break;
} 

$dates = array();
foreach (range(0, 29) as $n) {
  $dates[] = $min = CMbDT::date("- $n $php_period", $date);
}

$dates = array_reverse($dates);

$query_complement = "1";
if($type_stats == "consult") {
  $query_complement = "consultation.examen IS NOT NULL
                     OR consultation.traitement IS NOT NULL
                     OR consultation.histoire_maladie IS NOT NULL
                     OR consultation.conclusion IS NOT NULL
                     OR consultation.conclusion IS NOT NULL
                     OR consultation.chrono > 32
                     OR consultation.facture_id IS NOT NULL";
}
if($type_stats == "fse") {
  $query_complement = "1";
}

$query = "SELECT COUNT(*) total, chir_id, $sql_cast AS refdate
  FROM `consultation`
  LEFT JOIN plageconsult AS plage ON plage.plageconsult_id = consultation.plageconsult_id
  LEFT JOIN users_mediboard AS user ON user.user_id = plage.chir_id
  LEFT JOIN functions_mediboard AS function ON function.function_id = user.function_id
  WHERE $sql_cast >= '$min'
  AND function.group_id = '$group->_id'
  AND consultation.annule != '1'
  AND consultation.patient_id IS NOT NULL
  AND $query_complement
  GROUP BY chir_id, refdate
  ORDER BY refdate DESC
";

$totals = array();
foreach ($result = $ds->loadList($query) as $_row) {
  $totals[$_row["chir_id"]][$_row["refdate"]] = $_row["total"];
}

$user = CMediusers::get();
$users     = $user->loadAll(array_keys($totals));
$functions = CStoredObject::massLoadFwdRef($users, "function_id");

foreach ($users as $_user) {
  $_user->loadRefFunction();
  
  $function = $functions[$_user->function_id];
  $function->_ref_users[$_user->_id] = $_user;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("period"   , $period);
$smarty->assign("dates"    , $dates );
$smarty->assign("users"    , $users );
$smarty->assign("functions", $functions);
$smarty->assign("group"    , $group);
$smarty->assign("totals"   , $totals);

$smarty->display("macro_stats.tpl");
?>

<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if(!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

require_once($AppUI->getModuleClass("mediusers"));

$chir   = mbGetValueFromGet("chir" , 0);
$month  = mbGetValueFromGet("month", date("m"));
$year   = mbGetValueFromGet("year" , date("Y"));
$oper_id= mbGetValueFromGet("oper_id", null);

$pmonth = $month - 1;
if($pmonth == 0) {
  $pyear = $year - 1;
  $pmonth = 12;
}
else
  $pyear = $year;
if(strlen($pmonth) == 1)
  $pmonth = "0".$pmonth;
$nmonth = $month + 1;
if($nmonth == 13) {
  $nyear = $year + 1;
  $nmonth = "01";
}
else
  $nyear = $year;
if(strlen($nmonth) == 1)
  $nmonth = "0".$nmonth;
$curr_op_hour = mbGetValueFromGet("curr_op_hour", "25");
$curr_op_min  = mbGetValueFromGet("curr_op_min" , "00");
$today = date("Y-m-d");
$monthList = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin",
                   "Juillet", "Aout", "Septembre", "Octobre", "Novembre",
                   "Décembre");
$nameMonth = $monthList[$month-1];

$mediChir = new CMediusers();
$mediChir->load($chir);

// Selection des plages opératoires ayant suffisament de temps pour  caser l'opération
$sql = "SELECT plagesop.*, sallesbloc.nom," .
		"\nSUM(TIME_TO_SEC(operations.temp_operation)) AS duree," .
    "\nTIME_TO_SEC(plagesop.fin)-TIME_TO_SEC(plagesop.debut) AS plage," .
		"\nCOUNT(operations.operation_id) AS total" .
		"\nFROM plagesop" .
		"\nLEFT JOIN operations" .
		"\nON plagesop.id = operations.plageop_id" .
    "\nAND operations.annulee = 0" .
    "\nLEFT JOIN sallesbloc" .
    "\nON plagesop.id_salle = sallesbloc.id" .
		"\nWHERE plagesop.date LIKE '$year-$month-__'" .
		"\nAND (plagesop.chir_id = '$mediChir->user_id' OR plagesop.id_spec = '$mediChir->function_id')" .
		"\nGROUP BY plagesop.id" .
		"\nORDER BY plagesop.date, plagesop.debut, sallesbloc.nom, plagesop.id";
$list = db_loadlist($sql);

foreach($list as $key => $value) {
  $list[$key]["free_time"] = $value["plage"] - $value["duree"];
  $list[$key]["free_time"] -= $curr_op_hour*3600 + $curr_op_min*60;
}

// Création du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("oper_id"     , $oper_id);
$smarty->assign("month"       , $month);
$smarty->assign("nameMonth"   , $nameMonth);
$smarty->assign("pmonth"      , $pmonth);
$smarty->assign("nmonth"      , $nmonth);
$smarty->assign("year"        , $year);
$smarty->assign("pyear"       , $pyear);
$smarty->assign("nyear"       , $nyear);
$smarty->assign("curr_op_hour", $curr_op_hour);
$smarty->assign("curr_op_min" , $curr_op_min);
$smarty->assign("chir"        , $chir);
$smarty->assign("list"        , $list);

$smarty->display("plage_selector.tpl");

?>
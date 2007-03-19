<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

$chir         = mbGetValueFromGet("chir"         , 0);
$month        = mbGetValueFromGet("month"        , date("m"));
$year         = mbGetValueFromGet("year"         , date("Y"));
$group_id     = mbGetValueFromGet("group_id"     , $g);
$operation_id = mbGetValueFromGet("operation_id" , null);
$curr_op_hour = mbGetValueFromGet("curr_op_hour" , "25");
$curr_op_min  = mbGetValueFromGet("curr_op_min"  , "00");

$today        = date("Y-m-d");
$monthList    = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin",
                       "Juillet", "Aout", "Septembre", "Octobre", "Novembre",
                       "Décembre");
$nameMonth    = $monthList[$month-1];
 
 // Chargemetn du chirurgien
$mediChir = new CMediusers();
$mediChir->load($chir);

// Chargement des plages pour le chir ou sa spécialité
$listSalles = new CSalle;
$where = array();
$where["group_id"] = db_prepare("= %",$group_id);
$listSalles = $listSalles->loadList($where);

$listPlages = new CPlageOp;
$where = array();
$where[]           = db_prepare("(plagesop.chir_id = %1 OR plagesop.spec_id = %2)",$mediChir->user_id,$mediChir->function_id);
$where["date"]     = "LIKE '$year-$month-__'";
$where["salle_id"] = db_prepare_in(array_keys($listSalles));
$order = "date, debut";
$listPlages = $listPlages->loadList($where, $order);

$nb_secondes = $curr_op_hour*3600 + $curr_op_min*60;

foreach($listPlages as $keyPlage=>&$plageop){
  $plageop->loadRefSalle();
  $plageop->getNbOperations($nb_secondes, false);
}

// Calcul des mois et années pour navigation
$pmonth = $month - 1;
if($pmonth == 0) {
  $pyear = $year - 1;
  $pmonth = 12;
}else{
  $pyear = $year;
}
if(strlen($pmonth) == 1){
  $pmonth = "0".$pmonth;
}
$nmonth = $month + 1;
if($nmonth == 13) {
  $nyear = $year + 1;
  $nmonth = "01";
}else{
  $nyear = $year;
}
if(strlen($nmonth) == 1){
  $nmonth = "0".$nmonth;
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("operation_id" , $operation_id);
$smarty->assign("month"        , $month);
$smarty->assign("nameMonth"    , $nameMonth);
$smarty->assign("pmonth"       , $pmonth);
$smarty->assign("nmonth"       , $nmonth);
$smarty->assign("year"         , $year);
$smarty->assign("pyear"        , $pyear);
$smarty->assign("nyear"        , $nyear);
$smarty->assign("chir"         , $chir);
$smarty->assign("group_id"     , $group_id);
$smarty->assign("curr_op_hour" , $curr_op_hour);
$smarty->assign("curr_op_min"  , $curr_op_min);
$smarty->assign("listPlages"   , $listPlages);

$smarty->display("plage_selector.tpl");
?>
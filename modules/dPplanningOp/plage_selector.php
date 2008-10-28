<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $can, $m, $g;
$can->needsRead();
$ds = CSQLDataSource::get("std");

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
 
 // Chargement du chirurgien
$mediChir = new CMediusers();
$mediChir->load($chir);

// Chargement des plages pour le chir ou sa spécialité
$salle = new CSalle;
$where = array('bloc_id' => $ds->prepareIn(array_keys(CGroups::loadCurrent()->loadBlocs(PERM_READ))));
$listSalles = $salle->loadListWithPerms(PERM_READ, $where);

$listPlages = new CPlageOp;
$where = array();
$where[]           = $ds->prepare("(plagesop.chir_id = %1 OR plagesop.spec_id = %2)",$mediChir->user_id,$mediChir->function_id);
$where["date"]     = "LIKE '$year-$month-__'";
$where["salle_id"] = $ds->prepareIn(array_keys($listSalles));
$order = "date, debut";
$listPlages = $listPlages->loadList($where, $order);


$nb_secondes = $curr_op_hour*3600 + $curr_op_min*60;

foreach($listPlages as $keyPlage=>&$plageop){
  $plageop->loadRefSalle();
  $plageop->getNbOperations($nb_secondes, false);
  $plageop->loadRefsBack(0);
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

// Heures d'admission
$config              = CAppUI::conf("dPplanningOp CSejour");
$hours               = range($config["heure_deb"], $config["heure_fin"]);
$mins                = range(0, 59, $config["min_intervalle"]);
$heure_entree_veille = $config["heure_entree_veille"];
$heure_entree_jour   = $config["heure_entree_jour"];


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("operation_id", $operation_id);
$smarty->assign("month"       , $month);
$smarty->assign("nameMonth"   , $nameMonth);
$smarty->assign("pmonth"      , $pmonth);
$smarty->assign("nmonth"      , $nmonth);
$smarty->assign("year"        , $year);
$smarty->assign("pyear"       , $pyear);
$smarty->assign("nyear"       , $nyear);
$smarty->assign("chir"        , $chir);
$smarty->assign("group_id"    , $group_id);
$smarty->assign("curr_op_hour", $curr_op_hour);
$smarty->assign("curr_op_min" , $curr_op_min);
$smarty->assign("listPlages"  , $listPlages);

$smarty->assign("hours"              , $hours);
$smarty->assign("mins"               , $mins);
$smarty->assign("heure_entree_veille", $heure_entree_veille);
$smarty->assign("heure_entree_jour"  , $heure_entree_jour);

$smarty->display("plage_selector.tpl");
?>
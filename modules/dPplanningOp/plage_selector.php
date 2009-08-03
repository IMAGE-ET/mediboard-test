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
$date         = mbGetValueFromGet("date"         , mbDate());
$month        = mbGetValueFromGet("month"        , date("m"));
$year         = mbGetValueFromGet("year"         , date("Y"));
$group_id     = mbGetValueFromGet("group_id"     , $g);
$operation_id = mbGetValueFromGet("operation_id" , null);
$curr_op_hour = mbGetValueFromGet("curr_op_hour" , "25");
$curr_op_min  = mbGetValueFromGet("curr_op_min"  , "00");

$date_min = mbDate("+ ".CAppUI::conf("dPbloc CPlageOp days_locked")." DAYS");

// Liste des mois selectionnables

$date = mbTransformTime(null, $date, "%Y-%m-01");
$listMonthes = array();
for($i = -6; $i <= 12; $i++) {
  $curr_key   = mbTransformTime("$i month", $date, "%Y-%m-%d");
  $curr_month = mbTransformTime("$i month", $date, "%B %Y");
  $listMonthes[$i]["date"] = $curr_key;
  $listMonthes[$i]["month"] = $curr_month;
}
 
 // Chargement du chirurgien
$mediChir = new CMediusers();
$mediChir->load($chir);
$mediChir->loadBackRefs("secondary_functions");
$secondary_functions = array();
foreach($mediChir->_back["secondary_functions"] as $curr_sec_func) {
  $secondary_functions[] = $curr_sec_func->function_id;
}

// Chargement des plages pour le chir ou sa spécialité

$listPlages = new CPlageOp;
$where = array();
$selectPlages  = "(plagesop.chir_id = %1 OR plagesop.spec_id = %2 OR plagesop.spec_id ".$ds->prepareIn($secondary_functions).")";
$where[]       = $ds->prepare($selectPlages ,$mediChir->user_id,$mediChir->function_id);
$where["date"] = "LIKE '".mbTransformTime(null, $date, "%Y-%m-__")."'";
$order = "date, debut";
$listPlages = $listPlages->loadList($where, $order);


$nb_secondes = $curr_op_hour*3600 + $curr_op_min*60;

foreach ($listPlages as $keyPlage=>&$plageop){
  $plageop->loadRefSalle();
  $plageop->getNbOperations($nb_secondes, false);
  $plageop->loadRefsBack(0);
}

// Heures d'admission
$config              = CAppUI::conf("dPplanningOp CSejour");
$hours               = range($config["heure_deb"], $config["heure_fin"]);
$mins                = range(0, 59, $config["min_intervalle"]);
$heure_entree_veille = $config["heure_entree_veille"];
$heure_entree_jour   = $config["heure_entree_jour"];


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"        , $date);
$smarty->assign("date_min"    , $date_min);
$smarty->assign("listMonthes" , $listMonthes);
$smarty->assign("operation_id", $operation_id);
$smarty->assign("chir"        , $chir);
$smarty->assign("group_id"    , $group_id);
$smarty->assign("curr_op_hour", $curr_op_hour);
$smarty->assign("curr_op_min" , $curr_op_min);
$smarty->assign("listPlages"  , $listPlages);

$smarty->assign("hours"              , $hours);
$smarty->assign("mins"               , $mins);
$smarty->assign("heure_entree_veille", $heure_entree_veille);
$smarty->assign("heure_entree_jour"  , $heure_entree_jour);

$smarty->assign("resp_bloc", CModule::getInstalled("dPbloc")->canEdit());

$smarty->display("plage_selector.tpl");
?>
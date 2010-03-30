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

$chir         = CValue::get("chir"         , 0);
$date         = CValue::get("date"         , mbDate());
$month        = CValue::get("month"        , date("m"));
$year         = CValue::get("year"         , date("Y"));
$group_id     = CValue::get("group_id"     , $g);
$operation_id = CValue::get("operation_id" , null);
$curr_op_hour = CValue::get("curr_op_hour" , "25");
$curr_op_min  = CValue::get("curr_op_min"  , "00");

$date_min = mbDate("+ ".CAppUI::conf("dPbloc CPlageOp days_locked")." DAYS");

$resp_bloc = CModule::getInstalled("dPbloc")->canEdit();

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

// Chargement de la list des blocs opératoires
$bloc = new CBlocOperatoire();
$blocs = $bloc->loadGroupList(null, "nom");
foreach($blocs as $_bloc) {
  $_bloc->loadRefsSalles();
}

// Chargement des plages pour le chir ou sa spécialité par bloc
$where = array();
$selectPlages  = "(plagesop.chir_id = %1 OR plagesop.spec_id = %2 OR plagesop.spec_id ".CSQLDataSource::prepareIn($secondary_functions).")";
$where[]       = $ds->prepare($selectPlages ,$mediChir->user_id,$mediChir->function_id);
$where["date"] = "LIKE '".mbTransformTime(null, $date, "%Y-%m-__")."'";
if(!$resp_bloc) {
  $where[] = "date >= '".mbDate()."'";
}
$order = "date, debut";
$plage = new CPlageOp;
$listPlages = array();
foreach($blocs as $_bloc) {
  $where["salle_id"] = CSQLDataSource::prepareIn(array_keys($_bloc->_ref_salles));
  $listPlages[$_bloc->_id] = $plage->loadList($where, $order);
  if(!count($listPlages[$_bloc->_id])) {
    unset($listPlages[$_bloc->_id]);
  }
}

$nb_secondes = $curr_op_hour*3600 + $curr_op_min*60;

foreach ($listPlages as &$_bloc) {
  foreach($_bloc as &$_plage){
    $_plage->loadRefSalle();
    $_plage->getNbOperations($nb_secondes, false);
    $_plage->loadRefSpec(1);
    $_plage->loadRefsBack(0);
  }
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
$smarty->assign("blocs"       , $blocs);
$smarty->assign("listPlages"  , $listPlages);

$smarty->assign("hours"              , $hours);
$smarty->assign("mins"               , $mins);
$smarty->assign("heure_entree_veille", $heure_entree_veille);
$smarty->assign("heure_entree_jour"  , $heure_entree_jour);

$smarty->assign("resp_bloc", $resp_bloc);

$smarty->display("plage_selector.tpl");
?>
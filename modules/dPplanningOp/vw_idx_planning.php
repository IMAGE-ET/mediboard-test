<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsEdit();
$ds = CSQLDataSource::get("std");
$date      = CValue::getOrSession("date", mbDate());
$lastmonth = mbDate("-1 month", $date);
$nextmonth = mbDate("+1 month", $date);

// Sélection du praticien
$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);
$listPrat = $mediuser->loadPraticiens(PERM_EDIT);

$selPrat = CValue::getOrSession("selPrat", $mediuser->isPraticien() ? $mediuser->user_id : null);

$selPraticien = new CMediusers();
$selPraticien->load($selPrat);

if($selPraticien->isAnesth()) {
  // Selection des différentes interventions de la journée par service
  $service = new CService();
  $services = $service->loadGroupList();
  $interv = new COperation();
  $order = "operations.chir_id, operations.time_operation";
  $ljoin = array(
             "plagesop"    => "plagesop.plageop_id = operations.plageop_id",
             "sejour"      => "sejour.sejour_id = operations.sejour_id",
             "affectation" => "affectation.sejour_id = sejour.sejour_id AND '$date' BETWEEN DATE(affectation.entree) AND DATE(affectation.sortie)",
             "lit"         => "lit.lit_id = affectation.lit_id",
             "chambre"     => "chambre.chambre_id = lit.chambre_id",
             "service"     => "service.service_id = chambre.service_id",
           );
  $whereAmbu = array(
                 "plagesop.date = '$date' OR operations.date = '$date'",
                 "sejour.type"   => "= 'ambu'",
               );
  $whereHospi = array(
                 "plagesop.date = '$date' OR operations.date = '$date'",
                 "sejour.type"   => "= 'comp'",
               );
  $whereUrg   = array(
                 "plagesop.plageop_id" => "IS NULL",
                 "operations.date"     => "= '$date'",
               );
  foreach($services as $_service) {
    $whereAmbu["service.service_id"]          = "= '$_service->_id'";
    $whereHospi["service.service_id"]         = "= '$_service->_id'";
    $whereUrg["service.service_id"]           = "= '$_service->_id'";
    $listInterv["ambu"][$_service->_id]       = $interv->loadList($whereAmbu , $order, null, null, $ljoin);
    foreach($listInterv["ambu"][$_service->_id] as &$_interv) {
      $_interv->loadRefAffectation();
      $_interv->loadRefsFwd(1);
      $_interv->loadRefsConsultAnesth();
    }
    $listInterv["hospi"][$_service->_id]      = $interv->loadList($whereHospi, $order, null, null, $ljoin);
    foreach($listInterv["hospi"][$_service->_id] as &$_interv) {
      $_interv->loadRefAffectation();
      $_interv->loadRefsFwd(1);
      $_interv->loadRefsConsultAnesth();
    }
    $listInterv["hors plage"][$_service->_id] = $interv->loadList($whereUrg  , $order, null, null, $ljoin);
    foreach($listInterv["hors plage"][$_service->_id] as &$_interv) {
      $_interv->loadRefAffectation();
      $_interv->loadRefsFwd(1);
      $_interv->loadRefsConsultAnesth();
    }
  }
  $whereAmbu["service.service_id"]       = "IS NULL";
  $whereHospi["service.service_id"]      = "IS NULL";
  $whereUrg["service.service_id"]        = "IS NULL";
  $listInterv["ambu"]["non_place"]       = $interv->loadList($whereAmbu , $order, null, null, $ljoin);
  foreach($listInterv["ambu"]["non_place"] as &$_interv) {
    $_interv->loadRefAffectation();
    $_interv->loadRefsFwd(1);
    $_interv->loadRefsConsultAnesth();
  }
  $listInterv["hospi"]["non_place"]      = $interv->loadList($whereHospi, $order, null, null, $ljoin);
  foreach($listInterv["hospi"]["non_place"] as &$_interv) {
    $_interv->loadRefAffectation();
    $_interv->loadRefsFwd(1);
    $_interv->loadRefsConsultAnesth();
  }
  $listInterv["hors plage"]["non_place"] = $interv->loadList($whereUrg  , $order, null, null, $ljoin);
  foreach($listInterv["hors plage"]["non_place"] as &$_interv) {
    $_interv->loadRefAffectation();
    $_interv->loadRefsFwd(1);
    $_interv->loadRefsConsultAnesth();
  }

  // Création du template
  $smarty = new CSmartyDP();
  
  $smarty->assign("date"        , $date        );
  $smarty->assign("listPrat"    , $listPrat    );
  $smarty->assign("listInterv"  , $listInterv  );
  $smarty->assign("services"    , $services    );
  $smarty->assign("selPrat"     , $selPrat     );
  
  $smarty->display("vw_idx_visite_anesth.tpl");
  
} else {
  // Selection des plages du praticien et de celles de sa spécialité
  $selPratLogin   = null;
  $specialite     = null;
  $secondary_specs = array();
  if ($selPraticien->isPraticien()) {
    $selPratLogin = $selPraticien->user_id;
    $specialite = $selPraticien->function_id;
    $selPraticien->loadBackRefs("secondary_functions");
    foreach($selPraticien->_back["secondary_functions"] as  $curr_sec_spec) {
      $curr_sec_spec->loadRefsFwd();
      $curr_function = $curr_sec_spec->_ref_function;
      $secondary_specs[$curr_function->_id] = $curr_function;
    }
  }
  
  // Planning du mois
  $sql = "SELECT plagesop.*, plagesop.date AS opdate," .
      "\nSEC_TO_TIME(SUM(TIME_TO_SEC(operations.temp_operation))) AS duree," .
      "\nCOUNT(operations.operation_id) AS total," .
      "\nfunctions_mediboard.text AS nom_function, functions_mediboard.color as color_function" .
      "\nFROM plagesop" .
      "\nLEFT JOIN operations" .
      "\nON plagesop.plageop_id = operations.plageop_id" .
      "\nAND operations.annulee = '0'" .
      "\nAND operations.chir_id = '$selPratLogin'" .
      "\nLEFT JOIN functions_mediboard" .
      "\nON functions_mediboard.function_id = plagesop.spec_id" .
      "\nWHERE (plagesop.chir_id = '$selPratLogin' OR plagesop.spec_id = '$specialite' OR plagesop.spec_id ".CSQLDataSource::prepareIn(array_keys($secondary_specs)).")" .
      "\nAND plagesop.date LIKE '".mbTransformTime("+ 0 day", $date, "%Y-%m")."-__'" .
      "\nGROUP BY plagesop.plageop_id" .
      "\nORDER BY plagesop.date, plagesop.debut, plagesop.plageop_id";
  if($selPratLogin) {
    $listPlages = $ds->loadList($sql);
  } else {
    $listPlages = array();
  }
  
  // Urgences du mois
  $sql = "SELECT operations.*, operations.date AS opdate," .
      "\nSEC_TO_TIME(SUM(TIME_TO_SEC(operations.temp_operation))) AS duree," .
      "\nCOUNT(operations.operation_id) AS total" .
      "\nFROM operations" .
      "\nWHERE operations.annulee = '0'" .
      "\nAND operations.chir_id = '$selPratLogin'" .
      "\nAND operations.date LIKE '".mbTransformTime("+ 0 day", $date, "%Y-%m")."-__'" .
      "\nGROUP BY operations.date" .
      "\nORDER BY operations.date";
  if($selPratLogin) {
    $listUrgences = $ds->loadList($sql);
  } else {
    $listUrgences = array();
  }
  
  $listDays = array();
  foreach($listPlages as $curr_ops) {
    $listDays[$curr_ops["opdate"]][$curr_ops["plageop_id"]] = $curr_ops;
  }
  foreach($listUrgences as $curr_ops) {
    $listDays[$curr_ops["opdate"]]["hors_plage"] = $curr_ops;
  }
  
  ksort($listDays);
  
  // Création du template
  $smarty = new CSmartyDP();
  
  $smarty->assign("date"        , $date        );
  $smarty->assign("lastmonth"   , $lastmonth   );
  $smarty->assign("nextmonth"   , $nextmonth   );
  $smarty->assign("listPrat"    , $listPrat    );
  $smarty->assign("selPrat"     , $selPrat     );
  $smarty->assign("listDays"    , $listDays    );
  
  $smarty->display("vw_idx_planning.tpl");
}

?>
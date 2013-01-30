<?php /** $Id$ **/

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkEdit();
$ds = CSQLDataSource::get("std");

$date           = CValue::getOrSession("date", mbDate());
$canceled       = CValue::getOrSession("canceled", 0);

//5.1 / 5.2 hack @TODO : remove this if all phpversion are > 5.3
if (phpversion() >= "5.3") {
    $nextmonth = mbDate("first day of next month"   , $date);
    $lastmonth = mbDate("first day of previous month", $date);
} else {
    $nextmonth = mbDate("+1 month"   , mbTransformTime(null, $date, "%Y-%m-01" ));
    $lastmonth = mbDate("-1 month"   , mbTransformTime(null, $date, "%Y-%m-01" ));
}
$sans_anesth    = CValue::getOrSession("sans_anesth", 0);

// Sélection du praticien
$mediuser = CMediusers::get();
$listPrat = $mediuser->loadPraticiens(PERM_EDIT);

$selPrat = CValue::getOrSession("selPrat", $mediuser->isPraticien() ? $mediuser->user_id : null);

$selPraticien = new CMediusers();
$selPraticien->load($selPrat);

if ($selPraticien->isAnesth()) {
  // Selection des différentes interventions de la journée par service
  $count_ops = array(
    "ambu"       => 0,
    "comp"       => 0,
    "hors_plage" => 0);
  
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
             "service"     => "service.service_id = chambre.service_id"
           );
  
  $where_anesth = "operations.anesth_id = '$selPraticien->_id' OR plagesop.anesth_id = '$selPraticien->_id'";
  
  if ($sans_anesth) {
    $where_anesth .= " OR operations.anesth_id IS NULL OR plagesop.anesth_id IS NULL";
  }
  
  $whereAmbu = array(
                 "plagesop.date = '$date' OR operations.date = '$date'",
                 "sejour.type"   => "= 'ambu'",
                 "sejour.group_id" => "= '".CGroups::loadCurrent()->_id."'",
               );
  $whereAmbu[] = $where_anesth;
  
  if (!$canceled) {
    $whereAmbu["operations.annulee"] = " = '0'";
  }
  
  $whereHospi = array(
                 "plagesop.date = '$date' OR operations.date = '$date'",
                 "sejour.type"   => "= 'comp'",
                 "sejour.group_id" => "= '".CGroups::loadCurrent()->_id."'"
               );
  $whereHospi[] = $where_anesth;
  
  if (!$canceled) {
    $whereHospi["operations.annulee"] = " = '0'";
  }
  
  $whereUrg   = array(
                 "plagesop.plageop_id" => "IS NULL",
                 "operations.date"     => "= '$date'",
                 "sejour.group_id" => "= '".CGroups::loadCurrent()->_id."'",
               );
  $whereUrg[] = $where_anesth;
  
  if (!$canceled) {
    $whereUrg["operations.annulee"] = " = '0'";
  }
  
  foreach ($services as $_service) {
    $whereAmbu["service.service_id"]          = "= '$_service->_id'";
    $whereHospi["service.service_id"]         = "= '$_service->_id'";
    $whereUrg["service.service_id"]           = "= '$_service->_id'";
    
    $listInterv["ambu"][$_service->_id]       = $interv->loadList($whereAmbu , $order, null, null, $ljoin);
    $count_ops["ambu"] += count($listInterv["ambu"][$_service->_id]);
    
    $listInterv["comp"][$_service->_id]      = $interv->loadList($whereHospi, $order, null, null, $ljoin);
    $count_ops["comp"] += count($listInterv["comp"][$_service->_id]);
    
    $listInterv["hors_plage"][$_service->_id] = $interv->loadList($whereUrg  , $order, null, null, $ljoin);
    $count_ops["hors_plage"] += count($listInterv["hors_plage"][$_service->_id]);
  }
  
  $whereAmbu["service.service_id"]       = "IS NULL";
  $whereHospi["service.service_id"]      = "IS NULL";
  $whereUrg["service.service_id"]        = "IS NULL";
  
  $listInterv["ambu"]["non_place"]       = $interv->loadList($whereAmbu , $order, null, null, $ljoin);
  $count_ops["ambu"] += count($listInterv["ambu"]["non_place"]);
  
  $listInterv["comp"]["non_place"]      = $interv->loadList($whereHospi, $order, null, null, $ljoin);
  $count_ops["comp"] += count($listInterv["comp"]["non_place"]);
  
  $listInterv["hors_plage"]["non_place"] = $interv->loadList($whereUrg  , $order, null, null, $ljoin);
  $count_ops["hors_plage"] += count($listInterv["hors_plage"]["non_place"]);
  
  // Complétion du chargement
  foreach ($listInterv as $_intervs_by_type) {
    foreach ($_intervs_by_type as $_intervs_by_service) {
      foreach ($_intervs_by_service as $_interv) {
        $_interv->loadRefAffectation();
        $_interv->loadRefsFwd(1);
        $_interv->loadRefsConsultAnesth();
        $_interv->_ref_chir->loadRefFunction();
        $patient = $_interv->_ref_sejour->_ref_patient;
        $patient->loadRefConstantesMedicales();
      } 
    }
  }

  // Création du template
  $smarty = new CSmartyDP();
  
  $smarty->assign("date"        , $date);
  $smarty->assign("listPrat"    , $listPrat);
  $smarty->assign("listInterv"  , $listInterv);
  $smarty->assign("services"    , $services);
  $smarty->assign("selPrat"     , $selPrat);
  $smarty->assign("canceled"    , $canceled);
  $smarty->assign("sans_anesth" , $sans_anesth);
  $smarty->assign("count_ops"   , $count_ops);
  
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
    foreach ($selPraticien->_back["secondary_functions"] as  $curr_sec_spec) {
      $curr_sec_spec->loadRefsFwd();
      $curr_function = $curr_sec_spec->_ref_function;
      $secondary_specs[$curr_function->_id] = $curr_function;
    }
  }
  
  // Planning du mois
  $month_min = mbTransformTime("+ 0 month", $date, "%Y-%m-00");
  $month_max = mbTransformTime("+ 1 month", $date, "%Y-%m-00");
  
  $sql = "SELECT plagesop.*, plagesop.date AS opdate,
        SEC_TO_TIME(SUM(TIME_TO_SEC(operations.temp_operation))) AS duree,
        COUNT(operations.operation_id) AS total,
        functions_mediboard.text AS nom_function, functions_mediboard.color as color_function
      FROM plagesop
      LEFT JOIN operations
        ON plagesop.plageop_id = operations.plageop_id
          AND operations.annulee = '0'
          AND operations.chir_id = '$selPratLogin'
      LEFT JOIN functions_mediboard
        ON functions_mediboard.function_id = plagesop.spec_id
      WHERE (plagesop.chir_id = '$selPratLogin' OR plagesop.spec_id = '$specialite' OR plagesop.spec_id ".CSQLDataSource::prepareIn(array_keys($secondary_specs)).")
        AND plagesop.date BETWEEN '$month_min' AND '$month_max'
      GROUP BY plagesop.plageop_id
      ORDER BY plagesop.date, plagesop.debut, plagesop.plageop_id";
  $listPlages = array();
  if ($selPratLogin) {
    $listPlages = $ds->loadList($sql);
  }
  
  // Urgences du mois
  $sql = "SELECT operations.*, operations.date AS opdate,
        SEC_TO_TIME(SUM(TIME_TO_SEC(operations.temp_operation))) AS duree,
        COUNT(operations.operation_id) AS total
      FROM operations
      WHERE operations.annulee = '0'
        AND operations.chir_id = '$selPratLogin'
        AND operations.date BETWEEN '$month_min' AND '$month_max'
      GROUP BY operations.date
      ORDER BY operations.date";
  $listUrgences = array();
  if($selPratLogin) {
    $listUrgences = $ds->loadList($sql);
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
  
  $smarty->assign("date"        , $date);
  $smarty->assign("canceled"    , $canceled);
  $smarty->assign("lastmonth"   , $lastmonth);
  $smarty->assign("nextmonth"   , $nextmonth);
  $smarty->assign("listPrat"    , $listPrat);
  $smarty->assign("selPrat"     , $selPrat);
  $smarty->assign("listDays"    , $listDays);
  
  $smarty->display("vw_idx_planning.tpl");
}

?>
<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();
$ds = CSQLDataSource::get("std");

$date           = CValue::getOrSession("date", CMbDT::date());
$canceled       = CValue::getOrSession("canceled", 0);
$refresh        = CValue::get('refresh', 0);

$nextmonth = CMbDT::date("first day of next month"   , $date);
$lastmonth = CMbDT::date("first day of previous month", $date);

$sans_anesth    = CValue::getOrSession("sans_anesth", 0);

// Sélection du praticien
$mediuser = CMediusers::get();
$listPrat = $mediuser->loadPraticiens(PERM_EDIT);
foreach ($listPrat as $_prat) {
  $_prat->loadRefFunction();
}

$selPrat = CValue::getOrSession("selPrat", $mediuser->isPraticien() ? $mediuser->user_id : null);

$selPraticien = new CMediusers();
$selPraticien->load($selPrat);
$group = CGroups::loadCurrent();

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
    "affectation" => "affectation.sejour_id = sejour.sejour_id
      AND '$date' BETWEEN DATE(affectation.entree)
      AND DATE(affectation.sortie)",
    "lit"         => "lit.lit_id = affectation.lit_id",
    "chambre"     => "chambre.chambre_id = lit.chambre_id",
    "service"     => "service.service_id = chambre.service_id"
  );
  
  $where_anesth = "operations.anesth_id = '$selPraticien->_id' OR plagesop.anesth_id = '$selPraticien->_id'";
  
  if ($sans_anesth) {
    $where_anesth .= " OR operations.anesth_id IS NULL OR plagesop.anesth_id IS NULL";
  }
  
  $whereAmbu = array(
    "operations.date" => "= '$date'",
    "sejour.type"     => "= 'ambu'",
    "sejour.group_id" => "= '$group->_id'",
  );
  $whereAmbu[] = $where_anesth;
  
  if (!$canceled) {
    $whereAmbu["operations.annulee"] = " = '0'";
  }
  
  $whereHospi = array(
    "operations.date" => "= '$date'",
    "sejour.type"     => "= 'comp'",
    "sejour.group_id" => "= '$group->_id'",
  );
  $whereHospi[] = $where_anesth;
  
  if (!$canceled) {
    $whereHospi["operations.annulee"] = " = '0'";
  }
  
  $whereUrg   = array(
    "plagesop.plageop_id" => "IS NULL",
    "operations.date"     => "= '$date'",
    "sejour.group_id"     => "= '$group->_id'",
  );
  $whereUrg[] = $where_anesth;
  
  if (!$canceled) {
    $whereUrg["operations.annulee"] = " = '0'";
  }

  /** @var COperation[] $allInterv */
  $allInterv = array();
  
  foreach ($services as $_service) {
    $whereAmbu["service.service_id"]  = "= '$_service->_id'";
    $whereHospi["service.service_id"] = "= '$_service->_id'";
    $whereUrg["service.service_id"]   = "= '$_service->_id'";
    
    $listInterv["ambu"][$_service->_id] = $interv->loadList($whereAmbu , $order, null, null, $ljoin);
    $listInterv["comp"][$_service->_id] = $interv->loadList($whereHospi, $order, null, null, $ljoin);
    $listInterv["hors_plage"][$_service->_id] = $interv->loadList($whereUrg  , $order, null, null, $ljoin);

    $allInterv = array_merge($allInterv, $listInterv["ambu"][$_service->_id]);
    $allInterv = array_merge($allInterv, $listInterv["comp"][$_service->_id]);
    $allInterv = array_merge($allInterv, $listInterv["hors_plage"][$_service->_id]);

    $count_ops["ambu"] += count($listInterv["ambu"][$_service->_id]);
    $count_ops["comp"] += count($listInterv["comp"][$_service->_id]);
    $count_ops["hors_plage"] += count($listInterv["hors_plage"][$_service->_id]);

  }
  
  $whereAmbu["service.service_id"]       = "IS NULL";
  $whereHospi["service.service_id"]      = "IS NULL";
  $whereUrg["service.service_id"]        = "IS NULL";
  
  $listInterv["ambu"]["non_place"]       = $interv->loadList($whereAmbu , $order, null, null, $ljoin);
  $listInterv["comp"]["non_place"]      = $interv->loadList($whereHospi, $order, null, null, $ljoin);
  $listInterv["hors_plage"]["non_place"] = $interv->loadList($whereUrg  , $order, null, null, $ljoin);

  $allInterv = array_merge($allInterv, $listInterv["ambu"]["non_place"]);
  $allInterv = array_merge($allInterv, $listInterv["comp"]["non_place"]);
  $allInterv = array_merge($allInterv, $listInterv["hors_plage"]["non_place"]);

  $count_ops["ambu"] += count($listInterv["ambu"]["non_place"]);
  $count_ops["comp"] += count($listInterv["comp"]["non_place"]);
  $count_ops["hors_plage"] += count($listInterv["hors_plage"]["non_place"]);
  
  // Complétion du chargement
  $chirs     = CStoredObject::massLoadFwdRef($allInterv, "chir_id");
  $functions = CStoredObject::massLoadFwdRef($chirs, "function_id");
  $plages    = CStoredObject::massLoadFwdRef($allInterv, "plageop_id");
  $sejours   = CStoredObject::massLoadFwdRef($allInterv, "sejour_id");
  $patients  = CStoredObject::massLoadFwdRef($sejours, "patient_id");
  foreach ($allInterv as $_interv) {
    $_interv->loadRefAffectation();
    $_interv->loadRefChir()->loadRefFunction();
    $_interv->loadRefPatient()->loadRefLatestConstantes(null, array("poids", "taille"));
    $_interv->loadRefVisiteAnesth()->loadRefFunction();
    $_interv->loadRefsConsultAnesth()->loadRefConsultation()->loadRefPraticien()->loadRefFunction();
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
  
}

// Non anesthesiste
else {
  // Selection des plages du praticien et de celles de sa spécialité
  $praticien_id = null;
  $function_ids = null;
  if ($selPraticien->isPraticien()) {
    $praticien_id = $selPraticien->user_id;
    $function_ids = CMbArray::pluck($selPraticien->loadBackRefs("secondary_functions"), "function_id");
    $function_ids[] = $selPraticien->function_id;
  }

  // Planning du mois
  $month_min = CMbDT::format($date, "%Y-%m-01");
  $month_max = CMbDT::format($date, "%Y-%m-31");


  $sql = "SELECT plagesop.*, plagesop.date AS opdate,
        SEC_TO_TIME(SUM(TIME_TO_SEC(operations.temp_operation))) AS duree,
        COUNT(operations.operation_id) AS total,
        SUM(operations.rank_voulu > 0) AS planned_by_chir,
        COUNT(IF(operations.rank > 0, NULLIF(operations.rank, operations.rank_voulu), NULL)) AS order_validated,
        functions_mediboard.text AS nom_function, functions_mediboard.color as color_function
      FROM plagesop
      LEFT JOIN operations
        ON plagesop.plageop_id = operations.plageop_id
          AND operations.annulee = '0'
          AND operations.chir_id = '$praticien_id'
      LEFT JOIN functions_mediboard
        ON functions_mediboard.function_id = plagesop.spec_id
      WHERE (plagesop.chir_id = '$praticien_id' OR plagesop.spec_id ".CSQLDataSource::prepareIn($function_ids).")
        AND plagesop.date BETWEEN '$month_min' AND '$month_max'
      GROUP BY plagesop.plageop_id
      ORDER BY plagesop.date, plagesop.debut, plagesop.plageop_id";
  $listPlages = array();
  if ($praticien_id) {
    $listPlages = $ds->loadList($sql);
  }

  // Urgences du mois
  $sql = "SELECT operations.*, operations.date AS opdate,
        SEC_TO_TIME(SUM(TIME_TO_SEC(operations.temp_operation))) AS duree,
        COUNT(operations.operation_id) AS total
      FROM operations
      WHERE operations.annulee = '0'
        AND operations.chir_id = '$praticien_id'
        AND operations.plageop_id IS NULL
        AND operations.date BETWEEN '$month_min' AND '$month_max'
      GROUP BY operations.date
      ORDER BY operations.date";
  $listUrgences = array();
  if ($praticien_id) {
    $listUrgences = $ds->loadList($sql);
  }
  
  $listDays = array();
  foreach ($listPlages as $curr_ops) {
    $listDays[$curr_ops["opdate"]][$curr_ops["plageop_id"]] = $curr_ops;

  }
  foreach ($listUrgences as $curr_ops) {
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

  if (!$refresh) {
    $smarty->display("vw_idx_planning.tpl");
  } else {
    $smarty->display('inc_list_plagesop.tpl');
  }
}

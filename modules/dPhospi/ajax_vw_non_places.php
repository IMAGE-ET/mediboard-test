<?php /* $Id: ajax_vw_non_places.php $ */

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$services_ids    = CValue::getOrSession("services_ids");
$triAdm          = CValue::getOrSession("triAdm", "praticien");
$_type_admission = CValue::getOrSession("_type_admission", "ambucomp");
$filter_function = CValue::getOrSession("filter_function");
$date            = CValue::getOrSession("date");
$granularite     = CValue::getOrSession("granularite");
$readonly        = CValue::getOrSession("readonly", 0);
$duree_uscpo     = CValue::getOrSession("duree_uscpo", "0");
$isolement     = CValue::getOrSession("isolement", "0");
$prestation_id   = CValue::getOrSession("prestation_id", "");
$item_prestation_id = CValue::getOrSession("item_prestation_id");

if (is_array($services_ids)) {
  CMbArray::removeValue("", $services_ids);
}

$heureLimit = "16:00:00";
$group_id = CGroups::loadCurrent()->_id;
$where = array();
$where["annule"] = "= '0'";
$where["sejour.group_id"] = "= '$group_id'";
$where[] = "(sejour.type != 'seances' && affectation.affectation_id IS NULL) || sejour.type = 'seances'";
$where["sejour.service_id"] = "IS NULL " . (is_array($services_ids) && count($services_ids) ? "OR `sejour`.`service_id` " . CSQLDataSource::prepareIn($services_ids) : "");

$order = null;
switch ($triAdm) {
  case "date_entree":
    $order = "entree_prevue ASC";
    break;
  case "praticien":
    $order = "users_mediboard.function_id, sejour.entree_prevue, patients.nom, patients.prenom";
    break;
  case "patient" :
    $order = "patients.nom, patients.prenom";
    break;
}

switch ($_type_admission) {
  case "ambucomp":
    $where[] = "sejour.type = 'ambu' OR sejour.type = 'comp'";
    break;
  case "0":
    break;
  default:
    $where["sejour.type"] = "= '$_type_admission'"; 
}

$sejour = new CSejour;
$ljoin = array(
  "affectation"     => "sejour.sejour_id = affectation.sejour_id",
  "users_mediboard" => "sejour.praticien_id = users_mediboard.user_id",
  "patients"        => "sejour.patient_id = patients.patient_id"
);

$period = "";
$nb_unite = 0;

switch ($granularite) {
  case "day":
    $period = "1hour";
    $unite = "hour";
    $nb_unite = 1;
    $nb_ticks = 24;
    $date_min = mbDateTime($date);
    break;
  case "week":
    $period = "6hours";
    $unite = "hour";
    $nb_unite = 6;
    $nb_ticks = 28;
    $date_min = mbDateTime("-2 days", $date);
    break;
  case "4weeks":
    $period = "1day";
    $unite = "day";
    $nb_unite = 1;
    $nb_ticks = 28;
    $date_min = mbDateTime("-1 week", CMbDate::dirac("week", $date));
}

$offset = $nb_ticks * $nb_unite;
$date_max = mbDateTime("+ $offset $unite", $date_min);
$current = CMbDate::dirac("hour", mbDateTime());
$temp_datetime = mbDateTime(null, $date_min);

for ($i = 0 ; $i < $nb_ticks ; $i++) {
  $offset = $i * $nb_unite;
  
  $datetime = mbDateTime("+ $offset $unite", $date_min);
  $datetimes[] = $datetime;
  if ($granularite == "4weeks") {
    if (mbDate($current) == mbDate($temp_datetime) &&
      mbTime($current) >= mbTime($temp_datetime) && mbTime($current) > mbTime($datetime)) {
      $current = $temp_datetime;
    }
    $week_a = mbTransformTime($temp_datetime, null, "%V");
    $week_b = mbTransformTime($datetime, null, "%V");

    // les semaines
    $days[$datetime] = $week_b;
    
    // On stocke le changement de mois s'il advient
   if (mbTransformTime($datetime, null, "%m") != mbTransformTime($temp_datetime, null, "%m")) {
     
     // Entre deux semaines
     if ($i%7 == 0) {
       $change_month[$week_a] = array("right"=>$temp_datetime);
       $change_month[$week_b] = array("left"=>$datetime);
     }
     // Dans la même semaine
     else {
       $change_month[$week_b] = array("left" => $temp_datetime, "right" => $datetime);
     }
   }
  }
  else {
    if ($granularite == "week" && mbDate($current) == mbDate($temp_datetime) &&
        mbTime($datetime) >= mbTime($temp_datetime) && mbTime($current) <= mbTime($datetime)) {
      $current = $temp_datetime;
    }
    // le datetime, pour avoir soit le jour soit l'heure
    $days[] = mbDate($datetime);
  }
  $temp_datetime = $datetime;
}

$days = array_unique($days);

// Cas de la semaine 00
if ($granularite == "4weeks" && count($days) == 5) {
  array_pop($days);
}

$where["sejour.entree"] = "< '$date_max'";
$where["sejour.sortie"] = "> '$date_min'";

if ($duree_uscpo) {
  $ljoin["operations"] = "operations.sejour_id = sejour.sejour_id";
  $where["duree_uscpo"] = "> 0";
}

if ($isolement) {
  $where["isolement"] = "= '1'";
}

if ($item_prestation_id && $prestation_id) {
  $ljoin["item_liaison"] = "sejour.sejour_id = item_liaison.sejour_id";
  $where["item_liaison.item_souhait_id"] = " = '$item_prestation_id'";
}

$sejours = $sejour->loadList($where, $order, null, null, $ljoin);

$praticiens = CMbObject::massLoadFwdRef($sejours, "praticien_id");
CMbObject::massLoadFwdRef($sejours, "prestation_id");
CMbObject::massLoadFwdRef($sejours, "patient_id");
CMbObject::massLoadFwdRef($praticiens, "function_id");
$services = CMbObject::massLoadFwdRef($sejours, "service_id");

$sejours_non_affectes = array();
$functions_filter = array();
$operations = array();

foreach ($sejours as $_key => $_sejour) {
  $_sejour->loadRefPrestation();
  $_sejour->loadRefPraticien()->loadRefFunction();
  $functions_filter[$_sejour->_ref_praticien->function_id] = $_sejour->_ref_praticien->_ref_function;
  if ($filter_function && $filter_function != $_sejour->_ref_praticien->function_id) {
    unset($sejours[$_key]);
    continue;
  }
  else {
    $_sejour->_entree_offset = CMbDate::position(max($date_min, $_sejour->entree), $date_min, $period);
    $_sejour->_sortie_offset = CMbDate::position(min($date_max, $_sejour->sortie), $date_min, $period);
    $_sejour->_width = $_sejour->_sortie_offset - $_sejour->_entree_offset;
    $patient = $_sejour->loadRefPatient();
    $patient->loadRefPhotoIdentite();
    $patient->loadRefDossierMedical(false);
    $constantes = $patient->getFirstConstantes();
    $patient->_overweight = $constantes->poids > 120;
  }
  
  if (isset($operations[$_sejour->_id])) {
    $_operations = $operations[$_sejour->_id];
  }
  else {
    $operations[$_sejour->_id] = $_operations = $_sejour->loadRefsOperations();
  }
  
  foreach ($_operations as $key=>$_operation) {
    $_operation->loadRefPlageOp(1);
    
    $hour_operation = mbTransformTime(null, $_operation->temp_operation, "%H");
    $min_operation = mbTransformTime(null, $_operation->temp_operation, "%M");
    
    $_operation->_debut_offset = CMbDate::position($_operation->_datetime, max($date_min, $_sejour->entree), $period);
    $_operation->_fin_offset = CMbDate::position(mbDateTime("+$hour_operation hours +$min_operation minutes",$_operation->_datetime), max($date_min, $_sejour->entree), $period);
    $_operation->_width = $_operation->_fin_offset - $_operation->_debut_offset;
    
    if (($_operation->_datetime > $date_max)) {
      $_operation->_width_uscpo = 0;
    }
    else {
      $fin_uscpo = $hour_operation + 24 * $_operation->duree_uscpo;
      $_operation->_width_uscpo = CMbDate::position(mbDateTime("+$fin_uscpo hours +$min_operation minutes", $_operation->_datetime), max($date_min, $_sejour->entree), $period) - $_operation->_fin_offset;
    }
  }
  
  if ($prestation_id) {
    $_sejour->loadRefFirstLiaisonForPrestation($prestation_id);

    $item_liaison = new CItemLiaison();
    $where = array();
    $ljoin = array();
    
    $where["sejour_id"] = "= '$_sejour->_id'";
    $ljoin["item_prestation"] =
      "item_prestation.item_prestation_id = item_liaison.item_realise_id OR
       item_prestation.item_prestation_id = item_liaison.item_souhait_id";
    
    $where["object_class"] = " = 'CPrestationJournaliere'";
    $where["object_id"] = " = '$prestation_id'";
    $_sejour->_liaisons_for_prestation = $item_liaison->loadList($where, "date ASC", null, null, $ljoin);

    foreach ($_sejour->_liaisons_for_prestation as $_liaison) {
      $_liaison->loadRefItem();
      $_liaison->loadRefItemRealise();
    }
  }
  
  if ($_sejour->service_id) {
    @$sejours_non_affectes[$_sejour->service_id][] = $_sejour;
  }
  else {
    @$sejours_non_affectes["np"][] = $_sejour;
  }
}

$dossiers = CMbArray::pluck($sejours, "_ref_patient", "_ref_dossier_medical");
CDossierMedical::massCountAntecedentsByType($dossiers, "deficience");

$items_prestation = array();

if ($prestation_id) {
  $prestation = new CPrestationJournaliere;
  $prestation->load($prestation_id);
  $items_prestation = $prestation->loadBackRefs("items", "rank asc");
}

// Chargement des affectations dans les couloirs (sans lit_id)
$where = array();
$ljoin = array();
$where["lit_id"] = "IS NULL";
if (is_array($services_ids) && count($services_ids)) {
  $where["affectation.service_id"] = CSQLDataSource::prepareIn($services_ids);
}
$where["affectation.entree"] = "<= '$date_max'";
$where["affectation.sortie"] = ">= '$date_min'";

if ($duree_uscpo) {
  $ljoin["operations"] = "operations.sejour_id = affectation.sejour_id";
  $where["duree_uscpo"] = "> 0";
}

if ($isolement) {
  $ljoin["sejour"] = "sejour.sejour_id = affectation.sejour_id";
  $where["isolement"] = "= '1'";
}

$affectation = new CAffectation();

$affectations = $affectation->loadList($where, "entree ASC", null, null, $ljoin);
$sejours  = CMbObject::massLoadFwdRef($affectations, "sejour_id");
$services = $services + CMbObject::massLoadFwdRef($affectations, "service_id");
$patients = CMbObject::massLoadFwdRef($sejours, "patient_id");
$praticiens = CMbObject::massLoadFwdRef($sejours, "praticien_id");
CMbObject::massLoadFwdRef($praticiens, "function_id");

$operations = array();
$suivi_affectation = false;

foreach ($affectations as $_affectation) {
  $lit = new CLit;
  $lit->_selected_item = new CItemPrestation;
  $lit->_affectation_id = $_affectation->_id;
  
  if (!$suivi_affectation && $_affectation->parent_affectation_id) {
    $suivi_affectation = true;
  }
  $_affectation->loadRefsAffectations();
  $sejour = $_affectation->loadRefSejour();
  $sejour->loadRefPraticien()->loadRefFunction();
  $patient = $sejour->loadRefPatient();
  $patient->loadRefPhotoIdentite();
  $patient->loadRefDossierMedical(false);

  $_affectation->_entree_offset = CMbDate::position(max($date_min, $_affectation->entree), $date_min, $period);
  $_affectation->_sortie_offset = CMbDate::position(min($date_max, $_affectation->sortie), $date_min, $period);
  $_affectation->_width = $_affectation->_sortie_offset - $_affectation->_entree_offset;
  
  if (isset($operations[$sejour->_id])) {
    $_operations = $operations[$sejour->_id];
  }
  else {
    $operations[$sejour->_id] = $_operations = $sejour->loadRefsOperations();
  }

  if ($prestation_id) {
    $item_liaison = new CItemLiaison();
    $where = array();
    $ljoin = array();

    $where["sejour_id"] = "= '$sejour->_id'";
    $ljoin["item_prestation"] =
      "item_prestation.item_prestation_id = item_liaison.item_realise_id OR
       item_prestation.item_prestation_id = item_liaison.item_souhait_id";

    $where["object_class"] = " = 'CPrestationJournaliere'";
    $where["object_id"] = " = '$prestation_id'";
    $sejour->_liaisons_for_prestation = $item_liaison->loadList($where, "date ASC", null, null, $ljoin);

    foreach ($sejour->_liaisons_for_prestation as $_liaison) {
      $_liaison->loadRefItem();
      $_liaison->loadRefItemRealise();
    }
  }


  foreach ($_operations as $key=>$_operation) {
    $_operation->loadRefPlageOp(1);
    
    $hour_operation = mbTransformTime(null, $_operation->temp_operation, "%H");
    $min_operation = mbTransformTime(null, $_operation->temp_operation, "%M");
    
    $_operation->_debut_offset[$_affectation->_id] = CMbDate::position($_operation->_datetime, max($date_min, $_affectation->entree), $period);
    
    $_operation->_fin_offset[$_affectation->_id] = CMbDate::position(mbDateTime("+$hour_operation hours +$min_operation minutes",$_operation->_datetime), max($date_min, $_affectation->entree), $period);
    $_operation->_width[$_affectation->_id] = $_operation->_fin_offset[$_affectation->_id] - $_operation->_debut_offset[$_affectation->_id];
    
    if (($_operation->_datetime > $date_max)) {
      $_operation->_width_uscpo[$_affectation->_id] = 0;
    }
    else {
      $fin_uscpo = $hour_operation + 24 * $_operation->duree_uscpo;
      $_operation->_width_uscpo[$_affectation->_id] = CMbDate::position(mbDateTime("+$fin_uscpo hours + $min_operation minutes", $_operation->_datetime), max($date_min, $_affectation->entree), $period) - $_operation->_fin_offset[$_affectation->_id];
    }
  }
  
  $lit->_lines = array();
  $lit->_lines[] = $_affectation->_id;
  
  @$sejours_non_affectes[$_affectation->service_id][] = $lit;
}

$dossiers = CMbArray::pluck($affectations, "_ref_sejour", "_ref_patient", "_ref_dossier_medical");
CDossierMedical::massCountAntecedentsByType($dossiers, "deficience");

ksort($sejours_non_affectes, SORT_STRING);

$sejour = new CSejour;
$sejour->_type_admission = $_type_admission;

$smarty = new CSmartyDP;

$smarty->assign("sejours_non_affectes", $sejours_non_affectes);
$smarty->assign("sejour", $sejour);
$smarty->assign("triAdm", $triAdm);
$smarty->assign("functions_filter", $functions_filter);
$smarty->assign("filter_function", $filter_function);
$smarty->assign("granularite", $granularite);
$smarty->assign("date"     , $date);
$smarty->assign("date_min", $date_min);
$smarty->assign("date_max", $date_max);
$smarty->assign("nb_ticks", $nb_ticks);
$smarty->assign("days"    , $days);
$smarty->assign("datetimes", $datetimes);
$smarty->assign("readonly", $readonly);
$smarty->assign("duree_uscpo", $duree_uscpo);
$smarty->assign("isolement", $isolement);
$smarty->assign("current", $current);
$smarty->assign("items_prestation", $items_prestation);
$smarty->assign("item_prestation_id", $item_prestation_id);
$smarty->assign("prestation_id", $prestation_id);
$smarty->assign("td_width", 84.2 / $nb_ticks);
$smarty->assign("mode_vue_tempo", "classique");
$smarty->assign("affectations", $affectations);
$smarty->assign("services", $services);

$smarty->display("inc_vw_non_places.tpl");

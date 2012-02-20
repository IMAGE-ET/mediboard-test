<?php

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision:
* @author Romain Ollivier
*/

CCanDo::checkRead();

// Création du template
$smarty = new CSmartyDP();

// Current user
$mediuser = new CMediusers;
$mediuser->load(CAppUI::$instance->user_id);

// Current function
$mediuser->loadRefFunction();
$function = $mediuser->_ref_function;

// Filter
$filter = new CPlageconsult();
$filter->_function_id       = CValue::get("_function_id", $function->type == "cabinet" ? $function->_id : null );
$filter->_user_id           = CValue::get("_user_id", null);
$filter->_other_function_id = CValue::get("_other_function_id");
$filter->_date_min          = CValue::get("_date_min", mbDate("last month"));
$filter->_date_max          = CValue::get("_date_max", mbDate());
$smarty->assign("filter"   , $filter);

$functions = CMediusers::loadFonctions(PERM_EDIT, null, "cabinet");
$smarty->assign("functions", $functions);

$ds = $filter->_spec->ds;

if ($filter->_function_id) {
  // Consultations
  $query = "CREATE TEMPORARY TABLE consultation_prime AS 
    SELECT 
      consultation_id, 
      chir_id AS praticien_id, 
      patient_id, 
      plageconsult.date AS consult_date
    FROM consultation
    LEFT JOIN plageconsult ON plageconsult.plageconsult_id = consultation.plageconsult_id
    LEFT JOIN users_mediboard ON users_mediboard.user_id = plageconsult.chir_id
    LEFT JOIN users ON users.user_id = users_mediboard.user_id
    WHERE users_mediboard.function_id = '$filter->_function_id'
    AND plageconsult.date BETWEEN '$filter->_date_min' AND '$filter->_date_max'
    AND annule = '0'
    AND patient_id IS  NOT NULL;";
  $ds->exec($query);
  
  // Consultations counts
  $query = "SELECT praticien_id, COUNT(*)
    FROM consultation_prime
    GROUP BY praticien_id;";
  $consultations_counts = $ds->loadHashList($query);
  
  // Patients
  $query = "CREATE TEMPORARY TABLE consultation_patient
    SELECT praticien_id, patient_id
    FROM consultation_prime
    GROUP BY patient_id, praticien_id;";
  $ds->exec($query);
  
  // Patients counts
  $query = "SELECT praticien_id, COUNT(*)
    FROM consultation_patient
    GROUP BY praticien_id;";
  $patients_counts = $ds->loadHashList($query);

  // Sejours
  $query = "CREATE TEMPORARY TABLE consultation_sejour
    SELECT 
      consultation_prime.praticien_id, 
      consultation_prime.patient_id,
      consultation_id, 
      consult_date, 
      sejour.sejour_id, 
      sejour.entree, 
      sejour.sortie
    FROM consultation_prime
    LEFT JOIN sejour ON sejour.patient_id = consultation_prime.patient_id 
      AND sejour.praticien_id = consultation_prime.praticien_id 
      AND sejour.entree BETWEEN '$filter->_date_min' AND '$filter->_date_max'
      AND sejour.annule = '0'
    WHERE sejour.sejour_id IS NOT NULL
    GROUP BY consultation_id;";
  $ds->exec($query);
  
  // Sejours counts
  $query = "SELECT praticien_id, COUNT(*)
    FROM consultation_sejour
    GROUP BY praticien_id;";
  $sejours_counts = $ds->loadHashList($query);

  if ($filter->_other_function_id) {
    // Other (consultations)
    $query = "CREATE TEMPORARY TABLE consultation_other
      SELECT 
        consultation_prime.praticien_id, 
        consultation_prime.patient_id,
        consultation_prime.consultation_id AS consult1_id, 
        consult_date AS consult1_date, 
        consultation.consultation_id AS consult2_id, 
        plageconsult.date AS consult2_date
      FROM consultation_prime
      LEFT JOIN consultation ON consultation.patient_id = consultation_prime.patient_id 
      LEFT JOIN plageconsult ON plageconsult.plageconsult_id = consultation.plageconsult_id
      LEFT JOIN users_mediboard ON users_mediboard.user_id = plageconsult.chir_id
      WHERE users_mediboard.function_id = '$filter->_other_function_id'
      AND plageconsult.date BETWEEN '$filter->_date_min' AND '$filter->_date_max'
      GROUP BY consult1_id;";
    $ds->exec($query);
    
    // Other (consultations) counts
    $query = "SELECT praticien_id, COUNT(*)
      FROM consultation_other
      GROUP BY praticien_id;";
    $others_counts = $ds->loadHashList($query);
  }
}

if ($filter->_user_id) {
  $query = "CREATE TEMPORARY TABLE consultation_creation AS 
    SELECT user_log.user_id as user_id, plageconsult.chir_id as chir_id
    FROM user_log
    LEFT JOIN consultation ON consultation.consultation_id = user_log.object_id
    LEFT JOIN plageconsult ON plageconsult.plageconsult_id = consultation.plageconsult_id
    WHERE user_log.object_class = 'CConsultation'
    AND user_log.date BETWEEN '$filter->_date_min 00:00:00' AND '$filter->_date_max 23:59:59'
    AND user_log.type = 'create'";
  $ds->exec($query);
  
  $query2 = "SELECT consultation_creation.user_id, count(consultation_creation.user_id) AS total
    FROM consultation_creation
    WHERE consultation_creation.chir_id = '$filter->_user_id'
    GROUP BY consultation_creation.user_id
    ORDER BY total DESC";
    
  $stats_creation = $ds->loadList($query2);
  $smarty->assign("stats_creation", $stats_creation);
  
  $where = array();
  $where["user_id"] = CSQLDataSource::prepareIn(CMbArray::pluck($stats_creation, "user_id"));
  $prats_creation = $mediuser->loadList($where);
  CMbObject::massLoadFwdRef($prats_creation, "function_id");
  foreach ($prats_creation as $_prat) {
    $_prat->loadRefFunction();
  }
  $smarty->assign("prats_creation", $prats_creation);
}

$users = $mediuser->loadPraticiens();
$smarty->assign("users", $users);

// Praticiens
$praticiens = $mediuser->loadPraticiens(PERM_READ, $filter->_function_id);
$smarty->assign("praticiens", $praticiens);

// Stats by praticiens
$stats = array();
foreach ($praticiens as $_praticien) {
  // Counts
  $counts = array (
    "consultations" => @$consultations_counts[$_praticien->_id],
    "sejours"       => @$sejours_counts      [$_praticien->_id],
    "patients"      => @$patients_counts     [$_praticien->_id],
    "others"        => @$others_counts       [$_praticien->_id],
  );
  
  // Percents
  $percents = array (
    "consultations" => $counts["consultations"] ? 1 : null,
    "sejours"       => $counts["consultations"] ? $counts["sejours"]  / $counts["consultations"] : null,
    "patients"      => $counts["consultations"] ? $counts["patients"] / $counts["consultations"] : null,
    "others"        => $counts["consultations"] ? $counts["others"]   / $counts["consultations"] : null,
  );
  
  $stats[$_praticien->_id] = array(
    "counts"   => $counts,
    "percents" => $percents,
  );
}
$smarty->assign("stats", $stats);

// Template rendering
$smarty->display("vw_stats.tpl");
?>
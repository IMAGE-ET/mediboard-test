<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPdeveloppement
 * @version $Revision: $
 * @author Alexis Granger
 */

$dossier_medical = new CDossierMedical();
$ljoin["patients"] = "patients.patient_id = dossier_medical.object_id";
$where = array();
$where["patients.patient_id"] = " IS NULL";
$where["dossier_medical.object_class"] = " = 'CPatient'";
$dossiers = $dossier_medical->loadList($where, null, null, null, $ljoin);

$nb_patient_ok = 0;

// Nombre total de dossiers zombies
$nb_zombies = count($dossiers);
foreach($dossiers as &$_dossier){
  // Initialisation
  $log_ant = new CUserLog();
  $log_trait = new CUserLog();
  $logs = array();
  
  $_dossier->countBackRefs("antecedents");
  $_dossier->countBackRefs("traitements");
  
  if($_dossier->_count["antecedents"] == 0 && $_dossier->_count["traitements"] == 0 && $_dossier->codes_cim == ''){
    unset($dossiers[$_dossier->_id]);
    continue;
  }

  // Chargement du log de suppression du patient
  $log_delete_patient = new CUserLog();
  $log_delete_patient->type = "delete";
  $log_delete_patient->object_id = $_dossier->object_id;
  $log_delete_patient->object_class = $_dossier->object_class;
  $log_delete_patient->loadMatchingObject();
  
  $min_datetime = mbDateTime("- 5 seconds", $log_delete_patient->date);
  $max_datetime = $log_delete_patient->date;

  // Chargement du log de creation de patient
  $logs = array();
  $log = new CUserLog();
  $where = array();
  $where["object_class"] = " = 'CPatient'";
  $where["user_id"] = " = '$log_delete_patient->user_id'";
  $where["date"] = "BETWEEN '$min_datetime' AND '$max_datetime'";
  $where[] = "type = 'create' OR type = 'merge'";
  $log->loadObject($where);
  
  $test[$_dossier->_id] = $log->object_id;
  if($log->object_id){
    $nb_patient_ok++;
  }
}

$smarty = new CSmartyDP();
$smarty->assign("nb_patient_ok", $nb_patient_ok);
$smarty->assign("dossiers", $dossiers);
$smarty->assign("nb_zombies", $nb_zombies);
$smarty->assign("test", $test);
$smarty->display("check_dossiers_medicaux.tpl");

?>
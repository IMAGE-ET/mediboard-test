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
  //$patients_merge[$_dossier->_id] = '';
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
 
  
  // 2eme methode
  // Chargement du log de suppression du patient
  $log_delete_patient = new CUserLog();
  $log_delete_patient->type = "delete";
  $log_delete_patient->object_id = $_dossier->object_id;
  $log_delete_patient->object_class = $_dossier->object_class;
  $log_delete_patient->loadMatchingObject();
  
  $min_datetime = mbDateTime("- 5 seconds", $log_delete_patient->date);
  $max_datetime = $log_delete_patient->date;

  // Chargement des logs +/- 3 secondes par le meme user_id sur un patient
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
  
 
  // 1er methode
  $antecedents = $_dossier->loadBackRefs("antecedents", "antecedent_id ASC");
  $traitements = $_dossier->loadBackRefs("traitements", "traitement_id ASC");
  
  $antecedent = reset($antecedents);
  $traitement = reset($traitements);
  
  // Chargement du log du 1er antecedent du dossier medical
  if($antecedent){
	  $log_ant = new CUserLog();
	  $log_ant->type = 'create';
	  $log_ant->object_id = $antecedent->_id;
	  $log_ant->object_class = 'CAntecedent';
	  $log_ant->loadMatchingObject();
	  if($log_ant->_id){
	    $logs[$log_ant->date] = $log_ant;
	  }
  }
  
  // Chargement du log du 1er traitement du dossier medical
  if($traitement){
	  $log_trait = new CUserLog();
	  $log_trait->type = 'create';
	  $log_trait->object_id = $traitement->_id;
	  $log_trait->object_class = 'CTraitement';
	  $log_trait->loadMatchingObject();
  	if($log_trait->_id){
	    $logs[$log_trait->date] = $log_trait;
	  }
  }
  
  // Chargement du log de creation du dossier medical (creation de cim)
  $log_dm = new CUserLog();
  $log_dm->type = 'create';
  $log_dm->object_id = $_dossier->_id;
  $log_dm->object_class = 'CDossierMedical';
  $log_dm->loadMatchingObject();
  if($log_dm->_id){
    $logs[$log_dm->date] = $log_dm;
  }
  
  ksort($logs);
  $first_log = reset($logs);
  if(!$first_log){
    $first_log = new CUserLog();
  }
  
  $min_datetime = mbDateTime("- 10 minutes", $first_log->date);
  $max_datetime = mbDateTime("+ 30 minutes", $first_log->date);
  
  // Recherche des consultations
  $log_consult = new CUserLog();
  $where = array();
  $where["user_id"] = " = '$first_log->user_id'";
  $where["object_class"] = " = 'CConsultation'";
  $where["type"] = " = 'store'";
  //$where["fields"] = " LIKE '%valide%'";
  $where["date"] = "BETWEEN '$min_datetime' AND '$max_datetime'";
  $logs_consult = $log_consult->loadList($where);
  
  // Si une consultation est trouvée
  if(count($logs_consult)){
    foreach($logs_consult as $_log_consult){
	    $consult_id = $_log_consult->object_id;
	    $consultation = new CConsultation();
	    $consultation->load($consult_id);
	    
	    // Stockages des consultations
	    if(@!in_array($consult_id, $consultations[$_dossier->_id])){
	      $consultations[$_dossier->_id][$consultation->_id] = $consultation; 
	    }
	    
	    $patient_id = $consultation->patient_id;
	    // Recherche d'un log de fusion sur le patient
	    $log_patient = new CUserLog();
	    $where = array();
	    $where["object_id"] = " = '$patient_id'";
	    $where["object_class"] = " = 'CPatient'";
	    $where["type"] = " = 'merge'";
	    $log_patient->loadObject($where);
	    if($log_patient->_id){
	      if(@!in_array($patient_id, $patients[$_dossier->_id])){
	        $patients[$_dossier->_id][] = $patient_id;
	      }
	    }
	  }
  }

  /*
  if(count(@$patients[$_dossier->_id]) == 1){
    $nb_patient_ok++; 
  }
*/
}

$smarty = new CSmartyDP();
$smarty->assign("nb_patient_ok", $nb_patient_ok);
$smarty->assign("dossiers", $dossiers);
$smarty->assign("nb_zombies", $nb_zombies);
$smarty->assign("patients", $patients);
$smarty->assign("consultations", $consultations);
$smarty->assign("test", $test);
$smarty->display("check_dossiers_medicaux.tpl");

?>
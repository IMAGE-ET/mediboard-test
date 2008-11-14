<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

// Recuperation des variables
$prescription_id = mbGetValueFromGet("prescription_id");
$date            = mbDate();

$dates = array($date, mbDate("+ 1 DAY", $date), mbDate("+ 2 DAY", $date));
$logs = array();
$last_log = new CUserLog();

// Chargement de la prescription
$prescription = new CPrescription();
$prescription->load($prescription_id);

// Chargement du patient
$prescription->loadRefPatient();
$patient =& $prescription->_ref_patient;
$patient->loadIPP();
$patient->loadRefConstantesMedicales();
$poids = $patient->_ref_constantes_medicales->poids;

// Chargement du séjour
$prescription->loadRefObject();
$sejour =& $prescription->_ref_object;
$sejour->loadNumDossier();
$sejour->loadCurrentAffectation(mbDateTime());

// Chargement des lignes
$prescription->loadRefsLinesMed("1","1","service");
$prescription->loadRefsLinesElementByCat("1","","service");
$prescription->_ref_object->loadRefPrescriptionTraitement();	  
$prescription->_ref_object->_ref_prescription_traitement->loadRefsLinesMed("1","1","service");

$pharmacien = new CMediusers();

// Parcours et affichage des medicaments
foreach($prescription->_ref_prescription_lines as $_line){
	$_line->loadRefLogValidationPharma();
	$logs[$_line->_ref_log_validation_pharma->date] = $_line->_ref_log_validation_pharma;
}

// Chargement des lignes de perfusions
$prescription->loadRefsPerfusions();
foreach($prescription->_ref_perfusions as $_perfusion){
  $_perfusion->loadRefsLines();  
  $_perfusion->loadRefPraticien();
}

// Chargement du dernier pharmacien qui a validé une ligne
if($logs){
  ksort($logs);
  $last_log = end($logs);
  $pharmacien->load($last_log->user_id);
}

$hours = explode("|",CAppUI::conf("dPprescription CPrisePosologie heures_prise"));
sort($hours); 
 
/* Calcul permettant de regrouper toutes les heures dans un tableau afin d'afficher les medicaments
   dont les heures ne sont pas spécifié dans le tableau */
$heures = array();
$list_hours = range(0,23);
$last_hour_in_array = reset($hours);
krsort($list_hours); 
foreach($list_hours as &$hour){
  $hour = str_pad($hour, 2, "0", STR_PAD_LEFT);
  if(in_array($hour, $hours)){
    $last_hour_in_array = $hour;
  }
  if($last_hour_in_array >= $hour){
    $heures[$hour] = $last_hour_in_array;
  } else {
    $heures[$hour] = end($hours);
  }
}
ksort($heures);

foreach($dates as $_date){
  $prescription->calculPlanSoin($_date, 1, $heures);
  foreach($hours as $_hour){
  	$tabHours[$_date]["$_date $_hour:00:00"] = $_hour;
  }
}

// Chargement des categories
$categories = CCategoryPrescription::loadCategoriesByChap();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("prescription", $prescription);
$smarty->assign("last_log", $last_log);
$smarty->assign("pharmacien", $pharmacien);
$smarty->assign("tabHours", $tabHours);
$smarty->assign("prescription_id", $prescription_id);
$smarty->assign("dates", $dates);
$smarty->assign("categories", $categories);
$smarty->assign("patient", $patient);
$smarty->assign("sejour", $sejour);
$smarty->assign("poids", $poids);
$smarty->assign("date", $date);
$smarty->display("vw_plan_soin.tpl");

?>
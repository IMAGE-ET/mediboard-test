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

// Initialisations
foreach($dates as $_date){
	// Initialisations
	$prises_med[$_date] = array();
	$lines_med[$_date] = array();
	$list_prises_med[$_date] = array();
	$prises_element[$_date] = array();
	$lines_element[$_date] = array();
	$list_prises_element[$_date] = array();
	$nb_produit_by_cat[$_date] = array();
}
$all_lines_med = array();
$all_lines_element = array();
$intitule_prise_med = array();
$intitule_prise_element = array();
$last_log = new CUserLog();

// Chargement de la prescription
$prescription = new CPrescription();
$prescription->load($prescription_id);
$prescription->loadRefsLines();

$pharmacien = new CMediusers();

// Parcours et affichage des medicaments
foreach($prescription->_ref_prescription_lines as $_line){
	$_line->loadRefLogValidationPharma();
	$logs[$_line->_ref_log_validation_pharma->date] = $_line->_ref_log_validation_pharma;
}

// Chargement du dernier pharmacien qui a validé une ligne
if($logs){
  ksort($logs);
  $last_log = end($logs);
  $pharmacien->load($last_log->user_id);
}

// Chargement du patient
$prescription->loadRefPatient();
$patient =& $prescription->_ref_patient;
$patient->loadIPP();
$patient->loadRefConstantesMedicales();
$const_med = $patient->_ref_constantes_medicales;
$poids = $const_med->poids;

// Chargement du séjour
$prescription->loadRefObject();
$sejour =& $prescription->_ref_object;
$sejour->loadNumDossier();
$sejour->loadCurrentAffectation(mbDateTime());

// Calcul du plan de soin pour les 3 jours
foreach($dates as $_date){
  $prescription->calculPlanSoin($_date, &$lines_med[$_date], &$prises_med[$_date], &$list_prises_med[$_date], &$lines_element[$_date], 
	                              &$prises_element[$_date], &$list_prises_element[$_date], &$nb_produit_by_cat[$_date], &$all_lines_med, &$all_lines_element,
	                              &$intitule_prise_med, &$intitule_prise_element);
}

$tabHours = array("08","12","14","18","22","24","02","06");

// Chargement des categories
$categorie = new CCategoryPrescription();
$cats = $categorie->loadList();
foreach($cats as $key => $cat){
	$categories["cat".$key] = $cat;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("last_log", $last_log);
$smarty->assign("pharmacien", $pharmacien);
$smarty->assign("list_prises_med", $list_prises_med);
$smarty->assign("list_prises_element", $list_prises_element);
$smarty->assign("tabHours", $tabHours);
$smarty->assign("prescription_id", $prescription_id);
$smarty->assign("dates", $dates);
$smarty->assign("prises_med", $prises_med);
$smarty->assign("lines_med", $lines_med);
$smarty->assign("prises_element",$prises_element);
$smarty->assign("lines_element", $lines_element);
$smarty->assign("nb_produit_by_cat",$nb_produit_by_cat);
$smarty->assign("categories", $categories);
$smarty->assign("all_lines_med", $all_lines_med);
$smarty->assign("all_lines_element", $all_lines_element);
$smarty->assign("intitule_prise_med", $intitule_prise_med);
$smarty->assign("intitule_prise_element", $intitule_prise_element);
$smarty->assign("patient", $patient);
$smarty->assign("sejour", $sejour);
$smarty->assign("poids", $poids);
$smarty->assign("date", $date);
$smarty->display("vw_plan_soin.tpl");

?>
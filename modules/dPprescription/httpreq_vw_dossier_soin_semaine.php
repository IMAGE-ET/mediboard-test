<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

$date = mbGetValueFromGet("date");
$prescription_id = mbGetValueFromGet("prescription_id");

// Creation du tableau de dates
$dates = array(mbDate("-2 DAYS", $date),mbDate("-1 DAYS", $date),$date,mbDate("+1 DAYS", $date),mbDate("+2 DAYS", $date));

// Chargement de la prescription
$prescription = new CPrescription();
$prescription->load($prescription_id);
$prescription->loadRefsLinesMed();
$prescription->loadRefsLinesElementByCat();
$prescription->_ref_object->loadRefPrescriptionTraitement();	 
$traitement_personnel = $prescription->_ref_object->_ref_prescription_traitement;
if($traitement_personnel->_id){
  $traitement_personnel->loadRefsLinesMed("1");
}

// Chargement du poids et de la chambre du patient
$sejour =& $prescription->_ref_object;
$sejour->loadRefPatient();
$patient =& $sejour->_ref_patient;
$patient->loadRefConstantesMedicales();
$const_med = $patient->_ref_constantes_medicales;
$poids = $const_med->poids;

$types = array("med", "elt");
foreach($types as $type){
  $prescription->_prises[$type] = array();
  $prescription->_lines[$type] = array();
  $prescription->_intitule_prise[$type] = array();
}

 	
// Calcul du plan de soin 
foreach($dates as $_date){
  foreach($types as $type){
    $prescription->_list_prises[$type][$_date] = array();
  }
  $prescription->calculPlanSoin($_date);
}

$now = mbDate();

// Chargement des categories pour chaque chapitre
$categories = CCategoryPrescription::loadCategoriesByChap();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejour", $sejour);
$smarty->assign("patient", $patient);
$smarty->assign("poids", $poids);
$smarty->assign("categories", $categories);
$smarty->assign("dates", $dates);
$smarty->assign("prescription", $prescription);
$smarty->assign("now", $now);
$smarty->display("../../dPprescription/templates/inc_vw_dossier_soin_semaine.tpl");

?>
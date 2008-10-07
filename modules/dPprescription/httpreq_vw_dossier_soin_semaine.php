<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

$date = mbGetValueFromGet("date");
$prescription_id = mbGetValueFromGet("prescription_id");
$sejour = new CSejour();
$patient = new CPatient();

// Creation du tableau de dates
$dates = array(mbDate("-2 DAYS", $date),mbDate("-1 DAYS", $date),$date,mbDate("+1 DAYS", $date),mbDate("+2 DAYS", $date));

// Chargement de la prescription
$prescription = new CPrescription();

if($prescription_id){
  $prescription->load($prescription_id);
  $prescription->loadRefsLinesMed();
  $prescription->loadRefsLinesElementByCat();
  $prescription->_ref_object->loadRefPrescriptionTraitement();	 
  $traitement_personnel = $prescription->_ref_object->_ref_prescription_traitement;
  if($traitement_personnel->_id){
    $traitement_personnel->loadRefsLinesMed("1","1"); 
  }

  // Chargement du poids et de la chambre du patient
  $sejour =& $prescription->_ref_object;
  $sejour->loadRefPatient();
  $sejour->loadRefPraticien();
  $patient =& $sejour->_ref_patient;
  $patient->loadRefConstantesMedicales();

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
}

// Calcul du rowspan pour les medicaments
foreach($prescription->_lines["med"] as $_code_ATC => $_cat_ATC){
  if(!isset($this->_nb_produit_by_cat[$_code_ATC])){
    $prescription->_nb_produit_by_cat[$_code_ATC] = 0;
  }
  foreach($_cat_ATC as $_line) {
    foreach($_line as $line_med){
      $prescription->_nb_produit_by_cat[$_code_ATC]++;
    }
  }
}

// Calcul du rowspan pour les elements
foreach($prescription->_lines["elt"] as $elements_chap){
  foreach($elements_chap as $name_cat => $elements_cat){
    if(!isset($this->_nb_produit_by_cat[$name_cat])){
      $prescription->_nb_produit_by_cat[$name_cat] = 0;
    }
    foreach($elements_cat as $_element){
      $prescription->_nb_produit_by_cat[$name_cat]++;
    }
  }
}     

$now = mbDate();

// Chargement des categories pour chaque chapitre
$categories = CCategoryPrescription::loadCategoriesByChap();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejour", $sejour);
$smarty->assign("patient", $patient);
$smarty->assign("categories", $categories);
$smarty->assign("dates", $dates);
$smarty->assign("prescription", $prescription);
$smarty->assign("now", $now);
$smarty->assign("categorie", new CCategoryPrescription());
$smarty->display("../../dPprescription/templates/inc_vw_dossier_soin_semaine.tpl");

?>
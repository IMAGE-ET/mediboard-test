<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

$sejour_id = mbGetValueFromGetOrSession("sejour_id");
$date      = mbGetValueFromGetOrSession("date");
$now       = mbDateTime();

// Chargement du sejour
$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadRefPatient();
$sejour->loadRefPraticien();

// Chargement de la prescription
$prescription = new CPrescription();
$prescription->object_id = $sejour_id;
$prescription->object_class = "CSejour";
$prescription->type = "sejour";
$prescription->loadMatchingObject();
$prescription_id = $prescription->_id;

// Initialisation
$prises_med = array();
$lines_med = array();
$list_prises_med = array();
$prises_element = array();
$lines_element = array();
$list_prises_element = array();
$nb_produit_by_cat = array();

// Chargement des categories
// Chargement des categories pour chaque chapitre
$categories = CCategoryPrescription::loadCategoriesByChap();


if($prescription->_id){
	
	// Chargement des lignes
	$prescription->loadRefsLinesMed("1");
	$prescription->loadRefsLinesElementByCat();
	$prescription->_ref_object->loadRefPrescriptionTraitement();
		  
	$lines["medicament"] = $prescription->_ref_prescription_lines;
	$traitement_personnel = $prescription->_ref_object->_ref_prescription_traitement;
	if($traitement_personnel->_id){
	  $traitement_personnel->loadRefsLinesMed("1");
	}
	$lines["traitement"] = $traitement_personnel->_ref_prescription_lines;
	  	  

	// Calcul du plan de soin pour la journée $date
	$prescription->calculPlanSoin($lines, $date, $lines_med, $prises_med, $list_prises_med, $lines_element, $prises_element, $list_prises_element, $nb_produit_by_cat);
}	

$tabHours = array("08","12","14","18","22","24","02","06");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("list_prises_med"    , $list_prises_med);
$smarty->assign("list_prises_element", $list_prises_element);
$smarty->assign("tabHours"           , $tabHours);
$smarty->assign("sejour"             , $sejour);
$smarty->assign("prescription_id"    , $prescription_id);
$smarty->assign("date"               , $date);
$smarty->assign("now"                , $now);
$smarty->assign("prises_med"         , $prises_med);
$smarty->assign("lines_med"          , $lines_med);
$smarty->assign("prises_element"     , $prises_element);
$smarty->assign("lines_element"      , $lines_element);
$smarty->assign("nb_produit_by_cat"  , $nb_produit_by_cat);
$smarty->assign("categories"         , $categories);

$smarty->display("inc_vw_dossier_soins.tpl");

?>
<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: $
* @author Alexis Granger
*/

$date = mbGetValueFromGetOrSession("date");

// Filtres du sejour
$date_min = "$date 00:00:00";
$date_max = "$date 23:59:59";

$token_cat = mbGetValueFromGet("token_cat","");
$cats = explode("|",$token_cat);

$service_id = mbGetValueFromGetOrSession("service_id");

// Filtres sur l'heure des prises
$_filter_time_min = mbGetValueFromGet("_filter_time_min","00:00:00");
$_filter_time_max = mbGetValueFromGet("_filter_time_max","23:59:59");

// Filtres sur les lignes
$_date_line_min = "$date $_filter_time_min";
$_date_line_max = "$date $_filter_time_max";

// Chargement de toutes les prescriptions
$where = array();
$ljoin = array();
$ljoin['sejour'] = 'prescription.object_id = sejour.sejour_id';
$ljoin['affectation'] = 'sejour.sejour_id = affectation.sejour_id';
$ljoin['lit'] = 'affectation.lit_id = lit.lit_id';
$ljoin['chambre'] = 'lit.chambre_id = chambre.chambre_id';
$ljoin['service'] = 'chambre.service_id = service.service_id';
$where['prescription.type'] = " = 'sejour'";
$where[] = "(sejour.entree_prevue BETWEEN '$date_min' AND '$date_max') OR 
            (sejour.sortie_prevue BETWEEN '$date_min' AND '$date_max') OR
            (sejour.entree_prevue <= '$date_min' AND sejour.sortie_prevue >= '$date_max')"; 
$where['service.service_id'] = " = '$service_id'";
$prescription = new CPrescription();
$prescriptions = $prescription->loadList($where, null, null, null, $ljoin);
$lines = array();
$lines_produit = array();
$patients = array();
$prises = array();


foreach($prescriptions as $_prescription){
	$sejour =& $_prescription->_ref_object;
	$sejour->loadRefPatient();
	// Stockage de la liste des patients
	$patients[$sejour->_ref_patient->_id] = $sejour->_ref_patient;
	
	// Chargement des lignes de med
	if(in_array("med", $cats)){
	  $_prescription->loadRefsLinesMed();
	  $lines["medicament"] = $_prescription->_ref_prescription_lines;
	}
	
	// Chargement des lignes d'elements
	$_prescription->loadRefsLinesElement();
  foreach($_prescription->_ref_prescription_lines_element as $_line_element){
	  if(in_array($_line_element->_ref_element_prescription->category_prescription_id, $cats)){
		  $lines["element"][$_line_element->_id] = $_line_element;
		}
	}  	
	
	foreach($lines as $lines_by_type){
		if(count($lines_by_type)){
			foreach($lines_by_type as $line){	
				if(($line->_debut_reel <= $_date_line_min && $line->_fin_reelle >= $_date_line_min) ||
				  ($line->_debut_reel <= $_date_line_max && $line->_fin_reelle >= $_date_line_max) ||
				  ($line->_debut_reel >= $_date_line_min && $line->_fin_reelle <= $_date_line_max)) {
					$lines_produit[$line->_class_name][$line->_id] = $line;
					$line->loadRefsPrises();
				  foreach($line->_ref_prises as $_prise){
				  	$_prise->loadRefsFwd();
				  	if($_prise->_type == "moment"){
				  		if(($_prise->_ref_moment->heure > $_filter_time_min) && ($_prise->_ref_moment->heure < $_filter_time_max)){
				  		  $prises[$sejour->_ref_patient->_id][$_prise->_ref_moment->heure][$line->_class_name][$line->_id][$_prise->_id] = $_prise;	
				  		}
				  	}
				  }
				}
			}
		}
	}
}


// Chargement de toutes les categories
$categories = CCategoryPrescription::loadCategoriesByChap();

// Initialisation des filtres
$prescription = new CPrescription();
$prescription->_filter_time_min = $_filter_time_min;
$prescription->_filter_time_max = $_filter_time_max;

$token_cat = implode("|", $cats);

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("token_cat", $token_cat);
$smarty->assign("cats", $cats);
$smarty->assign("categories", $categories);
$smarty->assign("prescription", $prescription);
$smarty->assign("patients", $patients);
$smarty->assign("prises", $prises);
$smarty->assign("lines_produit", $lines_produit);
$smarty->display('vw_bilan_service.tpl');

?>
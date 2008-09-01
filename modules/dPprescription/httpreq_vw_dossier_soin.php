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

// Chargement des categories pour chaque chapitre
$categories = CCategoryPrescription::loadCategoriesByChap();

if($prescription->_id){	
  $types = array("med", "elt");
 	foreach($types as $type){
 	  $prescription->_prises[$type] = array();
 	  $prescription->_list_prises[$type][$date] = array();
 	  $prescription->_lines[$type] = array();
 	  $prescription->_intitule_prise[$type] = array();
 	}

	// Chargement des lignes
	$prescription->loadRefsLinesMed("1");
	$prescription->loadRefsLinesElementByCat();
	$prescription->_ref_object->loadRefPrescriptionTraitement();
		 
	$traitement_personnel = $prescription->_ref_object->_ref_prescription_traitement;
	if($traitement_personnel->_id){
	  $traitement_personnel->loadRefsLinesMed("1");
	}
	  	  
	// Calcul du plan de soin pour la journée $date
  $prescription->calculPlanSoin($date);
}	

$transmission = new CTransmissionMedicale();
$where = array();
$where[] = "(object_class = 'CCategoryPrescription') OR 
            (object_class = 'CPrescriptionLineElement') OR 
            (object_class = 'CPrescriptionLineMedicament')";

$where["sejour_id"] = " = '$sejour->_id'";
$transmissions_by_class = $transmission->loadList($where);

foreach($transmissions_by_class as $_transmission){
  $_transmission->loadRefsFwd();
	$prescription->_transmissions[$_transmission->object_class][$_transmission->object_id][$_transmission->_id] = $_transmission;
}

$hours = explode("|",CAppUI::conf("dPprescription CPrisePosologie heures_prise"));
sort($hours);
foreach($hours as $_hour){
	$tabHours["$date $_hour:00:00"] = $_hour;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("prescription", $prescription);
$smarty->assign("tabHours"           , $tabHours);
$smarty->assign("sejour"             , $sejour);
$smarty->assign("prescription_id"    , $prescription_id);
$smarty->assign("date"               , $date);
$smarty->assign("now"                , $now);
$smarty->assign("categories"         , $categories);

$smarty->display("inc_vw_dossier_soins.tpl");

?>
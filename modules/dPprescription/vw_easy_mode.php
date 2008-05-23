<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

global $AppUI;

// chargement des categories de prescription et des elements associés
$chapitres = CCategoryPrescription::loadCategoriesByChap();
foreach($chapitres as &$categories){
	foreach($categories as &$category){
		$category->loadElementsPrescription();
	}
}

$prescription_id = mbGetValueFromGet("prescription_id");

// chargement de la prescription
$prescription = new CPrescription();
$prescription->load($prescription_id);

// Chargement de la liste des moments
$moments = CMomentUnitaire::loadAllMomentsWithPrincipal();

// chargement des medicaments favoris du praticien
$medicaments = CPrescription::getFavorisMedPraticien($prescription->praticien_id);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("chapitres", $chapitres);
$smarty->assign("filter_line_element", new CPrescriptionLineElement());
$smarty->assign("moments", $moments);
$smarty->assign("prise_posologie", new CPrisePosologie());
$smarty->assign("class_category", new CCategoryPrescription());
$smarty->assign("medicaments", $medicaments);
$smarty->assign("prescription_id", $prescription_id);
$smarty->display("../../dPprescription/templates/vw_easy_mode.tpl");

?>
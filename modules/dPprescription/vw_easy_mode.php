<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

// chargement des categories de prescription et des elements associés
$chapitres = CCategoryPrescription::loadCategoriesByChap();
foreach($chapitres as &$categories){
	foreach($categories as &$category){
		$category->loadElementsPrescription();
	}
}

// Chargement de la liste des moments
$moments = CMomentUnitaire::loadAllMomentsWithPrincipal();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("chapitres", $chapitres);
$smarty->assign("filter_line_element", new CPrescriptionLineElement());
$smarty->assign("moments", $moments);
$smarty->assign("prise_posologie", new CPrisePosologie());
$smarty->assign("class_category", new CCategoryPrescription());
$smarty->display("../../dPprescription/templates/vw_easy_mode.tpl");

?>
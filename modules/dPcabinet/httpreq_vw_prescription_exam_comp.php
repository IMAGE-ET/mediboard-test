<?php /* $Id:  $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI;

$sejour_id = mbGetValueFromGet("sejour_id");

if($sejour_id == "undefined"){
	return;
}

$sejour = new CSejour();
$sejour->load($sejour_id);

$sejour->_ref_prescriptions = $sejour->loadBackRefs("prescriptions");
$totals_by_chapitre = array();
if($sejour->_ref_prescriptions){
	foreach($sejour->_ref_prescriptions as &$_prescription){
	  // Chargement du nombre d'elements pour chaque prescription
		$_prescription->countLinesMedsElements();
		$_prescription->loadRefPraticien();

		foreach ($_prescription->_counts_by_chapitre as $chapitre => $count) {
  	  @$totals_by_chapitre[$chapitre]+= $count;
  	}
	}
}  
    
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object_id", $sejour_id);
$smarty->assign("object_class", "CSejour");
$smarty->assign("praticien_id", $AppUI->user_id);
$smarty->assign("prescriptions", $sejour->_ref_prescriptions);
$smarty->assign("totals_by_chapitre", $totals_by_chapitre);


$smarty->display("../../dPprescription/templates/inc_widget_prescription.tpl");

?>
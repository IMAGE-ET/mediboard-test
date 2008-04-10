<?php /* $Id:  $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

$prescription_id = mbGetValueFromGetOrSession("prescription_id");
$prescription = new CPrescription();

if (!$prescription->_ref_module) {
  CAppUI::stepAjax("Module Prescriptions non installé", UI_MSG_WARNING);
  return;
}

$prescription->load($prescription_id);

// Chargement des lignes de prescriptions

if ($prescription->_id){
	$prescription->loadRefsLinesMedComments();  
	$prescription->loadRefsLinesElementsComments();

	if($prescription->object_class == "CSejour"){
  	$prescription->loadRefObject();
  	$prescription->_ref_object->loadRefsPrescriptions();
  	foreach($prescription->_ref_object->_ref_prescriptions as $catPrescription){
  		foreach($catPrescription as &$_prescription){
  	    $_prescription->loadRefsLinesMedComments();
  	    $_prescription->loadRefsLinesElementsComments();
  		}
	  }
	}
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object_id", $prescription->object_id);
$smarty->assign("object_class", $prescription->object_class);
$smarty->assign("praticien_id", $prescription->praticien_id);
$smarty->assign("prescription", $prescription);
if($prescription->object_class == "CSejour"){
	$smarty->assign("prescriptions", $prescription->_ref_object->_ref_prescriptions);
}

$smarty->display("inc_widget_prescription.tpl");

?>
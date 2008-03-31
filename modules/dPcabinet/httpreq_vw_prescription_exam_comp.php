<?php /* $Id:  $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Alexis Granger
*/

$sejour_id = mbGetValueFromGet("sejour_id");
if($sejour_id == "undefined"){
	return;
}

$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadRefsPrescriptions();

$prescription = $sejour->_ref_last_prescription;
if(!$prescription->_id){
	$prescription->object_id = $sejour->_id;
	$prescription->object_class = "CSejour";
	$prescription->praticien_id = $sejour->praticien_id;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object_id", $prescription->object_id);
$smarty->assign("object_class", $prescription->object_class);
$smarty->assign("praticien_id", $prescription->praticien_id);
$smarty->assign("prescription", $prescription);

$smarty->display("../../dPprescription/templates/inc_widget_prescription.tpl");

?>
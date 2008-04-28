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

$prescriptions = $sejour->loadBackRefs("prescriptions");
foreach($prescriptions as &$_prescription){
  // Chargement du nombre d'elements pour chaque prescription
	$_prescription->countLinesMedsElements();
	$_prescription->loadRefPraticien();
}
      
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("getActivePrescription", CModule::getActive("dPprescription"));
$smarty->assign("object_id", $sejour_id);
$smarty->assign("object_class", "CSejour");
$smarty->assign("praticien_id", $AppUI->user_id);
$smarty->assign("prescriptions", $prescriptions);

$smarty->display("../../dPprescription/templates/inc_widget_prescription.tpl");

?>
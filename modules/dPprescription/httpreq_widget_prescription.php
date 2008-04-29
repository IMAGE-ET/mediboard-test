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

// Chargement de la prescription
$prescription->load($prescription_id);

if ($prescription->_id){
	$prescription->loadRefObject();
	// Chargement de toutes les prescription de l'objet
  $prescriptions = $prescription->_ref_object->loadBackRefs("prescriptions");
  foreach($prescriptions as &$_prescription){
  	// Chargement du nombre d'elements pour chaque prescription
  	$_prescription->countLinesMedsElements();
  	$_prescription->loadRefPraticien();
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("today", mbDate());
$smarty->assign("object_id", $prescription->object_id);
$smarty->assign("object_class", $prescription->object_class);
$smarty->assign("praticien_id", $prescription->praticien_id);
$smarty->assign("prescription", $prescription);
$smarty->assign("prescriptions", $prescriptions);

$smarty->display("inc_widget_prescription.tpl");

?>
<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Romain Ollivier
 */	      

global $AppUI, $can, $m;

$can->needsRead();

$sejour_id = mbGetValueFromGetOrSession("sejour_id");

$sejour = new CSejour();
$sejour->load($sejour_id);

$prescription = new CPrescription();

// Chargement de la prescription
$prescription_sejour = new CPrescription();
$where = array();
$where["object_id"] = " = '$sejour_id'";
$where["object_class"] = " = 'CSejour'";
$where["type"] = " != 'traitement'";
$order = "prescription_id DESC";
$prescriptions_sejour = $prescription_sejour->loadList($where, $order);
if(count($prescriptions_sejour)){
  $prescription =& end($prescriptions_sejour);
}
foreach($prescriptions_sejour as $_prescription_sejour){
  if($_prescription_sejour->type == "sejour"){
  	$prescription =& $_prescription_sejour;
  	break;
  }
}

// Chargement des medicaments et commentaires de medicament
if ($prescription->_id) {
  $prescription->loadRefsLinesMed();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("prescription", $prescription);
$smarty->display("inc_vw_prescription_meds.tpl");

?>
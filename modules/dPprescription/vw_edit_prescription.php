<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Romain Ollivier
 */

global $AppUI, $can, $m;

$can->needsRead();

$prescription_id = mbGetValueFromGetOrSession("prescription_id");
$object_class    = mbGetValueFromGetOrSession("object_class");
$object_id       = mbGetValueFromGetOrSession("object_id");

// Chargement de la catégorie demandé
$prescription = new CPrescription();
$prescription->load($prescription_id);
if(!$prescription->_id) {
  $prescription->object_class = $object_class;
  $prescription->object_id    = $object_id;
}
if($prescription->object_id) {
  $prescription->loadRefsFwd();
  $prescription->_ref_object->loadRefSejour();
  $prescription->_ref_object->loadRefPatient();
  $prescription->_ref_object->loadRefsPrescriptions();
}
if($prescription->_id) {
  $prescription->loadRefsLines();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("prescription", $prescription);

$smarty->display("vw_edit_prescription.tpl");

?>
<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision: $
 *  @author Romain Ollivier
 */
 
global $can;

$can->needsRead();

$typeListe = mbGetValueFromGetOrSession("typeListe");

// Chargement de l'item choisi
$prescriptionItem = new CPrescriptionLaboExamen;
$prescriptionItem->load(mbGetValueFromGetOrSession("prescription_labo_examen_id"));
$siblingItems = array();
if($prescriptionItem->loadRefs()) {
  $siblingItems = $prescriptionItem->loadSiblings();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("prescriptionItem", $prescriptionItem);
$smarty->assign("siblingItems", $siblingItems);


$smarty->display("inc_edit_resultat.tpl");
?>

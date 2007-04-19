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
$prescriptionItem->loadRefs();

$siblingItems = new CPrescriptionLaboExamen;


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("prescriptionItem", $prescriptionItem);

$smarty->display("inc_edit_resultat.tpl");
?>

<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

global $AppUI, $can, $m;

$can->needsRead();

$prescription_line_id = mbGetValueFromGetOrSession("prescription_line_id");
$typeDate = mbGetValueFromGetOrSession("chapitre");

// Chargement de la ligne de prescription
if($typeDate == "Med"){
  // Medicaments
	$prescription_line = new CPrescriptionLineMedicament();
  $prescription_line->load($prescription_line_id);
  $type = "Med";
} else {
  // Elements
	$prescription_line = new CPrescriptionLineElement();
	$prescription_line->load($prescription_line_id);
  $type = "Soin";
}

// Chargement des prises de la ligne de prescription
$prescription_line->loadRefsPrises();

// Chargement de la liste des moments
$moments = CMomentUnitaire::loadAllMomentsWithPrincipal();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("type", $type);
$smarty->assign("typeDate" , $typeDate);
$smarty->assign("line"     , $prescription_line);
$smarty->assign("moments"  , $moments);

$smarty->display("../../dPprescription/templates/line/inc_vw_prises_posologie.tpl");

?>
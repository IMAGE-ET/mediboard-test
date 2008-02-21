<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Romain Ollivier
 */

global $AppUI, $can, $m, $g;

$can->needsRead();

$etablissement = new CGroups();
$etablissement->load($g);

$prescription_id = mbGetValueFromGetOrSession("prescription_id");
$prescription = new CPrescription();
$prescription->load($prescription_id);
$prescription->loadRefsFwd();
$prescription->_ref_praticien->loadRefsFwd();
$prescription->loadRefsLines();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"         , mbDate());
$smarty->assign("etablissement", $etablissement);
$smarty->assign("prescription" , $prescription);

$smarty->display("print_prescription.tpl");

?>
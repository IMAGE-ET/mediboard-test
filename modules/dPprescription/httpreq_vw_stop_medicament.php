<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

$line_id = mbGetValueFromGet("line_id");

// Chargement de la ligne de prescription
$line_medicament = new CPrescriptionLineMedicament();
$line_medicament->load($line_id);
$line_medicament->loadRefUserArret();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("today"              , mbDate());
$smarty->assign("curr_line", $line_medicament);
$smarty->display("inc_vw_stop_medicament.tpl");

?>
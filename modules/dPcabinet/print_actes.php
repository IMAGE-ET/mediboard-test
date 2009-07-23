<?php /* $Id: print_fiche.php 6271 2009-05-12 14:39:22Z alexis_granger $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 6271 $
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsEdit();
$consultation_id = mbGetValueFromGet("consultation_id");

// Consultation courante
$consult = new CConsultation();
if ($consultation_id) {
  $consult->load($consultation_id);
  $consult->loadView();
	$consult->loadRefsActesNGAP();
	$consult->loadRefPraticien();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("consult", $consult);
$smarty->display("print_actes.tpl");

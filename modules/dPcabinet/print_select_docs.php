<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m;

$consultation_id = mbGetValueFromGet("consultation_id");
$sejour_id = mbGetValueFromGet("sejour_id");

// Chargement de la prescription de pre-admission
$prescription_pre_adm = new CPrescription();
if($sejour_id){
	$prescription_pre_adm->object_id = $sejour_id;
	$prescription_pre_adm->object_class = "CSejour";
	$prescription_pre_adm->type = "pre_admission";
	$prescription_pre_adm->loadMatchingObject();
}

// Consultation courante
$consult = new CConsultation();
$consult->load($consultation_id);
$can->edit &= $consult->canEdit();

$can->needsEdit();
$can->needsObject($consult);

$consult->loadRefsDocs();  

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("prescription_pre_adm_id", $prescription_pre_adm->_id);
$smarty->assign("consult"   , $consult);
$smarty->assign("documents" , $consult->_ref_documents);

$smarty->display("print_select_docs.tpl");
?>

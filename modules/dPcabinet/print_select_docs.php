<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $can;

$consultation_id = CValue::get("consultation_id");
$sejour_id       = CValue::get("sejour_id");

// Chargement de la prescription de pre-admission
$prescription_preadm = new CPrescription();
$prescription_sortie = new CPrescription();
if($sejour_id){
	$prescription_sortie->object_id = $prescription_preadm->object_id = $sejour_id;
	$prescription_sortie->object_class = $prescription_preadm->object_class = "CSejour";
  
	$prescription_preadm->type = "pre_admission";
	$prescription_preadm->loadMatchingObject();
	
	$prescription_sortie->type = "sortie";
	$prescription_sortie->loadMatchingObject();
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
$smarty->assign("prescription_preadm", $prescription_preadm);
$smarty->assign("prescription_sortie" , $prescription_sortie);
$smarty->assign("consult"             , $consult);
$smarty->assign("documents"           , $consult->_ref_documents);
$smarty->display("print_select_docs.tpl");
?>

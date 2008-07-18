<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage bloodSalvage
* @version $Revision: $
* @author Alexandre Germonneau
*/


global $AppUI, $can, $m;
$AppUI->requireModuleFile("bloodSalvage", "inc_personnel");

$can->needsEdit();

$blood_salvage_id = mbGetValueFromGet("blood_salvage_id");
$blood_salvage = new CBloodSalvage();

if($blood_salvage_id) {
	$blood_salvage->load($blood_salvage_id);
	$blood_salvage->loadrefsFwd();
	$blood_salvage->_ref_operation->loadRefsFwd();
	$blood_salvage->_ref_operation->_ref_anesth->load($blood_salvage->_ref_operation->anesth_id);	
	if($blood_salvage->_ref_operation->type_anesth) {
	$blood_salvage->_ref_operation->_ref_type_anesth->load($blood_salvage->_ref_operation->type_anesth);	
	}
	$blood_salvage->_ref_operation->loadRefPatient();
	$blood_salvage->_ref_operation->_ref_patient->loadRefs();
	$blood_salvage->_ref_operation->_ref_patient->loadRefDossierMedical();
	$blood_salvage->_ref_operation->_ref_patient->loadRefConstantesMedicales();
	
	$anticoag = new CBcbProduit();
	if($blood_salvage->anticoagulant_cip) {
	$anticoag->load($blood_salvage->anticoagulant_cip);
	}
	
  $list_nurse_sspi= CPersonnel::loadListPers("reveil");
  $tabAffected = array();
  $timingAffect = array(); 
	loadAffected($blood_salvage->_id, $list_nurse_sspi, $tabAffected, $timingAffect);
	
}

$smarty = new CSmartyDP();

$smarty->assign("blood_salvage",$blood_salvage);
$smarty->assign("tabAffected",$tabAffected);
$smarty->assign("anticoagulant",$anticoag);
$smarty->display("print_rapport.tpl");
?>
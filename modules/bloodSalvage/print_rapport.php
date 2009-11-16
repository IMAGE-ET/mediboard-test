<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $m;
CAppUI::requireModuleFile("bloodSalvage", "inc_personnel");

$anticoag ="";
$blood_salvage_id = CValue::get("blood_salvage_id");
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
	if(CModule::getActive("dPmedicament")){
		$anticoag = new CBcbProduit();
		if($blood_salvage->anticoagulant_cip) {
	  	$anticoag->load($blood_salvage->anticoagulant_cip);
		}
	} else {
		$list = CAppUI::conf("bloodSalvage AntiCoagulantList");
    $anticoagulant_list = explode("|",$list);
    if($blood_salvage->anticoagulant_cip !== null){
      $anticoag = $anticoagulant_list[$blood_salvage->anticoagulant_cip];		
    }
	}
	
  $list_nurse_sspi= CPersonnel::loadListPers("reveil");
  $tabAffected = array();
  $timingAffect = array(); 
	loadAffected($blood_salvage->_id, $list_nurse_sspi, $tabAffected, $timingAffect);
	$version_patient = CModule::getActive("dPpatients");
}

$smarty = new CSmartyDP();

$smarty->assign("blood_salvage",$blood_salvage);
$smarty->assign("tabAffected",$tabAffected);
$smarty->assign("anticoagulant",CModule::getActive("dPmedicament") ? $anticoag->libelle : $anticoag);
$smarty->display("print_rapport.tpl");
?>
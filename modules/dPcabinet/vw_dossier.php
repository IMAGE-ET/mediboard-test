<?php /* $Id: vw_dossier.php,v 1.5 2006/04/21 16:56:07 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 1.5 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;
require_once( $AppUI->getModuleClass('mediusers') );
require_once( $AppUI->getModuleClass('dPcabinet', 'consultation') );
require_once( $AppUI->getModuleClass('dPplanningOp', 'planning') );
require_once( $AppUI->getModuleClass('dPcompteRendu', 'compteRendu') );
require_once( $AppUI->getModuleClass('dPcompteRendu', 'pack') );
require_once( $AppUI->getModuleClass('dPpatients', 'patients') );

if (!$canEdit) {
	$AppUI->redirect( "m=system&a=access_denied" );
}
// L'utilisateur est-il praticien?
$chirSel = new CMediusers;
$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);
if ($mediuser->isPraticien()) {
  $chirSel = $mediuser;
}

$pat_id = mbGetValueFromGetOrSession("patSel", 0);
$patSel = new CPatient;
$patSel->load($pat_id);
$patient = new CPatient;
$patient->load($pat_id);
$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_READ);

// Chargement des références du patient
if($pat_id) {
  
  // Infos patient complètes (tableau de droite)
  $patient->loadRefs();
  if($patient->_ref_curr_affectation->affectation_id) {
    $patient->_ref_curr_affectation->loadRefsFwd();
    $patient->_ref_curr_affectation->_ref_lit->loadRefsFwd();
    $patient->_ref_curr_affectation->_ref_lit->_ref_chambre->loadRefsFwd();
  } elseif($patient->_ref_next_affectation->affectation_id) {
    $patient->_ref_next_affectation->loadRefsFwd();
    $patient->_ref_next_affectation->_ref_lit->loadRefsFwd();
    $patient->_ref_next_affectation->_ref_lit->_ref_chambre->loadRefsFwd();
  }
  foreach ($patient->_ref_operations as $key => $op) {
    $patient->_ref_operations[$key]->loadRefs();
  }
  foreach ($patient->_ref_hospitalisations as $key => $op) {
    $patient->_ref_hospitalisations[$key]->loadRefs();
  }
  foreach ($patient->_ref_consultations as $key => $consult) {
    $patient->_ref_consultations[$key]->loadRefs();
    $patient->_ref_consultations[$key]->_ref_plageconsult->loadRefs();
  }
  
  // Infos patient du cabinet (tableau de gauche)
  $patSel->loadRefs();
  foreach($patSel->_ref_consultations as $key => $value) {
    $consultation =& $patSel->_ref_consultations[$key];
    $consultation->loadRefs();
    $plageconsult =& $consultation->_ref_plageconsult;
    $plageconsult->loadRefsFwd();
    if (!array_key_exists($plageconsult->chir_id, $listPrat)) {
    	unset($patSel->_ref_consultations[$key]);
    }
  }
  foreach($patSel->_ref_operations as $key => $value) {
    $operation =& $patSel->_ref_operations[$key];
    $operation->loadRefs();
    if (!array_key_exists($operation->chir_id, $listPrat)) {
      unset($patSel->_ref_operations[$key]);
    }
  }
  foreach($patSel->_ref_hospitalisations as $key => $value) {
    $hospitalisation =& $patSel->_ref_hospitalisations[$key];
    $hospitalisation->loadRefs();
    $hospitalisation->_ref_chir->loadRefs();
    if (!array_key_exists($hospitalisation->chir_id, $listPrat)) {
      unset($patSel->_ref_hospitalisations[$key]);
    }
  }
}

$canEditCabinet = !getDenyEdit("dPcabinet");

// Création du template
require_once( $AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP;

$smarty->assign('patSel', $patSel);
$smarty->assign('patient', $patient);
$smarty->assign('chirSel', $chirSel);
$smarty->assign('listPrat', $listPrat);
$smarty->assign('canEditCabinet', $canEditCabinet);

$smarty->display('vw_dossier.tpl');

?>
<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
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

$pat_id = mbGetValueFromGetOrSession("pat_id", 0);
$patient = new CPatient;
$patient->load($pat_id);

// Chargement des praticiens
$listPrat = new CMediusers;
$listPrat = $listPrat->loadPraticiens(PERM_READ);

// Chargement des références du patient
if($patient->patient_id) {
  $patient->loadRefs();
  foreach($patient->_ref_consultations as $key => $value) {
    $patient->_ref_consultations[$key]->loadRefs();
  }
  foreach($patient->_ref_operations as $key => $value) {
    $patient->_ref_operations[$key]->loadRefs();
    $patient->_ref_operations[$key]->loadRefGHM();
    foreach($patient->_ref_operations[$key]->_ref_actes_ccam as $key2 => $value2) {
      $patient->_ref_operations[$key]->_ref_actes_ccam[$key2]->loadRefsFwd();
    }
    $patient->_ref_operations[$key]->_ref_plageop->loadRefsFwd();
    if($patient->_ref_operations[$key]->_ref_consult_anesth->consultation_anesth_id) {
      $patient->_ref_operations[$key]->_ref_consult_anesth->loadRefsFwd();
      $patient->_ref_operations[$key]->_ref_consult_anesth->_ref_plageconsult->loadRefsFwd();
    }
  }
  foreach($patient->_ref_hospitalisations as $key => $value) {
    $patient->_ref_hospitalisations[$key]->loadRefs();
  }
}

$canEditCabinet = !getDenyEdit("dPcabinet");

// Création du template
require_once( $AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP;

$smarty->debugging = false;

$smarty->assign('patient', $patient);
$smarty->assign('listPrat', $listPrat);
$smarty->assign('canEditCabinet', $canEditCabinet);

$smarty->display('vw_dossier.tpl');

?>
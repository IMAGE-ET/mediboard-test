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

// Chargement des praticiens
$listPrat = new CMediusers;
$listPrat = $listPrat->loadPraticiens(PERM_READ);

// Chargement complet du dossier patient
$pat_id = mbGetValueFromGetOrSession("pat_id");
$patient = new CPatient;
$patient->load($pat_id);
if ($patient->patient_id) {
  $patient->loadDossierComplet();
    
  // Chargement complémentaires sur les opérations
  foreach ($patient->_ref_operations as $keyOp => $valueOp) {
    $operation =& $patient->_ref_operations[$keyOp];
    $consultAnest =& $operation->_ref_consult_anesth;
    
    $operation->loadRefGHM();
    
    foreach ($operation->_ref_actes_ccam as $keyActe => $valueActe) {
      $acte =& $operation->_ref_actes_ccam[$keyActe];
      $acte->loadRefsFwd();
    }
    
    $plage =& $operation->_ref_plageop;
    $plage->loadRefsFwd();
    
    if ($consultAnest->consultation_anesth_id) {
      $consultAnest->loadRefsFwd();
      $consultAnest->_ref_plageconsult->loadRefsFwd();
    }
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
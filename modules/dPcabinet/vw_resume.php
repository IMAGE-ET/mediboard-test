<?php /* $Id: vw_resume.php,v 1.2 2006/04/21 16:56:07 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 1.2 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('dPpatients', 'patients') );

if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$patient_id = mbGetValueFromGet("patient_id");

$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefs();
foreach($patient->_ref_consultations as $key => $value) {
  $patient->_ref_consultations[$key]->loadRefs();
}
foreach($patient->_ref_operations as $key => $value) {
  $patient->_ref_operations[$key]->loadRefs();
}

$consultations =& $patient->_ref_consultations;
$operations =& $patient->_ref_operations;

$user = new CMediusers;
$user->load($AppUI->user_id);
$listPrat = $user->loadPraticiens(PERM_EDIT);

// Consultations
foreach($consultations as $key => $value) {
  $prat = $value->_ref_plageconsult->chir_id;
  if(!isset($listPrat[$prat])) {
    unset($consultations[$key]);
  }
}

// Interventions
foreach($operations as $key => $value) {
  $prat = $value->_ref_plageop->chir_id;
  if(!isset($listPrat[$prat])) {
    unset($operations[$key]);
  }
}

// Documents
$docsCons = array();
$docsOp = array();
foreach($consultations as $key => $value) {
  if($consultations[$key]->_ref_documents) {
    $docsCons = array_merge($docsCons, $consultations[$key]->_ref_documents);
  }
}
foreach($operations as $key => $value) {
  if($operations[$key]->_ref_documents) {
    $docsOp = array_merge($docsOp, $operations[$key]->_ref_documents);
  }
}

// Fichiers
$filesCons = array();
$filesOp = array();
foreach($consultations as $key => $value) {
  if($consultations[$key]->_ref_files) {
    $filesCons = array_merge($filesCons, $consultations[$key]->_ref_files);
  }
}
foreach($operations as $key => $value) {
  if($operations[$key]->_ref_files) {
    $filesOp = array_merge($filesOp, $operations[$key]->_ref_files);
  }
}

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('consultations' , $consultations );
$smarty->assign('operations', $operations);
$smarty->assign('docsCons', $docsCons);
$smarty->assign('docsOp', $docsOp);
$smarty->assign('filesCons', $filesCons);
$smarty->assign('filesOp', $filesOp);

$smarty->display('vw_resume.tpl');

?>
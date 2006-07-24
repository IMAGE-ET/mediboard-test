<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("dPpatients", "patients"));

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
foreach($patient->_ref_sejours as $key => $value) {
  $patient->_ref_sejours[$key]->loadRefs();
}

$consultations =& $patient->_ref_consultations;
$sejours =& $patient->_ref_sejours;

$user = new CMediusers;
$user->load($AppUI->user_id);
$listPrat = $user->loadPraticiens(PERM_EDIT);

$docsCons = array();
$docsOp = array();
$filesCons = array();
$filesOp = array();

// Consultations
foreach($consultations as $key => $value) {
  $prat = $value->_ref_plageconsult->chir_id;
  if(!isset($listPrat[$prat])) {
    unset($consultations[$key]);
  } else {
    if($consultations[$key]->_ref_documents) {
      $docsCons = array_merge($docsCons, $consultations[$key]->_ref_documents);
    }
    if($consultations[$key]->_ref_files) {
      $filesCons = array_merge($filesCons, $consultations[$key]->_ref_files);
    }
  }
}

// Sejours
foreach($sejours as $key => $sejour) {
  $sejours[$key]->loadRefsOperations();
  foreach($sejours[$key]->_ref_operations as $keyOp => $op) {
    $sejours[$key]->_ref_operations[$keyOp]->loadRefs();
    $prat = $op->_ref_plageop->chir_id;
    if(!isset($listPrat[$prat])) {
      unset($sejours[$key]->_ref_operations[$keyOp]);
    } else {
      if($sejours[$key]->_ref_operations[$keyOp]->_ref_documents) {
        $docsOp = array_merge($docsOp, $sejours[$key]->_ref_operations[$keyOp]->_ref_documents);
      }
      if($sejours[$key]->_ref_operations[$keyOp]->_ref_files) {
        $filesOp = array_merge($filesOp, $sejours[$key]->_ref_operations[$keyOp]->_ref_files);
      }
    }
  }
}

// Création du template
require_once( $AppUI->getSystemClass ("smartydp" ) );
$smarty = new CSmartyDP(1);

$smarty->assign("consultations" , $consultations );
$smarty->assign("sejours"       , $sejours);
$smarty->assign("docsCons"      , $docsCons);
$smarty->assign("docsOp"        , $docsOp);
$smarty->assign("filesCons"     , $filesCons);
$smarty->assign("filesOp"       , $filesOp);

$smarty->display("vw_resume.tpl");

?>
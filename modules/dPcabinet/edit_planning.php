<?php /* $Id: edit_planning.php,v 1.7 2006/04/21 16:56:07 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 1.7 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('dPcabinet', 'consultation') );

if (!$canEdit) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

$consult = new CConsultation();
$chir = new CMediusers;
$pat = new CPatient;

$consultation_id = mbGetValueFromGetOrSession("consultation_id");
if (!$consultation_id) {
  // L'utilisateur est-il praticien?
  $mediuser = new CMediusers();
  $mediuser->load($AppUI->user_id);
  if ($mediuser->isPraticien()) {
    $chir = $mediuser;
  }
  
  // A t'on fourni l'id du praticien
  if ($chir_id = mbGetValueFromGetOrSession("chir_id")) {
    $chir->load($chir_id);
  }

  // A t'on fourni l'id du patient
  if ($pat_id = mbGetValueFromGet("pat_id")) {
    $pat->load($pat_id);
  }
} else {
  $consult->load($consultation_id);
  $consult->loadRefs();
  $consult->_ref_plageconsult->loadRefs();

  $chir =& $consult->_ref_plageconsult->_ref_chir;
  $pat  =& $consult->_ref_patient;
}
// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('consult', $consult);
$smarty->assign('chir', $chir);
$smarty->assign('pat', $pat);

$smarty->display('addedit_planning.tpl');

?>
<?php /* $Id: print_fiche.php,v 1.2 2006/04/21 16:56:07 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 1.2 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('dPcabinet', 'consultation') );
require_once( $AppUI->getModuleClass('mediusers') );

if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$date = mbGetValueFromGetOrSession("date", mbDate());
$today = mbDate();

$consultation_id = mbGetValueFromGet("consultation_id");

// Consultation courante
$consult = new CConsultation();
if ($consultation_id) {
  $consult->load($consultation_id);
  $consult->loadRefs();
  if($consult->_ref_consult_anesth->consultation_anesth_id) {
    $consult->_ref_consult_anesth->loadRefs();
  }
  $praticien =& $consult->_ref_chir;
  $praticien->loadRefs();
  $patient =& $consult->_ref_patient;
  $patient->loadRefs();
  foreach ($patient->_ref_consultations as $key => $value) {
    $patient->_ref_consultations[$key]->loadRefs();
    $patient->_ref_consultations[$key]->_ref_plageconsult->loadRefs();
  }
  foreach ($patient->_ref_operations as $key => $value) {
    $patient->_ref_operations[$key]->loadRefs();
  }
  foreach ($patient->_ref_hospitalisations as $key => $value) {
    $patient->_ref_hospitalisations[$key]->loadRefs();
  }
}

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('consult', $consult);

$smarty->display('print_fiche.tpl');
?>
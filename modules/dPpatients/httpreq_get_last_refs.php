<?php /* $Id: httpreq_get_last_refs.php,v 1.1 2005/09/11 23:59:49 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 1.1 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;
require_once( $AppUI->getModuleClass('dPpatients', 'patients') );

$patient_id = mbGetValueFromGet("patient_id", 0);
$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefs();
foreach($patient->_ref_operations as $key => $value) {
  $patient->_ref_operations[$key]->loadRefsFwd();
}
foreach($patient->_ref_consultations as $key => $value) {
  $patient->_ref_consultations[$key]->loadRefsFwd();
  $patient->_ref_consultations[$key]->_ref_plageconsult->loadRefsFwd();
}

if ($canRead) {
  // Création du template
  require_once( $AppUI->getSystemClass ('smartydp' ) );
  $smarty = new CSmartyDP;

  $smarty->assign('patient', $patient);

  $smarty->display('httpreq_get_last_refs.tpl');
}
<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$patient_id = CValue::get("patient_id", 0);
$consultation_id = CValue::get("consultation_id", 0);

$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefs();

$consultation = new CConsultation();
$consultation->load($consultation_id);
$consultation->loadRefConsultAnesth();

foreach($patient->_ref_sejours as $_sejour) {
  $_sejour->loadRefsOperations();
  $_sejour->loadRefsConsultations();
  foreach($_sejour->_ref_consultations as $_consult) {
    $_consult->getType();
  }
  foreach($_sejour->_ref_operations as $_op) {
    $_op->loadRefsFwd();
  }
}
foreach($patient->_ref_consultations as $_key => $_consult) {
	if ($_consult->annule || $_consult->sejour_id) {
		unset($patient->_ref_consultations[$_key]);
		continue;
	}
  $_consult->loadRefsFwd();
  $_consult->getType();
  $_consult->loadRefPraticien();
  $_consult->_ref_plageconsult->loadRefsFwd();
}

if ($can->read) {
  // Création du template
  $smarty = new CSmartyDP();
  
  $smarty->assign("consultation", $consultation);
  $smarty->assign("patient", $patient);

  $smarty->display("httpreq_get_last_refs.tpl");
}

?>
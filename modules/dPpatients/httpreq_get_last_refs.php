<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

$patient_id = CValue::get("patient_id", 0);
$consultation_id = CValue::get("consultation_id", 0);
$is_anesth = CValue::get("is_anesth", 1);

$patient = new CPatient;
$patient->load($patient_id);
$where = array("group_id" => "= '".CGroups::loadCurrent()->_id."'");
$patient->loadRefsSejours($where);
$patient->loadRefsConsultations();

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

if (CCanDo::read()) {
  // Création du template
  $smarty = new CSmartyDP();
  
  $smarty->assign("is_anesth", $is_anesth);
  $smarty->assign("consultation", $consultation);
  $smarty->assign("patient", $patient);

  $smarty->display("httpreq_get_last_refs.tpl");
}

?>
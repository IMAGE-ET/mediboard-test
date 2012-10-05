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
$where = array(
  "group_id" => "= '".CGroups::loadCurrent()->_id."'",
  "annule"   => "= '0'"
);
         
foreach ($patient->loadRefsSejours($where) as $_sejour) {
  foreach ($_sejour->loadRefsConsultations() as $_consult) {
    $_consult->getType();
    $_consult->loadRefPlageConsult();
    $_consult->loadRefPraticien()->loadRefFunction();
  }
  
  foreach ($_sejour->loadRefsOperations(array("annulee" => "= '0'")) as $_operation) {
    $_operation->loadRefsFwd();
  }
}

foreach ($patient->loadRefsConsultations(array("annule" => "= '0'")) as $_consult) {
  if ($_consult->sejour_id) {
    unset($patient->_ref_consultations[$_consult->_id]);
    continue;
  }
  
  $function = $_consult->loadRefPraticien()->loadRefFunction();
  if ($function->group_id != CGroups::loadCurrent()->_id) {
    unset($patient->_ref_consultations[$_consult->_id]);
    continue;
  }

  $_consult->getType();
  $_consult->loadRefPlageConsult();
  
  // Facture de consultation
  $facture = $_consult->loadRefFacture();
  if ($facture->_id) {
    $facture->loadRefsNotes();
  }
}

$consultation = new CConsultation();
$consultation->load($consultation_id);
$consultation->loadRefConsultAnesth();

if (CCanDo::read()) {
  // Création du template
  $smarty = new CSmartyDP();
  
  $smarty->assign("is_anesth", $is_anesth);
  $smarty->assign("consultation", $consultation);
  $smarty->assign("patient", $patient);

  $smarty->display("httpreq_get_last_refs.tpl");
}

?>
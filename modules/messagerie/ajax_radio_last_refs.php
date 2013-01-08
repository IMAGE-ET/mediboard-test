<?php /** $Id$ **/

/**
 * @package    Mediboard
 * @subpackage messagerie
 * @version    $Revision$
 * @author     SARL OpenXtrem
 */

CCanDo::checkRead();

$patient_id = CValue::getOrSession("patient_id");

$patient = new CPatient();
$patient->load($patient_id);

$where = array(
  "group_id" => "= '".CGroups::loadCurrent()->_id."'",
  "annule"   => "= '0'"
);

//sejours & opé
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

//consultations
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

$smarty = new CSmartyDP();
$smarty->assign("patient", $patient);
$smarty->display("ajax_radio_last_refs.tpl");
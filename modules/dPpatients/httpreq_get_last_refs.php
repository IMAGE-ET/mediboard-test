<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$patient_id      = CValue::get("patient_id", 0);
$consultation_id = CValue::get("consultation_id", 0);
$is_anesth       = CValue::get("is_anesth", 1);
$group = CGroups::loadCurrent();

$patient = new CPatient;
$patient->load($patient_id);
$where = array(
  "group_id" => "= '".$group->_id."'",
  "annule"   => "= '0'"
);
         
foreach ($patient->loadRefsSejours($where) as $_sejour) {
  foreach ($_sejour->loadRefsOperations(array("annulee" => "= '0'")) as $_operation) {
    $_operation->loadRefChir()->loadRefFunction();
    $_operation->loadRefPatient();
    $_operation->loadRefPlageOp();
  }
}

foreach ($patient->loadRefsConsultations() as $_consult) {
  $_consult->getType();
  $_consult->loadRefPlageConsult();
  $function = $_consult->loadRefPraticien()->loadRefFunction();

  if ($_consult->sejour_id) {
    unset($patient->_ref_consultations[$_consult->_id]);
    if (isset($patient->_ref_sejours[$_consult->sejour_id])) {
      $patient->_ref_sejours[$_consult->sejour_id]->_ref_consultations[$_consult->_id] = $_consult;
    }
  }

  if ($function->group_id != $group->_id) {
    unset($patient->_ref_consultations[$_consult->_id]);
    continue;
  }
  $_consult->loadRefFacture()->loadRefsNotes();
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

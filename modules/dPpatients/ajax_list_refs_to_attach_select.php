<?php 

/**
 * $Id$
 *  
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$patient_id   = CValue::getOrSession("patient_id");
$object_guid  = CValue::getOrSession("object_guid");

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
$smarty->assign("object_guid", $object_guid);
$smarty->display("inc_list_refs_to_link.tpl");
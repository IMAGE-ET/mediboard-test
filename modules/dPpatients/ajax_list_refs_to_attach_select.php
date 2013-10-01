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

/**
 * List ccodable of patient.
 * If prat != codable->prat, disable the check
 */

CCanDo::checkEdit();

$patient_id   = CValue::getOrSession("patient_id");
$object_guid  = CValue::getOrSession("object_guid");
$prat_id      = CValue::get("prat_id");
$date_guess   = CValue::get("date");  //datetime

// patient
$patient = new CPatient();
$patient->load($patient_id);
$patient->loadRefPhotoIdentite();

// praticien
$praticien = new CMediusers();
$praticien->load($prat_id);

$where = array(
  "group_id" => "= '".CGroups::loadCurrent()->_id."'",
  "annule"   => "= '0'"
);

//sejours & opé
foreach ($patient->loadRefsSejours($where) as $_sejour) {
  $_sejour->loadRefPraticien();
  $_sejour->_guess_status = 0;

  if ($date_guess >= $_sejour->entree && $date_guess <= $_sejour->sortie) {
    $_sejour->_guess_status = 1;
    if ($_sejour->_ref_praticien->function_id == $praticien->function_id) {
      $_sejour->_guess_status = 2;
      if ($_sejour->_ref_praticien->_id == $prat_id) {
        $_sejour->_guess_status = 3;
      }
    }
  }

  //consult de sejour
  foreach ($_sejour->loadRefsConsultations() as $_consult) {
    $_consult->getType();
    $_consult->loadRefPlageConsult();
    $_consult->loadRefPraticien()->loadRefFunction();
    $_consult->_guess_status = 0;

    if ($date_guess >= $_sejour->entree && $date_guess <= $_sejour->sortie) {
      $_consult->_guess_status = 1;
      if ($_consult->_ref_praticien->function_id == $praticien->function_id) {
        $_consult->_guess_status = 2;
        if ($_consult->_ref_praticien->_id == $prat_id) {
          $_consult->_guess_status = 3;
        }
      }
    }
  }

  //interv du sejour
  foreach ($_sejour->loadRefsOperations(array("annulee" => "= '0'")) as $_operation) {
    $_operation->loadRefsFwd();

    if ($date_guess >= $_operation->debut_op && $date_guess <= $_operation->fin_op) {
      $_operation->_guess_status = 1;
      if ($_operation->_ref_praticien->function_id == $praticien->function_id) {
        $_operation->_guess_status = 2;
        if ($_operation->_ref_praticien->_id == $prat_id) {
          $_operation->_guess_status = 3;
        }
      }
    }
  }
}

//consultations
foreach ($patient->loadRefsConsultations(array("annule" => "= '0'")) as $_consult) {
  $_consult->_guess_status = 0;
  if ($_consult->sejour_id) {
    unset($patient->_ref_consultations[$_consult->_id]);
    continue;
  }

  $function = $_consult->loadRefPraticien()->loadRefFunction();
  if ($function->group_id != CGroups::loadCurrent()->_id) {
    unset($patient->_ref_consultations[$_consult->_id]);
    continue;
  }

  $plage = $_consult->loadRefPlageConsult();
  if ($date_guess == $plage->date) {
    $_consult->_guess_status = 1;
    if ($function->_id != $praticien->function_id) {
      $_consult->_guess_status = 2;
      if ($_consult->_ref_praticien->_id == $prat_id) {
        $_consult->_guess_status = 3;
      }
    }
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
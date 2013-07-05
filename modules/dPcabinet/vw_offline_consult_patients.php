<?php 

/**
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$date = CValue::get("date", CMbDT::date());

//plages de consultation
$Pconsultation = new CPlageconsult();
$Pconsultation->date = $date;
$Pconsultations = $Pconsultation->loadMatchingList(array("debut"));

$consultations = array();
$resumes_patient = array();

/** @var $Pconsultations CPlageConsult[] */
foreach ($Pconsultations as $_plage_consult) {
  $_plage_consult->loadRefsConsultations(false);
  $_plage_consult->loadRefChir();
  $_plage_consult->updateFormFields();
  $_plage_consult->loadFillRate();

  /** @var $consultations CConsultation[] */
  foreach ($_plage_consult->_ref_consultations as $_consult) {

    $patient = $_consult->loadRefPatient();
    $patient->loadDossierComplet();
    $patient->loadRefDossierMedical();
    $smarty = new CSmartyDP();
    $smarty->assign("patient", $_consult->_ref_patient);
    $resumes_patient[$patient->_id] = $smarty->fetch("vw_resume.tpl");  //dynamic assignment
  }
}



//smarty global
$smarty = new CSmartyDP();
$smarty->assign("consultations", $Pconsultations);
$smarty->assign("resumes_patient", $resumes_patient);
$smarty->assign("date", $date);
$smarty->display("vw_offline/consult_patients.tpl");
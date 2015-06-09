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

CCanDo::checkRead();
$patient_id   = CValue::getOrSession("patient_id", 0);
$vw_cancelled = CValue::get("vw_cancelled", 0);

// Récuperation du patient sélectionné
$patient = new CPatient();
if (CValue::get("new", 0)) {
  $patient->load(null);
  CValue::setSession("id", null);
}
else {
  $patient->load($patient_id);
}

$nb_sejours_annules = 0;
$nb_ops_annulees = 0;
$nb_consults_annulees = 0;

if ($patient->_id) {
  $patient->loadDossierComplet(null, false);
  $patient->loadIPP();
  $patient->loadPatientLinks();
  $patient->countINS();
  if (CModule::getActive("fse")) {
    $cv = CFseFactory::createCV();
    if ($cv) {
      $cv->loadIdVitale($patient);
    }
  }

  if (!$vw_cancelled) {
    foreach ($patient->_ref_sejours as $_key => $_sejour) {
      foreach ($_sejour->_ref_operations as $_key_op => $_operation) {
        if ($_operation->annulee) {
          unset ($_sejour->_ref_operations[$_key_op]);
          $nb_ops_annulees++;
        }
      }
      if ($_sejour->annule) {
        unset($patient->_ref_sejours[$_key]);
        $nb_sejours_annules++;
      }
    }
    // Suppression des consultations annulees
    foreach ($patient->_ref_consultations as $consult) {
      if ($consult->annule) {
        unset($patient->_ref_consultations[$consult->_id]);
        $nb_consults_annulees++;
      }
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient"             , $patient);
$smarty->assign("canPatients"         , CModule::getCanDo("dPpatients"));
$smarty->assign("canAdmissions"       , CModule::getCanDo("dPadmissions"));
$smarty->assign("canPlanningOp"       , CModule::getCanDo("dPplanningOp"));
$smarty->assign("canCabinet"          , CModule::getCanDo("dPcabinet"));
$smarty->assign("nb_sejours_annules"  , $nb_sejours_annules);
$smarty->assign("nb_ops_annulees"     , $nb_ops_annulees);
$smarty->assign("nb_consults_annulees", $nb_consults_annulees);
$smarty->assign("vw_cancelled"        , $vw_cancelled);

$smarty->display("inc_vw_patient.tpl");

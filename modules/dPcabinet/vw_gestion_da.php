<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();
$conusultation_id  = CValue::getOrSession("conusultation_id");
$consult_anesth  = CValue::getOrSession("consult_anesth_id");

$consult = new CConsultation();
$consult->load($conusultation_id);
$consult->loadRefPlageConsult();
$patient = $consult->loadRefPatient();
$patient->loadRefsSejours();
foreach ($patient->_ref_sejours as $_sejour) {
  $_sejour->loadRefsOperations();
  foreach ($_sejour->_ref_operations as $op) {
    $op->loadRefPlageOp();
    $op->loadRefChir();
  }
}
$patient->getNextSejourAndOperation();
// Chargement du patient
$patient = $consult->_ref_patient;
$patient->countBackRefs("consultations");
$patient->countBackRefs("sejours");
$patient->loadRefs();
$patient->loadRefsNotes();
$patient->loadRefPhotoIdentite();

$sejour = new CSejour();
$group_id = CGroups::loadCurrent()->_id;
$where = array();
$where["patient_id"] = "= '$patient->_id'";
if (CAppUI::conf("dPpatients CPatient multi_group") == "hidden") {
  $where["sejour.group_id"] = "= '$group_id'";
}
$order = "entree ASC";
$patient->_ref_sejours = $sejour->loadList($where, $order);

$date_consult = $consult->_ref_plageconsult->date;
$ops_sans_dossier_anesth = array();
// Chargement de ses séjours
foreach ($patient->_ref_sejours as $_key => $_sejour) {
  if ($date_consult > $_sejour->entree_prevue && $date_consult > $_sejour->sortie_prevue) {
    unset($patient->_ref_sejours[$_sejour->_id]);
    continue;
  }
  $_sejour->loadRefsOperations();
  $_sejour->loadRefsFwd();
  foreach ($_sejour->_ref_operations as $_key_op => $_operation) {
    $_operation->loadRefsFwd();
    $_operation->_ref_chir->loadRefFunction()->loadRefGroup();
    $day = CMbDT::daysRelative($consult->_ref_plageconsult->date, $_operation->_ref_plageop->date);
    if (!$_operation->_ref_consult_anesth->_id && $day >= 0) {
      $ops_sans_dossier_anesth[] = $_operation;
    }
    else {
      unset($_sejour->_ref_operations[$_operation->_id]);
      if (!count($_sejour->_ref_operations)) {
        unset($patient->_ref_sejours[$_sejour->_id]);
      }
    }
  }
}

$consult->loadRefPraticien();
$consult->loadRefsDossiersAnesth();
$consult->loadRefFirstDossierAnesth();

$tab_op = array();
foreach ($consult->_refs_dossiers_anesth as $consultation_anesth) {
  $consultation_anesth->loadRelPatient();
  $consult->_ref_patient->loadRefConstantesMedicales(null, array("poids"), $consult);
  $consultation_anesth->_ref_consultation = $consult;

  $consultation_anesth->loadRefOperation()->loadRefSejour();
  $consultation_anesth->_ref_operation->_ref_sejour->loadRefDossierMedical();
}

$dossier_medical_patient = $patient->loadRefDossierMedical();
$dossier_medical_patient->loadRefsAntecedents();
$dossier_medical_patient->loadRefsTraitements();
$dossier_medical_patient->loadRefPrescription();

$user = new CMediusers();
$user->load($consult->_ref_praticien->_id);
$listChirs   = $user->loadPraticiens(PERM_READ);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("consult" , $consult);
$smarty->assign("patient" , $patient);
$smarty->assign("dm_patient"              , $dossier_medical_patient);
$smarty->assign("ops_sans_dossier_anesth" , $ops_sans_dossier_anesth);
$smarty->assign("first_operation"         , reset($ops_sans_dossier_anesth));
$smarty->assign("consult_anesth"          , $consult_anesth);
$smarty->assign("listChirs"               , $listChirs);

$smarty->display("inc_consult_anesth/vw_gestion_da.tpl");

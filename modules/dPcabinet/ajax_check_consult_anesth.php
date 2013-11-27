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

CCanDo::checkEdit();
$consult_id = CValue::getOrSession("consult_id");

$consult = new CConsultation();
$consult->load($consult_id);
$consult->loadRefPatient()->loadRefConstantesMedicales(null, array("poids"));
$consult->loadRefsDossiersAnesth();

$tab_op = array();
foreach ($consult->_refs_dossiers_anesth as $consult_anesth) {
  $consult_anesth->loadRefSejour()->loadRefsOperations();
  $consult_anesth->_ref_sejour->loadRefDossierMedical();

  foreach ($consult_anesth->_ref_sejour->_ref_operations as $interv) {
    $interv->loadRefPlageOp();
    $interv->loadRefsConsultAnesth();
    $tab_op[] = $interv->_id;
  }
  if (!$consult_anesth->sejour_id) {
    $tab_op[] = 0;
  }
}

if (!count($tab_op)) {
  $tab_op[] = 0;
}

$dossier_medical_patient = $consult->_ref_patient->loadRefDossierMedical();
$dossier_medical_patient->loadRefsAntecedents();
$dossier_medical_patient->loadRefsTraitements();
$dossier_medical_patient->loadRefPrescription();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("consult"     , $consult);
$smarty->assign("constantes"  , $consult->_ref_patient->_ref_constantes_medicales);
$smarty->assign("dm_patient"  , $dossier_medical_patient);
$smarty->assign("tab_op"      , $tab_op);

$smarty->display("inc_check_consult_anesth.tpl");
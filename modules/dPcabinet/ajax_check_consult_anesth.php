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
$consult->loadRefConsultAnesth();
$consult->_ref_sejour = $consult->_ref_consult_anesth->loadRefSejour();
$consult->_ref_sejour->loadRefsOperations();

$tab_op = array();
foreach ($consult->_ref_sejour->_ref_operations as $interv) {
  $interv->loadRefPlageOp();
  $interv->loadRefsConsultAnesth();
  $tab_op[] = $interv->_id;
}
if (!count($tab_op)) {
  $tab_op[] = 0;
}

$dossier_medical_patient = $consult->_ref_patient->loadRefDossierMedical();
$dossier_medical_patient->loadRefsAntecedents();
$dossier_medical_patient->loadRefsTraitements();
$dossier_medical_patient->loadRefPrescription();

$dossier_medical_sejour = $consult->_ref_sejour->loadRefDossierMedical();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("consult"     , $consult);
$smarty->assign("constantes"  , $consult->_ref_patient->_ref_constantes_medicales);
$smarty->assign("dm_patient"  , $dossier_medical_patient);
$smarty->assign("dm_sejour"   , $dossier_medical_sejour);
$smarty->assign("tab_op"      , $tab_op);

$smarty->display("inc_check_consult_anesth.tpl");
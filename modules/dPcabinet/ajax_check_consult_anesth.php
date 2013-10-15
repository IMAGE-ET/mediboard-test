<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CCanDo::checkEdit();
$consult_id = CValue::getOrSession("consult_id");

$consult = new CConsultation();
$consult->load($consult_id);

$consult->loadRefPatient()->loadRefConstantesMedicales(null, array("poids"));
$consult_anesth = $consult->loadRefConsultAnesth();
$consult_anesth->loadRefOperation();

$dossier_medical_patient = $consult->_ref_patient->loadRefDossierMedical();
$dossier_medical_patient->loadRefsAntecedents();
$dossier_medical_patient->loadRefsTraitements();
$dossier_medical_patient->loadRefPrescription();

$dossier_medical_sejour = $consult_anesth->_ref_sejour->loadRefDossierMedical();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("consultation"    , $consult);
$smarty->assign("constantes"      , $consult->_ref_patient->_ref_constantes_medicales);
$smarty->assign("consult_anesth"  , $consult_anesth);
$smarty->assign("dm_patient" , $dossier_medical_patient);
$smarty->assign("dm_sejour"  , $dossier_medical_sejour);

$smarty->display("inc_check_consult_anesth.tpl");

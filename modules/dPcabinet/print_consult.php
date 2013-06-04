<?php
/**
 * $Id: $
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: $
 */

CCanDo::checkRead();

$today = date("d/m/Y");

$consult_id = CValue::get("consult_id", 0);

//Création de la consultation
$consult = new CConsultation();
$consult->load($consult_id);
$consult->loadRefPatient();
$consult->loadRefSejour();
$consult->loadRefPraticien();
$consult->loadRefsBack();
$consult->loadRefsDocs();
$consult->loadComplete();

$sejour = $consult->_ref_sejour;
$sejour->loadRefsConsultations();
$sejour->loadListConstantesMedicales();
$sejour->loadNDA();
$sejour->loadSuiviMedical();
$sejour->loadExtDiagnostics();
$patient = $consult->_ref_patient;

$patient->loadIPP();
$patient->loadRefDossierMedical();

$dossier_medical = $patient->_ref_dossier_medical;
$dossier_medical->countAntecedents();
$dossier_medical->countTraitements();

$dossier_medical->loadRefPrescription();
$dossier_medical->loadRefsTraitements();

$constantes_medicales_grid = CConstantesMedicales::buildGrid($sejour->_list_constantes_medicales, false);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient", $patient);
$smarty->assign("sejour" , $sejour);
$smarty->assign("dossier_medical", $dossier_medical);
$smarty->assign("consult", $consult);
$smarty->assign("constantes_medicales_grid", $constantes_medicales_grid);
$smarty->assign("today", $today  );

$smarty->display("print_consult.tpl");

?>
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

$consult_id = CValue::get("consult_id");
$patient_id = CValue::get("patient_id");

$consult_anesth = new CConsultAnesth;
$consult_anesth->load($consult_id);

$patient = new CPatient();
$patient->load($patient_id);

$dossier_medical = $patient->loadRefDossierMedical();

$consult_anesth->apfel_femme      = 0;
$consult_anesth->apfel_atcd_nvp   = 0;
$consult_anesth->apfel_morphine   = 0;
$consult_anesth->apfel_non_fumeur = 1;

// Femme
if ($patient->sexe === "f") {
  $consult_anesth->apfel_femme = 1;
}

// Non fumeur
if (count($dossier_medical->_codes_cim)) {
  $is_fumeur = 0;
  
  foreach ($dossier_medical->_codes_cim as $_code_cim) {
    if (preg_match("/^(F17|T652|Z720|Z864|Z587)/", $_code_cim)) {
      $is_fumeur = 1;
      break;
    }
  }
  
  if ($is_fumeur) {
    $consult_anesth->apfel_non_fumeur = 0;
  }
}

$smarty = new CSmartyDP;

$smarty->assign("consult_anesth", $consult_anesth);

$smarty->display("inc_guess_score_apfel.tpl");

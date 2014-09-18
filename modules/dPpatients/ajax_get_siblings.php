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

$patient_id      = CValue::get("patient_id");
$nom             = CValue::get("nom");
$nom_jeune_fille = CValue::get("nom_jeune_fille");
$prenom          = CValue::get("prenom");
$prenom_2        = CValue::get("prenom_2");
$prenom_3        = CValue::get("prenom_3");
$prenom_4        = CValue::get("prenom_4");
$naissance       = CValue::get("naissance", "0000-00-00");
$submit          = CValue::get("submit"   , 0);
$json_result     = CValue::get("json_result");

$similar     = true;

$old_patient = new CPatient();
$old_patient->load($patient_id);

if ($patient_id && $nom && $prenom) {
  $similar = $old_patient->checkSimilar($nom, $prenom);
}

$patientMatch             = new CPatient();
$patientMatch->patient_id = $patient_id;

if (CAppUI::conf('dPpatients CPatient function_distinct')) {
  $function_id = CMediusers::get()->function_id;
  $patientMatch->function_id = $function_id;
}

$patientMatch->nom             = $nom;
$patientMatch->nom_jeune_fille = $nom_jeune_fille;
$patientMatch->prenom          = $prenom;
$patientMatch->prenom_2        = $prenom_2;
$patientMatch->prenom_3        = $prenom_3;
$patientMatch->prenom_4        = $prenom_4;
$patientMatch->naissance       = $naissance;

$doubloon = implode("|", $patientMatch->getDoubloonIds());

$siblings = null;
if (!$doubloon) {
  $siblings = $patientMatch->getSiblings();
}

//Test pour l'ouverture de la modal
if ($json_result) {
  $result = false;
  if (!$similar || $siblings || ($doubloon && $old_patient->status != "DPOT")) {
    $result = true;
  }

  CApp::json($result);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("similar"      , $similar);
$smarty->assign("old_patient"  , $old_patient);
$smarty->assign("doubloon"     , $doubloon);
$smarty->assign("siblings"     , $siblings);
$smarty->assign("patient_match", $patientMatch);
$smarty->assign("submit"       , $submit);

$smarty->display("inc_get_siblings.tpl");

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

$patient_id      = CValue::get("patient_id"     , null);
$nom             = CValue::get("nom"            , null);
$nom_jeune_fille = CValue::get("nom_jeune_fille", null);
$prenom          = CValue::get("prenom"         , null);
$prenom_2        = CValue::get("prenom_2"       , null);
$prenom_3        = CValue::get("prenom_3"       , null);
$prenom_4        = CValue::get("prenom_4"       , null);
$naissance       = CValue::get("naissance"      , "0000-00-00");
$submit          = CValue::get("submit"         , 0);

$old_patient = null;
$similar     = true;

if ($patient_id) {
  $old_patient = new CPatient();
  $old_patient->load($patient_id);
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

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("similar"      , $similar);
$smarty->assign("old_patient"  , $old_patient);
$smarty->assign("doubloon"     , $doubloon);
$smarty->assign("siblings"     , $siblings);
$smarty->assign("patient_match", $patientMatch);
$smarty->assign("submit"       , $submit);

$smarty->display("inc_get_siblings.tpl");

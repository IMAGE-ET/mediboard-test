<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PMSI
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$group = CGroups::loadCurrent();

$sejour  = new CSejour();
$patient = new CPatient();

// Si on passe un numéro de dossier,
// on charge le patient et le séjour correspondant
if ($NDA = CValue::get("NDA")) {
  $sejour->loadFromNDA($NDA);
  if ($sejour->_id && $sejour->group_id == $group->_id) {
    $patient = $sejour->loadRefPatient();
    CValue::setSession("sejour_id", $sejour->_id);
    CValue::setSession("patient_id", $patient->_id);
  }
}

// Si on n'a pas récupéré de patient via le numero de dossier,
// on charge le dossier en session
if (!$patient->_id) {
  $patient->load(CValue::getOrSession("patient_id"));
  $sejour->load(CValue::getOrSession("sejour_id"));
  // Si le séjour a un patient différent de celui selectionné,
  // on le déselectionne
  if ($patient->_id && $sejour->_id && $sejour->patient_id != $patient->_id) {
    CValue::setSession("sejour_id");
    $sejour = new CSejour();
  }
  // Si on a un séjour mais pas de patient,
  // on utilise le patient du séjour
  if ($sejour->_id && !$patient->_id) {
    $patient->load($sejour->patient_id);
    CValue::setSession("patient_id", $patient->_id);
  }
}

// Si on a un patient,
// on charge ses références
if ($patient->_id) {
  $patient->loadRefsSejours();
  $patient->loadRefsConsultations();
  // Si on n'a pas de séjour,
  // on prend le premier de la liste des séjours du patient
  if (!$sejour->_id && count($patient->_ref_sejours)) {
    $sejour = reset($patient->_ref_sejours);
  }
}

// Compteur par volets des items
$sejour->loadRefsDocItems();
$nbActes = 0;
$nbDiag = $sejour->DP ? 1 : 0;
$sejour->countActes();
$nbActes += $sejour->_count_actes;

foreach ($sejour->loadRefsOperations() as $_op) {
  $_op->countActes();
  $nbActes += $_op->_count_actes;
}

foreach ($sejour->loadRefsConsultations() as $_consult) {
  $_consult->countActes();
  $nbActes += $_consult->_count_actes;
}

$nbDiag += $sejour->DR ? 1 : 0;
$nbDiag += count($sejour->loadDiagnosticsAssocies());

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient" , $patient);
$smarty->assign("sejour"  , $sejour);
$smarty->assign("nbActes" , $nbActes);
$smarty->assign("nbDiag"  , $nbDiag);

$smarty->display("vw_dossier_pmsi.tpl");
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

CCanDo::checkEdit();

$patient_id = CValue::get("patient_id");
$type       = CValue::get("type");
$antecedent_id = CValue::get("antecedent_id");

$patient = new CPatient();
$patient->load($patient_id);

$antecedents = array();

if ($type) {
  $dossier_medical = $patient->loadRefDossierMedical();
  /** @var CAntecedent[] $antecedents */
  $antecedents = $dossier_medical->loadRefsAntecedentsOfType($type);

  foreach ($antecedents as $_antecedent) {
    $_antecedent->loadFirstLog();
  }
}

$antecedent = new CAntecedent();
$antecedent->load($antecedent_id);

$smarty = new CSmartyDP();

$smarty->assign("patient"    , $patient);
$smarty->assign("antecedents", $antecedents);
$smarty->assign("is_anesth"  , CAppUI::$user->isAnesth());
$smarty->assign("antecedent" , $antecedent);
$smarty->assign("type"       , $type);

$smarty->display("inc_edit_antecedents.tpl");

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

$patient_id = CValue::get("patient_id");
$type       = CValue::get("type");
$antecedent_id = CValue::get("antecedent_id");

$patient = new CPatient;
$patient->load($patient_id);

$dossier_medical = $patient->loadRefDossierMedical();
$dossier_medical->loadRefsAntecedents();

$antecedents = $dossier_medical->_all_antecedents;

foreach ($antecedents as &$_antecedent) {
  $_antecedent->loadFirstLog();
}

if ($type) {
  $antecedents = $dossier_medical->_ref_antecedents_by_type[$type];
}

$antecedent = new CAntecedent;
$antecedent->load($antecedent_id);

$smarty = new CSmartyDP;

$smarty->assign("patient"    , $patient);
$smarty->assign("antecedents", $antecedents);
$smarty->assign("is_anesth"  , CAppUI::$user->isAnesth());
$smarty->assign("antecedent" , $antecedent);
$smarty->assign("type"       , $type);

$smarty->display("inc_edit_antecedents.tpl");

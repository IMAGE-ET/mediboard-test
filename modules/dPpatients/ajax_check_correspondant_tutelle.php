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
$tutelle = CValue::get("tutelle");

$has_tutelle = 0;

if ($patient_id) {
  $patient = new CPatient();
  $patient->load($patient_id);
  
  $correspondants = $patient->loadRefsCorrespondantsPatient();
  
  foreach ($correspondants as $_correspondant) {
    if ($_correspondant->parente == "tuteur") {
      $has_tutelle = 1;
      break;
    }
  }
}

$smarty = new CSmartyDP;

$smarty->assign("has_tutelle", $has_tutelle);
$smarty->assign("tutelle"    , $tutelle);

$smarty->display("inc_check_correspondant_tutelle.tpl");

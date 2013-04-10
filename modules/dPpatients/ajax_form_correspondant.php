<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPpatients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

$correspondant_id = CValue::get("correspondant_id");
$patient_id       = CValue::get("patient_id");
$duplicate        = CValue::get("duplicate");

$patient = new CPatient();
$patient->load($patient_id);

$correspondant             = new CCorrespondantPatient;
$correspondant->patient_id = $patient_id;
$correspondant->_duplicate = $duplicate;
$correspondant->updatePlainFields();

if ($correspondant_id) {
  $correspondant->load($correspondant_id);
  $patient->load($correspondant->patient_id);
}

$patient->loadRefsCorrespondantsPatient();

$smarty = new CSmartyDP;
$smarty->assign("correspondant" , $correspondant);
$smarty->assign("patient"       , $patient);
$smarty->display("inc_form_correspondant.tpl");
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

$consultation_id = CValue::get("consultation_id");

$consultation = new CConsultation;
$consultation->load($consultation_id);

$patient = $consultation->loadRefPatient();
$patient->loadRefsCorrespondants();

$smarty = new CSmartyDP;

$smarty->assign("consult", $consultation);
$smarty->assign("patient", $patient);

$smarty->display("inc_list_patient_medecins.tpl");

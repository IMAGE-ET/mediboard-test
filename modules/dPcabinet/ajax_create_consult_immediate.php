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

$patient_id   = CValue::get("patient_id");
$sejour_id    = CValue::get("sejour_id");
$operation_id = CValue::get("operation_id");

$patient = new CPatient();
$patient->load($patient_id);

$consult = new CConsultation();
$consult->_datetime = CMbDT::dateTime();

$praticiens = CConsultation::loadPraticiens(PERM_EDIT);

$smarty = new CSmartyDP;
$smarty->assign("patient"     , $patient);
$smarty->assign("consult"     , $consult);
$smarty->assign("praticiens"  , $praticiens);
$smarty->assign("sejour_id"   , $sejour_id);
$smarty->assign("operation_id", $operation_id);
$smarty->assign("ufs"         , CUniteFonctionnelle::getUFs());
$smarty->display("inc_consult_immediate.tpl");
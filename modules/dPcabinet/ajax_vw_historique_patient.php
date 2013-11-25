<?php 

/**
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$patient_id = CValue::get("patient_id");
$type       = CValue::get("type", "sejour");

$patient = new CPatient();
$patient->load($patient_id);

switch ($type) {
  case "sejour":
    $patient->loadRefsSejours();
    // Chargement de ses séjours
    foreach ($patient->_ref_sejours as $_key => $_sejour) {
      $_sejour->loadRefsOperations();
      foreach ($_sejour->_ref_operations as $_key_op => $_operation) {
        $_operation->loadRefsFwd();
        $_operation->_ref_chir->loadRefFunction()->loadRefGroup();
      }
      $_sejour->loadRefsFwd();
    }
    break;
  case "consultation":
    $patient->loadRefsConsultations();
    foreach ($patient->_ref_consultations as $_consultation) {
      $_consultation->loadRefsFwd();
      $_consultation->_ref_chir->loadRefFunction()->loadRefGroup();
    }
}

$smarty = new CSmartyDP();

$smarty->assign("patient"  , $patient);
$smarty->assign("type"     , $type);

$smarty->display("inc_vw_historique_patient.tpl");
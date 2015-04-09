<?php 

/**
 * $Id$
 *  
 * @category Dossier patient
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$patient_id   = CView::get("patient_id", "num pos");

CView::checkin();

$patient = new CPatient();
$patient->load($patient_id);

$patient->_ref_operations = array();

foreach ($patient->loadRefsSejours() as $_sejour) {
  $_sejour->loadRefPraticien()->loadRefFunction();
  $patient->_ref_operations = array_merge($patient->_ref_operations, $_sejour->loadRefsOperations());
}

/** @var COperation $_operation */
foreach ($patient->_ref_operations as $_operation) {
  $_operation->loadRefPlageOp();
  $_operation->loadRefChir()->loadRefFunction();
}


foreach ($patient->loadRefsConsultations() as $_consult) {
  $_consult->loadRefPlageConsult();
  $_consult->_ref_chir->loadRefFunction();
}

$colspan = 3;
if (!count($patient->_ref_sejours)) {
  $colspan--;
}
if (!count($patient->_ref_operations)) {
  $colspan--;
}
if (!count($patient->_ref_consultations)) {
  $colspan--;
}

$smarty = new CSmartyDP();

$smarty->assign("patient", $patient);
$smarty->assign("colspan", $colspan);

$smarty->display("inc_context_doc.tpl");
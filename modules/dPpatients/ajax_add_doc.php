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
$context_guid = CView::get("context_guid", "str");

CView::checkin();

$patient = new CPatient();
$patient->load($patient_id);

$curr_user = CMediusers::get();

// Le contexte par défaut est le patient
$context = $patient;
$context->_praticien_id = $curr_user->_id;

if ($context_guid) {
  $context = CMbObject::loadFromGuid($context_guid);
}

switch ($context->_class) {
  case "CConsultation":
    $context->loadRefPlageConsult();
    $context->_ref_chir->loadRefFunction();
    break;
  case "CSejour":
    $context->loadRefPraticien()->loadRefFunction();
    break;
  case "COperation":
    $context->loadRefPlageOp();
    $context->loadRefChir()->loadRefFunction();
    break;
  default:
}

$smarty = new CSmartyDP();

$smarty->assign("patient"  , $patient);
$smarty->assign("context"  , $context);
$smarty->assign("curr_user", $curr_user);

$smarty->display("inc_add_doc.tpl");
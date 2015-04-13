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

// Chargement des formulaires
$group_id = CGroups::loadCurrent()->_id;
$where = array(
  "group_id = '$group_id' OR group_id IS NULL",
);
$ex_class = new CExClass();
CExClass::$_list_cache = $ex_class->loadList($where, "name");

// Loading the events
$ex_classes_creation = array();
$ex_class_events = array();
$_ex_class_creation = array();

foreach (CExClass::$_list_cache as $_ex_class_id => $_ex_class) {
  if (!$_ex_class->conditional) {
    $_ex_class_creation[] = $_ex_class_id;
  }
}

$where = array(
  "ex_class_event.ex_class_id" => CSQLDataSource::get("std")->prepareIn($_ex_class_creation),
  "ex_class_event.disabled"    => "= '0'",
);

/** @var CExClassEvent[] $ex_class_events_by_ref */
$ex_class_event = new CExClassEvent();
$ex_class_events_by_ref = $ex_class_event->loadList($where);
CStoredObject::massLoadBackRefs($ex_class_events_by_ref, "constraints");

foreach ($ex_class_events_by_ref as $_ex_class_event) {
  $_key = "$_ex_class_event->host_class/$_ex_class_event->ex_class_id";

  /** @var CExClassEvent[] $_ex_class_events */
  if (!array_key_exists($_key, $ex_class_events)) {
    $ex_class_events[$_key] = array();
  }

  $ex_class_events[$_key][] = $_ex_class_event;
}

foreach ($_ex_class_creation as $_ex_class_id) {
  if (!isset($ex_class_events["$context->_class/$_ex_class_id"])) {
    continue;
  }

  $_ex_class_events = $ex_class_events["$context->_class/$_ex_class_id"];

  foreach ($_ex_class_events as $_id => $_ex_class_event) {
    if (!$_ex_class_event->checkConstraints($context)) {
      unset($_ex_class_events[$_id]);
    }
  }

  if (count($_ex_class_events)) {
    $ex_classes_creation[$_ex_class_id] = $_ex_class_events;
  }
}

$smarty = new CSmartyDP();

$smarty->assign("patient"            , $patient);
$smarty->assign("context"            , $context);
$smarty->assign("curr_user"          , $curr_user);
$smarty->assign("ex_classes"         , CExClass::$_list_cache);
$smarty->assign("ex_classes_creation", $ex_classes_creation);

$smarty->display("inc_add_doc.tpl");
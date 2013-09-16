<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$object_guid   = CValue::get("object_guid");
$axis_id       = CValue::get("axis_id");

/** @var COperation|CSejour $object */
$object = CStoredObject::loadFromGuid($object_guid);

$result_set = new CObservationResultSet();
$result_set->context_class = $object->_class;
$result_set->context_id    = $object->_id;
$result_set->datetime = CMbDT::dateTime();
$result_set->patient_id = $object->loadRelPatient()->_id;

$axis = new CSupervisionGraphAxis();
$axis->load($axis_id);
$series = $axis->loadRefsSeries();

$results = array();
foreach ($series as $_serie) {
  $_result = new CObservationResult();
  $_result->value_type_id = $_serie->value_type_id;
  $_result->unit_id       = $_serie->value_unit_id ? $_serie->value_unit_id : "";
  $_result->loadRefValueUnit();
  $_result->loadRefValueType();
  $_result->_serie_title = $_serie->title;

  //$_result->updateFormFields();

  $results[] = $_result;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object",     $object);
$smarty->assign("result_set", $result_set);
$smarty->assign("results",    $results);
$smarty->assign("axis",       $axis);

$smarty->display("inc_edit_observation_result_set.tpl");

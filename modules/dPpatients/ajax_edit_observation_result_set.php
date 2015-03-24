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
$pack_id       = CValue::get("pack_id");
$result_set_id = CValue::get("result_set_id");
$result_id     = CValue::get("result_id");

$result_set = new CObservationResultSet();
if ($result_set_id) {
  $result_set->load($result_set_id);

  $object = $result_set->loadRefContext();
}
else {
  /** @var COperation|CSejour $object */
  $object = CStoredObject::loadFromGuid($object_guid);

  $result_set->context_class = $object->_class;
  $result_set->context_id    = $object->_id;
  $result_set->datetime = CMbDT::dateTime();
  $result_set->patient_id = $object->loadRelPatient()->_id;
}

list($results/*, $times*/) = CObservationResultSet::getResultsFor($object);

$pack = new CSupervisionGraphPack();
$pack->load($pack_id);
$links = $pack->loadRefsGraphLinks();

foreach ($links as $_link) {
  $_graph = $_link->loadRefGraph();

  if ($_graph instanceof CSupervisionGraph) {
    $axes = $_graph->loadRefsAxes();

    foreach ($axes as $_axis) {
      $series = $_axis->loadRefsSeries();
      $_axis->loadRefsLabels();

      foreach ($series as $_serie) {
        $_result = new CObservationResult();
        $_result->value_type_id = $_serie->value_type_id;
        $_result->unit_id       = $_serie->value_unit_id ? $_serie->value_unit_id : null;

        if ($result_set->_id) {
          $_result->observation_result_set_id = $result_set->_id;
          $_result->loadMatchingObject();
        }

        $_result->loadRefValueUnit();
        $_result->loadRefValueType();
        $_result->_serie_title = $_serie->title ? $_serie->title : $_axis->_view;

        $_serie->_result = $_result;
      }
    }
  }
  elseif ($_graph instanceof CSupervisionTimedData) {
    $_result = new CObservationResult();
    $_result->value_type_id = $_graph->value_type_id;

    if ($result_set->_id) {
      $_result->observation_result_set_id = $result_set->_id;
      $_result->loadMatchingObject();
    }

    $_result->loadRefValueType();

    $_graph->_result = $_result;
  }
  elseif ($_graph instanceof CSupervisionTimedPicture) {
    $_result = new CObservationResult();
    $_result->value_type_id = $_graph->value_type_id;

    if ($result_set->_id) {
      $_result->observation_result_set_id = $result_set->_id;
      $_result->loadMatchingObject();
    }

    $_result->loadRefValueType();

    $_graph->loadRefsFiles();
    $_graph->_result = $_result;
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object",     $object);
$smarty->assign("result_set", $result_set);
$smarty->assign("pack",       $pack);
$smarty->assign("result_id",  $result_id);
$smarty->assign("results", $results);

$smarty->display("inc_edit_observation_result_set.tpl");

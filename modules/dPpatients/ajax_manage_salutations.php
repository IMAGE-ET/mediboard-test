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

$object_class = CValue::get('object_class');
$object_id    = CValue::get('object_id');

if (!$object_class || !$object_id) {
  CAppUI::stepAjax('common-error-Missing parameter', UI_MSG_ERROR);
}

if (!$object_class || !in_array('CStoredObject', class_parents($object_class))) {
  CAppUI::stepAjax('common-error-Invalid class name', UI_MSG_ERROR);
}

$object = CStoredObject::loadFromGuid("{$object_class}-{$object_id}");
if (!$object || !$object->_id) {
  CAppUI::stepAjax('common-error-Invalid object', UI_MSG_ERROR);
}

/** @var CSalutation[] $salutations */
$salutations = CSalutation::loadAllSalutations($object->_class, $object->_id);

CStoredObject::massLoadFwdRef($salutations, 'owner_id');
CStoredObject::massLoadFwdRef($salutations, 'object_id');

$functions = array();
$salutations_by_function = array();
/** @var CSalutation $_salutation */
foreach ($salutations as $_salutation) {
  $_salutation->loadRefOwner();

  if (!isset($salutations_by_function[$_salutation->_ref_owner->function_id])) {
    $salutations_by_function[$_salutation->_ref_owner->function_id] = array();
    $functions[$_salutation->_ref_owner->function_id] = $_salutation->_ref_owner->loadRefFunction();
  }

  $salutations_by_function[$_salutation->_ref_owner->function_id][] = $_salutation;
}
$salutations = $salutations_by_function;

ksort($salutations);

$smarty = new CSmartyDP();
$smarty->assign("salutations", $salutations);
$smarty->assign("functions", $functions);
$smarty->assign("object", $object);
$smarty->display("inc_manage_salutations.tpl");

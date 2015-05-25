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

$smarty = new CSmartyDP();
$smarty->assign("object", $object);
$smarty->display("vw_manage_salutations.tpl");

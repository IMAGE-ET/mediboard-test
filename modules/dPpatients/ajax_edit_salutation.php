<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$salutation_id = CValue::get('salutation_id');
$object_class  = CValue::get('object_class');
$object_id     = CValue::get('object_id');

$salutation = new CSalutation();
$salutation->load($salutation_id);

if (!$salutation_id) {
  if (!$object_class || !in_array('CStoredObject', class_parents($object_class))) {
    CAppUI::stepAjax('common-error-Invalid class name', UI_MSG_ERROR);
  }

  $object = CStoredObject::loadFromGuid("{$object_class}-{$object_id}");

  if (!$object || !$object->_id) {
    CAppUI::stepAjax('common-error-Invalid object', UI_MSG_ERROR);
  }

  $salutation->owner_id     = CMediusers::get()->_id;
  $salutation->object_class = $object_class;
  $salutation->object_id    = $object_id;
}

$salutation->loadRefOwner();
$salutation->loadTargetObject();

$users    = new CMediusers();
$users    = $users->loadListWithPerms(PERM_EDIT, array('actif' => "= '1'"));

$smarty = new CSmartyDP();
$smarty->assign("salutation", $salutation);
$smarty->assign("object_class", $object_class);
$smarty->display("vw_edit_salutation.tpl");

<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$object_guid = CValue::get("object_guid");
$values      = CValue::get("_v", array()); // pre-filled values

if (!$object_guid) {
  CAppUI::stepAjax("Un identifiant d'objet doit être fourni", UI_MSG_WARNING);
  return;
}

$object = CMbObject::loadFromGuid($object_guid);

if ($object && $object->_id) {
  global $can;
  $can->read = $object->canRead();
  $can->edit = $object->canEdit();
  $can->needsRead();
}

if (!$object->_id && !empty($values)) {
  foreach ($values as $_key => $_value) {
    $object->$_key = $_value;
  }
}

$template = $object->getTypedTemplate("edit");

$object->loadEditView();
$object->loadRefsTagItems();

$smarty = new CSmartyDP();
$smarty->assign("object", $object);
$smarty->display($template);

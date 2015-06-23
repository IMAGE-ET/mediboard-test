<?php
/**
 * Show alerts not handled
 *
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$object_guid = CValue::get("object_guid");
$level       = CValue::get("level");
$tag         = CValue::get("tag");

$object = CMbObject::loadFromGuid($object_guid);

if (!$object->_guid) {
  CApp::rip();
}

$object->loadAlertsNotHandled($level, $tag, null);
$object->canDo();

$alert_ids = CMbArray::pluck($object->_refs_alerts_not_handled, "_id");

$smarty = new CSmartyDP();

$smarty->assign("object"   , $object);
$smarty->assign("level"    , $level);
$smarty->assign("alert_ids", $alert_ids);

$smarty->display("inc_vw_alertes.tpl");

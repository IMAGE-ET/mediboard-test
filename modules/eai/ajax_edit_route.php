<?php 

/**
 * $Id$
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$route_id   = CValue::get("route_id");
$actor_guid = CValue::get("actor_guid");

$list_receiver = CApp::getChildClasses("CInteropReceiver", array(), true);
$list_sender   = CApp::getChildClasses("CInteropSender"  , array(), true);

if ($actor_guid) {
  $actor = CMbObject::loadFromGuid($actor_guid);
}

$route = new CEAIRoute();
$route->load($route_id);
if (!$route->_id && isset($actor)) {
  $route->sender_class = $actor->_class;
  $route->sender_id    = $actor->_id;
}

$route->loadRefReceiver();
$route->loadRefSender();

$smarty = new CSmartyDP();
$smarty->assign("route"        , $route);
$smarty->assign("list_receiver", $list_receiver);
$smarty->assign("list_sender"  , $list_sender);
$smarty->display("inc_edit_route.tpl");
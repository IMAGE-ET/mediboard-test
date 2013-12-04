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

$route_id = CValue::get("route_id");

$list_receiver = CApp::getChildClasses("CInteropReceiver", array(), true);
$list_sender   = CApp::getChildClasses("CInteropSender", array(), true);

$route = new CEAIRoute();
$route->load($route_id);
$route->loadRefReceiver();
$route->loadRefSender();

$smarty = new CSmartyDP();
$smarty->assign("route"        , $route);
$smarty->assign("list_receiver", $list_receiver);
$smarty->assign("list_sender"  , $list_sender);
$smarty->display("inc_edit_route.tpl");
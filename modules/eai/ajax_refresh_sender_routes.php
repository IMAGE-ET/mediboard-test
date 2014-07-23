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

$actor_guid = CValue::get("actor_guid");

/** @var CInteropSender $actor */
$sender = CMbObject::loadFromGuid($actor_guid);

$route               = new CEAIRoute();
$route->sender_class = $sender->_class;
$route->sender_id    = $sender->_id;
$routes = $route->loadMatchingList();

foreach ($routes as $_route) {
  /** @var CEAIRoute $_route */
  $_route->loadRefReceiver();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sender", $sender);
$smarty->assign("routes", $routes);

$smarty->display("inc_list_actor_routes.tpl");
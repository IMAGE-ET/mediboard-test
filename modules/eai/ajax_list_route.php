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

$route = new CEAIRoute();

$routes  = array();
$senders = array();

foreach ($route->loadList(null, "sender_id ASC") as $_route) {
  /** @var CEAIRoute $_route */
  $sender = $_route->loadRefSender();
  $_route->loadRefReceiver();

  $senders[$sender->_guid]  = $sender;

  $routes[$sender->_guid][] = $_route;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("routes" , $routes);
$smarty->assign("senders", $senders);

$smarty->display("inc_list_route.tpl");
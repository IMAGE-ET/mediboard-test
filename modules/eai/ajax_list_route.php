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

$route = new CEAIRoute();
$routes = $route->loadList(null, "sender_id ASC");
foreach ($routes as $_route) {
  /** @var CEAIRoute $_route */
  $_route->loadRefSender();
  $_route->loadRefReceiver();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("routes", $routes);

$smarty->display("inc_list_route.tpl");
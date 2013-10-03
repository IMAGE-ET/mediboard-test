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

$http_tunnel = new CHTTPTunnelObject();
$tunnels = $http_tunnel->loadList();

foreach ($tunnels as $_tunnel) {
  $_tunnel->checkStatus();
}

$smarty = new CSmartyDP();
$smarty->assign("tunnels", $tunnels);
$smarty->display("vw_tunnel_tools.tpl");
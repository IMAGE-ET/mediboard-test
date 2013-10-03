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

$action = CValue::get("action", null);
$id     = CValue::get("idTunnel", null);

$tunnel = new CHTTPTunnelObject();
$tunnel->load($id);

$http_client = new CHTTPClient($tunnel->address);
if ($tunnel->ca_file) {
  $http_client->setSSLPeer($tunnel->ca_file);
}

$result = "";
switch ($action) {
  case "restart":
    $http_client->setOption(CURLOPT_CUSTOMREQUEST, "CMD RESTART");
    $result = $http_client->executeRequest();
    break;
  case "stop":
    $http_client->setOption(CURLOPT_CUSTOMREQUEST, "CMD STOP");
    $result = $http_client->executeRequest();
    break;
  case "stat":
    $http_client->setOption(CURLOPT_CUSTOMREQUEST, "CMD STAT");
    $result = $http_client->executeRequest();
    $result = json_decode($result, true);
    break;
}

$smarty = new CSmartyDP();
$smarty->assign("result", $result);
$smarty->display("inc_tunnel_result.tpl");

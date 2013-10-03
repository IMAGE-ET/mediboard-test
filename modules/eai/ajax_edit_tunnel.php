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

$id_tunnel = CValue::get("tunnel_id");

$http_tunnel = new CHTTPTunnelObject();
$http_tunnel->load($id_tunnel);

$smarty = new CSmartyDP();
$smarty->assign("tunnel", $http_tunnel);
$smarty->display("inc_edit_tunnel.tpl");
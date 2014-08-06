<?php 

/**
 * $Id$
 *  
 * @category Outils
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$session_id = CValue::get("session_id");
$timeout    = CValue::get("timeout", 30);

if (!$session_id) {
  global $rootName;
  $session_name = preg_replace("/[^a-z0-9]/i", "", $rootName);
  $session_id = CValue::cookie($session_name);
}

$ip_server = $_SERVER["SERVER_ADDR"];

$smarty = new CSmartyDP();

$smarty->assign("session_id", $session_id);
$smarty->assign("timeout"   , $timeout);
$smarty->assign("ip_server" , $ip_server);

$smarty->display("routage.tpl");
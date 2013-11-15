<?php 

/**
 * $Id$
 *  
 * @category system
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$get["m"] = "system";
$get["a"] = "httpreq_do_empty_shared_memory";
$get["ajax"] = "1";

$result_send = CApp::multipleServerCall($get, null);

$smarty = new CSmartyDP();
$smarty->assign("result_send", $result_send);
$smarty->display("inc_do_empty_shared_all_servers.tpl");
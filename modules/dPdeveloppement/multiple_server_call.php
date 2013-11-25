<?php 

/**
 * $Id$
 *  
 * @category dPdeveloppement
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */


$get_session  = CValue::getOrSession("multiple_server_call_get");
$post_session = CValue::getOrSession("multiple_server_call_post");

$smarty = new CSmartyDP();
$smarty->assign("get", $get_session);
$smarty->assign("post", $post_session);
$smarty->display("multiple_server_call.tpl");
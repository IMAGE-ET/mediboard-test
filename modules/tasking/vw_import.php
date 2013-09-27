<?php 

/**
 * $Id$
 *  
 * @category Tasking
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$user_id = CValue::post("user_id");

$user = null;
if ($user_id) {
  $user = new CMediusers();
  $user->load($user_id);
}

$smarty = new CSmartyDP();
$smarty->assign("user", $user);
$smarty->display("vw_import.tpl");
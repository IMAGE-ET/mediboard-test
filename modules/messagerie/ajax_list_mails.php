<?php /** $Id$ **/

/**
 * @package    Mediboard
 * @subpackage messagerie
 * @version    $Revision$
 * @author     SARL OpenXtrem
 */

CCanDo::checkRead();

$type = CValue::get("type","all");

$user = CMediusers::get();

$mail = new CUserMail();
$mail->user_id = $user->_id;
$order = "date_inbox DESC";

$mails = $mail->loadMatchingList($order);

foreach ($mails as $_mail) {
  $_mail->loadComplete();
  $_mail->loadRefsFwd();
}


$smarty = new CSmartyDP();
$smarty->assign("mails", $mails);
$smarty->assign("type", $type);
$smarty->display("inc_list_mails.tpl");
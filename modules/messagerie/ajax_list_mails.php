<?php /** $Id$ **/

/**
 * @package    Mediboard
 * @subpackage messagerie
 * @version    $Revision$
 * @author     SARL OpenXtrem
 */

CCanDo::checkRead();

$account = CValue::get("account");

$user = CMediusers::get();

$mail = new CUserMail();
$mail->account_id = $account;
$order = "date_inbox DESC";

$mails = $mail->loadMatchingList($order);

foreach ($mails as $_mail) {
  $_mail->loadComplete();
  $_mail->loadRefsFwd();
}


$smarty = new CSmartyDP();
$smarty->assign("mails", $mails);
$smarty->assign("account", $account);
$smarty->display("inc_list_mails.tpl");
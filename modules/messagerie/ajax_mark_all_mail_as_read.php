<?php /** $Id$ **/

/**
 * @package    Mediboard
 * @subpackage messagerie
 * @version    $Revision$
 * @author     SARL OpenXtrem
 */

CCanDo::checkRead();
CPop::checkImapLib();

$account = CValue::get("account");
$user = CMediusers::get();

//get account
$account_pop = new CSourcePOP();
$account_pop->load($account);

//get the list
$mail = new CUserMail();
$where = array();
$where[] = "date_read IS NULL AND account_id = '$account'";
$mails = $mail->loadList($where);

$pop = new CPop($account_pop);
$pop->open();
$count = 0;
foreach ($mails as $_mail) {
  if ($pop->setflag($_mail->uid, "\\Seen")) {
    $_mail->date_read = mbDateTime();
    if (!$msg = $_mail->store() ) {
      $count++;
    }
  }
}
$pop->close();

if ($count > 0) {
  CAppUI::stepAjax("CUserMail-markedAsRead", UI_MSG_OK, $count);
}
else {
  CAppUI::stepAjax("CUserMail-markedAsRead-none", UI_MSG_OK);
}
<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$user = CMediusers::get();
$mail_id = CValue::get("mail_id");

//pop init

//mail
$mail = new CUserMail();
$mail->load($mail_id);
$mail->loadRefsFwd();
$mail->checkHprim();//HprimMedecin
//$mail->checkApicrypt();//HprimMedecin

//pop account
$log_pop = new CSourcePOP();
$log_pop->load($mail->account_id);

//if not read email, send the seen flag to server
if (!$mail->date_read && !CAppUI::pref("markMailOnServerAsRead")) {
  $pop = new CPop($log_pop);
  $pop->open();
  $pop->setflag($mail->uid, "\\Seen");
  $pop->close();
}
$mail->date_read = CMbDT::dateTime();
$mail->store();

//get the CFile attachments
$nbAttachPicked = 0;
$nbAttach = count($mail->_attachments);
foreach ($mail->_attachments as $_att) {
  $_att->loadRefsFwd();
  if ($_att->_file->_id) {
    $nbAttachPicked++;
  }
}

$mail->checkInlineAttachments();

//apicrypt
if (stripos($mail->_text_plain->content, "****FIN****") !== false) {
  $mail->_is_apicrypt = 1;
}

$headers = preg_split("/(\r\n|\n)/", $mail->_text_plain->content);
//hprim
if ($mail->_is_apicrypt) {
  $mail->_text_plain->content = implode("\n", array_splice($headers, 13));
}

//Smarty
$smarty = new CSmartyDP();
$smarty->assign("mail", $mail);
$smarty->assign("nbAttachPicked", $nbAttachPicked);
$smarty->assign("nbAttachAll",  $nbAttach);
$smarty->assign("header", $headers);
$smarty->display("vw_open_external_email.tpl");
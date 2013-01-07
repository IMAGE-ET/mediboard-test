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

//pop account
$log_pop = new CSourcePOP();
$log_pop->name = "user-pop-".$user->_id;
$log_pop->loadMatchingObject();

//if not read email, send the seen flag to server
if (!$mail->date_read) {
  $pop = new CPop($log_pop);
  $pop->open();
  if ($pop->setflag($mail->uid, "\\Seen")) {
    $mail->date_read = $mail->date_read = mbDateTime();
    $mail->store();
  }
  $pop->close();
}

//get the CFile attachments
foreach ($mail->_attachments as $_att) {
  $_att->loadRefsFwd();
}

$mail->checkInlineAttachments();

//Smarty
$smarty = new CSmartyDP();
$smarty->assign("mail", $mail);
$smarty->display("ajax_open_external_email.tpl");
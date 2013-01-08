<?php /** $Id$ **/

/**
 * @package    Mediboard
 * @subpackage messagerie
 * @version    $Revision$
 * @author     SARL OpenXtrem
 */


CCanDo::checkRead();
$mail_id = CValue::get("mail_id", 0);
$pat_id = CValue::get("pat_id");

$pat = new CPatient();
$pat->load($pat_id);

if ($mail_id) {
  $mail = new CUserMail();
  $mail->load($mail_id);
  $mail->loadRefsFwd();

  //get the CFile attachments
  foreach ($mail->_attachments as $_att) {
    $_att->loadRefsFwd();
  }

  $mail->checkInlineAttachments();
}


//smarty
$smarty = new CSmartyDP();
$smarty->assign("mail", $mail);
$smarty->assign("pat", $pat);
$smarty->display("ajax_link_attachments.tpl");
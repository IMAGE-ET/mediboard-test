<?php /** $Id$ **/

/**
 * @package    Mediboard
 * @subpackage messagerie
 * @version    $Revision$
 * @author     SARL OpenXtrem
 */


CCanDo::checkRead();

$user = CMediusers::get();
$attachment_id = CValue::get("attachment_id", 0);

//connection log
$log_pop = new CSourcePOP();
$log_pop->name = "user-pop-".$user->_id;
$log_pop->loadMatchingObject();

//load attachment
$attachment = new CMailAttachments();
$attachment->_id = $attachment_id;
$attachment->loadMatchingObject();

//load email
$mail = new CUserMail();
$mail->_id = $attachment->mail_id;
$mail->loadMatchingObject();

//CFile
$file = new CFile();
$file->setObject($attachment);
$file->private = 0;
$file->author_id  = $user->_id;
$file->loadMatchingObject();


//create the file
if (!$file->_id) {
  $pop = new CPop($log_pop);
  $pop->open();

  $file_pop = $pop->decodeMail($attachment->encoding, $pop->openPart($mail->uid, $attachment->getpartDL()));
  $file->file_name  = $attachment->name;
  $file->file_type  = $attachment->getType($attachment->type, $attachment->subtype);
  $file->fillFields();
  $file->putContent($file_pop);
  if ($str = $file->store()) {
    CAppUI::setMsg($str, UI_MSG_ERROR);
  }
  else {
    CAppUI::setMsg("CMailAttachment-msg-attachmentsaved", UI_MSG_OK);
  }

  $pop->close();
}

echo CAppUI::getMsg();
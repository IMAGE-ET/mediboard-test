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
$attachment_id = CValue::get("attachment_id");

//load email
$mail = new CUserMail();
$mail->load($mail_id);

//connection log
$log_pop = new CSourcePOP();
$log_pop->_id = $mail->account_id;
$log_pop->loadMatchingObject();




if ($attachment_id != 0) { //je récupère LA pièce jointe
  //load attachment
  $attachment = new CMailAttachments();
  $attachment->load($attachment_id);


  //CFile
  $file = new CFile();
  $file->setObject($attachment);
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
      CAppUI::setMsg("CMailAttachments-msg-attachmentsaved", UI_MSG_OK);
    }

    $pop->close();
  }
}
else {  //je récupère TOUTES les pièces jointes
  $mail->loadRefsFwd();
  foreach ($mail->_attachments as $_att) {
    $file = new CFile();
    $file->setObject($_att);
    $file->author_id  = $user->_id;
    $file->loadMatchingObject();

    if (!$file->_id) {
      $pop = new CPop($log_pop);
      $pop->open();

      $file_pop = $pop->decodeMail($_att->encoding, $pop->openPart($mail->uid, $_att->getpartDL()));
      $file->file_name  = $_att->name;
      $file->file_type  = $_att->getType($_att->type, $_att->subtype);
      $file->fillFields();
      $file->putContent($file_pop);
      if ($str = $file->store()) {
        CAppUI::setMsg($str, UI_MSG_ERROR);
      }
      else {
        CAppUI::setMsg("CMailAttachments-msg-attachmentsaved", UI_MSG_OK);
      }

      $pop->close();
    }
  }
}

echo CAppUI::getMsg();
<?php /** $Id$ **/

/**
 * @package    Mediboard
 * @subpackage messagerie
 * @version    $Revision$
 * @author     SARL OpenXtrem
 */

CCanDo::checkRead();

$user = CMediusers::get();
$object_id = CValue::get("object_id");
$object_class = CValue::get("object_class");
$attach_list = CValue::get("attach_list");

if ($attach_list != "") {

  //load object
  $object = new $object_class;
  $object->load($object_id);

  $attachments = explode("-", $attach_list);
  foreach ($attachments as $_attachment) {
    $attachment = new CMailAttachments();
    $attachment->load($_attachment);
    $attachment->loadRefsFwd();

    if ($attachment->_file->_id) {
      //je lie
      $attachment->_file->setObject($object);
      if ($msg = $attachment->_file->store()) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
      }
      else {
        $attachment->file_id = $attachment->_file->_id;
        if (!$msg = $attachment->store()) {
          CAppUI::setMsg("CMailAttachment-msg-attachmentLinked-success", UI_MSG_OK);
        }
      }
    }
    else {
      //CFile does not exist

      //mail
      $mail = new CUserMail();
      $mail->load($attachment->mail_id);
      // pop
      $account = new CSourcePOP();
      $account->load($mail->account_id);

      $pop = new CPop($account);
      $pop->open();


      $file = new CFile();
      $file->setObject($object);
      $file->private = 0;
      $file->author_id  = CAppUI::$user->_id;

      $pop = new CPop($account);
      $pop->open();
      $file_pop = $pop->decodeMail($attachment->encoding, $pop->openPart($mail->uid, $attachment->getpartDL()));
      $pop->close();

      $file->file_name  = $attachment->name;
      $file->file_type  = $attachment->getType($attachment->type, $attachment->subtype);
      $file->fillFields();
      $file->putContent($file_pop);
      if ($str = $file->store()) {
        CAppUI::setMsg($str, UI_MSG_ERROR);
      }
      else {
        $attachment->file_id = $file->_id;
        $attachment->store();
      }
    }
  }
}
else {
  CAppUI::setMsg("CMailAttachment-msg-noAttachSelected", UI_MSG_ERROR);
}

echo CAppUI::getMsg();
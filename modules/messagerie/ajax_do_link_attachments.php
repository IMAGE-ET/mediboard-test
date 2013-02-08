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
$text_plain = CValue::get("text_plain_id");
$text_html = CValue::get("text_html_id");
$mail_id = CValue::get("mail_id");

//load object
$object = new $object_class;
$object->load($object_id);

$mail = new CUserMail();
$mail->load($mail_id);

if (!$object->_id) {
  CAppUI::setMsg("CUserMail-link-objectNull", UI_MSG_ERROR);
}

if ($attach_list == "") {
  CAppUI::setMsg("CMailAttachment-msg-no_object_to_attach", UI_MSG_ERROR);
  echo CAppUI::getMsg();
}

$attachments = explode("-", $attach_list);
foreach ($attachments as $_attachment) {
  $attachment = new CMailAttachments();
  $attachment->load($_attachment);
  $attachment->loadRefsFwd();

  //je lie
  if ($attachment->_file->_id) {
    $attachment->_file->setObject($object);
    if ($msg = $attachment->_file->store()) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
    }
    else {
      $attachment->file_id = $attachment->_file->_id;
      if ($msg = $attachment->store()) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
      }
      else {
        CAppUI::setMsg("CMailAttachment-msg-attachmentLinked-success", UI_MSG_OK);
      }
    }
  }
  //CFile does not exist
  else {
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


if ($text_html || $text_plain) {
  if ($text_html) {
    $text = new CContentHTML();
    $text->load($text_html);
  }
  else {
    $text = new CContentAny();
    $text->load($text_plain);
  }

  $file = new CFile();
  $file->setObject($object);
  $file->private = 0;
  $file->author_id = CAppUI::$user->_id;
  $file->file_name  = $mail->subject;
  $file->file_type = "text/html";
  $file->fillFields();
  $file->putContent($text->content);
  if ($str = $file->store()) {
    CAppUI::setMsg($str, UI_MSG_ERROR);
  }
  else {
    $mail->text_file_id = $file->_id;
    $mail->store();
    CAppUI::setMsg("CUserMail-content-attached", UI_MSG_OK);
  }
}
//if ($text_plain) {}

if (!$text_html && !$text_plain && $attach_list == "" ) {
  CAppUI::setMsg("CMailAttachment-msg-noAttachSelected", UI_MSG_ERROR);
}

echo CAppUI::getMsg();
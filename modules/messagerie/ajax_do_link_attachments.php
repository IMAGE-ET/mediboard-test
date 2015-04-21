<?php /** $Id$ **/

/**
 * @package    Mediboard
 * @subpackage messagerie
 * @version    $Revision$
 * @author     SARL OpenXtrem
 */

CCanDo::checkRead();
$user           = CMediusers::get();
$object_id      = CValue::get("object_id");
$object_class   = CValue::get("object_class");
$attach_list    = CValue::get("attach_list");
$text_plain     = CValue::get("text_plain_id");
$text_html      = CValue::get("text_html_id");
$rename_text    = CValue::get("rename_text");
$category_id    = CValue::get("category_id");
$mail_id        = CValue::get("mail_id");

//load object
/** @var CMbObject $object */
$object = new $object_class;
$object->load($object_id);

$mail = new CUserMail();
$mail->load($mail_id);

if (!$object->_id) {
  CAppUI::stepAjax("CUserMail-link-objectNull", UI_MSG_ERROR);
}

if (str_replace("-", "", $attach_list) == "" && !$text_plain && !$text_html) {
  CAppUI::stepAjax("CMailAttachments-msg-no_object_to_attach", UI_MSG_ERROR);
}

$attachments = trim($attach_list) ? explode("-", $attach_list) : array();
foreach ($attachments as $_attachment) {
  //no attachment value
  if (!$_attachment) {
    continue;
  }
  $attachment = new CMailAttachments();
  if ($_attachment != "") {
    $attachment->load($_attachment);
    $attachment->loadRefsFwd();

    //already linked = skip, no id, skip
    if ($attachment->file_id || !$attachment->_id) {
      continue;
    }
  }


  //linking
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
        CAppUI::stepAjax("CMailAttachments-msg-attachmentLinked-success", UI_MSG_OK);
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
      CAppUI::stepAjax($str, UI_MSG_ERROR);
    }
    else {
      $attachment->file_id = $file->_id;
      $attachment->store();
    }
  }
}


//text link
if ($text_html || $text_plain) {
  $content_type = "text/plain";
  if ($text_html) {
    $text = new CContentHTML();
    $text->load($text_html);
    $content_type = "text/html";
  }
  else {
    $text = new CContentAny();
    $text->load($text_plain);
  }

  $file = new CFile();
  $file->setObject($object);
  $file->author_id = CAppUI::$user->_id;
  $file->file_name = "sans_titre";
  $file->file_category_id = $category_id;
  if ($mail->subject) {
    $file->file_name = $mail->subject;
  }
  if ($rename_text) {
    $file->file_name = $rename_text;
  }
  $file->file_type = $content_type;
  $file->fillFields();
  $file->putContent($text->content);
  if ($str = $file->store()) {
    CAppUI::stepAjax($str, UI_MSG_ERROR);
  }
  else {
    $mail->text_file_id = $file->_id;
    $mail->store();
    CAppUI::stepAjax("CUserMail-content-attached", UI_MSG_OK);
  }
}

if (!$text_html && !$text_plain && $attach_list == "" ) {
  CAppUI::stepAjax("CMailAttachments-msg-noAttachSelected", UI_MSG_ERROR);
}
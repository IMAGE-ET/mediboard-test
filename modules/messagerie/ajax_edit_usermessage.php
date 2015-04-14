<?php 

/**
 * $Id$
 *  
 * @category Messagerie
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$user = CMediusers::get();
$to_id = CValue::get("to_id");
$answer_to_all = CValue::get('answer_to_all');
$in_reply_to = CValue::get("in_reply_to");
$message_id = CValue::getOrSession("usermessage_id");
$dest_message = CValue::get("usermessage_dest_id");
$subject = utf8_decode(CValue::get('subject'));

// classic case
$usermessage = new CUserMessage();
$usermessage->load($message_id);

if ($dest_message) {
  $dest = new CUserMessageDest();
  $dest->load($dest_message);
  $usermessage = $dest->loadRefMessage();
}

/** @var CUserMessageDest[] $destinataires */
// check if sent
$usermessage->_can_edit = true;
$usermessage->loadRefDestUser();
$destinataires = $usermessage->loadRefDests();
foreach ($destinataires as $_dest) {
  if ($_dest->datetime_sent) {
    $usermessage->_can_edit = false;
  }
  if ($_dest->to_user_id == $user->_id && $usermessage->_id && !$_dest->datetime_read) {
    $_dest->datetime_read = CMbDT::dateTime();
    $_dest->store();
  }
  $_dest->loadRefTo()->loadRefFunction();
  $_dest->loadRefFrom()->loadRefFunction();
}

// last check
if (!$usermessage->_id) {
  $usermessage->creator_id = $user->_id;
  if ($subject) {
    $usermessage->subject = $subject;
  }
  // in reply to
  if ($in_reply_to) {
    $temp_message = new CUserMessage();
    $temp_message->load($in_reply_to);
    $usermessage->subject = "Re: ".$temp_message->subject;
    $usermessage->in_reply_to = $in_reply_to;
    $usermessage->creator_id = $user->_id;

    if ($answer_to_all) {
      $temp_message->loadRefDests();
      $usermessage->_ref_destinataires = array();
      foreach ($temp_message->_ref_destinataires as $_destinataire) {
        if ($_destinataire->to_user_id != $user->_id) {
          $dest = new CUserMessageDest();
          $dest->to_user_id = $_destinataire->to_user_id;
          $dest->from_user_id = $usermessage->creator_id;
          $dest->loadRefTo()->loadRefFunction();
          $usermessage->_ref_destinataires[] = $dest;
        }
      }
    }
  }

  if ($to_id) {
    $dest = new CUserMessageDest();
    $dest->to_user_id = $to_id;
    $dest->from_user_id = $usermessage->creator_id;
    $dest->user_message_id = null;
    $dest->loadRefTo()->loadRefFunction();
    if ($in_reply_to) {
      $dest->in_reply_to_id = $in_reply_to;
    }
    $usermessage->_ref_destinataires[] = $dest;
  }
}
$usermessage->loadRefCreator()->loadRefFunction();


if (CAppUI::pref('inputMode') == 'html') {
// Initialisation de CKEditor
  $templateManager               = new CTemplateManager();
  $templateManager->editor       = "ckeditor";
  $templateManager->simplifyMode = true;
  if (!$usermessage->_can_edit) {
    $templateManager->printMode = true;
  }
  $templateManager->initHTMLArea();
}
// smarty
$smarty = new CSmartyDP();
$smarty->assign("usermessage", $usermessage);
$smarty->assign("user", $user);
$smarty->display("inc_edit_usermessage.tpl");
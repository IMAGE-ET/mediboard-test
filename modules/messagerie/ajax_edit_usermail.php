<?php 

/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage messagerie
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

$account_id = CValue::get('account_id');
$mail_id = Cvalue::get('mail_id');
$reply_to_id = CValue::get('reply_to_id');
$answer_to_all = CValue::get('answer_to_all');

$account = new CSourcePOP();
$account->load($account_id);

$smtp = CExchangeSource::get("mediuser-$account->object_id", 'smtp');

if (!$smtp->_id) {
  $smarty = new CSmartyDP();
  $smarty->assign('msg', CAppUI::tr('CUserMail-msg-no_smtp_source_linked_to_pop_account'));
  $smarty->assign('type', 'error');
  $smarty->assign('modal', 1);
  $smarty->assign('close_modal', 1);
  $smarty->display('inc_display_msg.tpl');
  CApp::rip();
}

$mail = new CUserMail();
if ($mail_id) {
  $mail->load($mail_id);
  if ($mail->text_html_id) {
    $mail->loadContentHTML();
    $mail->_content = $mail->_text_html->content;
  }
  elseif ($mail->text_plain_id) {
    $mail->loadContentPlain();
    $mail->_content = $mail->_text_plain->content;
  }
}
else {
  $mail->from = $account->user;
  $mail->account_class = $account->_class;
  $mail->account_id = $account->_id;
  $mail->draft = '1';

  if ($reply_to_id) {
    $mail->in_reply_to_id = $reply_to_id;
    $reply_to = new CUserMail();
    $reply_to->load($reply_to_id);
    $mail->to = $reply_to->from;
    strpos($reply_to->subject, 'Re:') === false ? $mail->subject = "Re: $reply_to->subject" : $mail->subject = $reply_to->subject;

    if ($answer_to_all) {
      $mail->cc = $reply_to->cc;
    }
  }

  $mail->store();
}
$mail->loadAttachments();
foreach ($mail->_attachments as $_attachment) {
  $_attachment->loadFiles();
}

// Initialisation de CKEditor
$templateManager = new CTemplateManager();
$templateManager->editor = "ckeditor";
$templateManager->messageMode = true;
$templateManager->initHTMLArea();

$smarty = new CSmartyDP();
$smarty->assign('mail', $mail);
$smarty->assign('account', $account);
$smarty->display('inc_edit_usermail.tpl');
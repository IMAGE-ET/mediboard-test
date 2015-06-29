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
 
/**
 * Description
 */
class CUserMailController extends CDoObjectAddEdit {

  /**
   * @see parent::__construct()
   */
  function __construct() {
    $this->CDoObjectAddEdit('CUserMail', 'user_mail_id');

    $this->redirect = 'm=messagerie';
  }

  /**
   * @see parent::doStore()
   */
  function doStore() {
    /** @var CUserMail $mail */
    $mail = $this->_obj;

    $mail->date_inbox = CMbDT::dateTime();
    $mail->date_read = $mail->date_inbox;

    $content_html = new CContentHTML();

    $mail->_content = CUserMail::purifyHTML($mail->_content);
    $content_html->content = $mail->_content;

    if (!$msg = $content_html->store()) {
      $mail->text_html_id = $content_html->_id;
      $mail->_text_html = $content_html;
    }

    $content_plain = new CContentAny();
    $content_plain->content = strip_tags($mail->_content);
    if (!$msg = $content_plain->store()) {
      $mail->text_plain_id = $content_plain->_id;
    }

    $hash = CMbSecurity::hash(CMbSecurity::SHA256, "==FROM==\n$mail->from\n==TO==\n$mail->to\n==SUBJECT==\n$mail->subject\n==CONTENT==\n$mail->_content");

    if ($msg = $mail->store()) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
      return parent::doStore();
    }

    $action = CValue::post('action');

    switch ($action) {
      case 'draft':
        $mail->draft = '1';
        CAppUI::setMsg('CUserMail-msg-drafted', UI_MSG_OK);
        break;

      case 'send':
        $mail->sent = '1';
        $mail->draft = '0';
        $account = $mail->loadAccount();

        if ($mail->_is_apicrypt) {
          /** @var CSourceSMTP $smtp */
          $smtp = CExchangeSource::get("mediuser-$account->object_id-apicrypt", 'smtp');
        }
        else {
          /** @var CSourceSMTP $smtp */
          $smtp = CExchangeSource::get("mediuser-$account->object_id", 'smtp');
        }
        $smtp->init();

        foreach (explode(',', $mail->to) as $_address) {
          $smtp->addTo($_address);
        }

        if ($mail->cc != '') {
          foreach (explode(',', $mail->cc) as $_address) {
            $smtp->addCc($_address);
          }
        }

        if ($mail->bcc != '') {
          foreach (explode(',', $mail->bcc) as $_address) {
            $smtp->addBcc($_address);
          }
        }

        $smtp->setSubject($mail->subject);

        if ($mail->_is_apicrypt) {
          $receiver = explode(',', $mail->to);
          $body = CApicrypt::encryptBody($account->object_id, $receiver[0], $mail->_content);
          $smtp->setBody($body);
        }
        else {
          $smtp->setBody($mail->_content);
        }

        /** @var CMailAttachments[] $attachments */
        $attachments = $mail->loadAttachments();
        foreach ($attachments as $_attachment) {
          $file = $_attachment->loadFiles();
          $smtp->addAttachment($file->_file_path, $file->file_name);
        }

        try {
          $smtp->send();
          CAppUI::setMsg('CUserMail-msg-sent', UI_MSG_OK);
        }
        catch (phpmailerException $e) {
          CAppUI::setMsg($e->errorMessage(), UI_MSG_ERROR);
        }
        catch (CMbException $e) {
          $e->stepAjax();
        }
        break;
      default:
    }

    $mail->store();

    if (CAppUI::isMsgOK() && $this->redirectStore) {
      $this->redirect =& $this->redirectStore;
    }

    if (!CAppUI::isMsgOK() && $this->redirectError) {
      $this->redirect =& $this->redirectError;
    }
  }
}

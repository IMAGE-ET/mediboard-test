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
class CMailAttachmentController extends CDoObjectAddEdit {
  /**
   * @see parent::__construct()
   */
  function __construct() {
    $this->CDoObjectAddEdit('CMailAttachments', 'user_mail_attachment_id');

    $this->redirect = 'm=messagerie';
  }

  /**
   * @see parent::doStore()
   */
  function doStore() {
    if (isset($_FILES['attachment'])) {
      $mail_id = CValue::post('mail_id');

      $mail = new CUserMail();
      $mail->load($mail_id);

      $files = array();

      foreach ($_FILES['attachment']['error'] as $key => $file_error) {
        if (isset($_FILES['attachment']['name'][$key])) {
          $files[] = array(
            'name'     => $_FILES['attachment']['name'][$key],
            'tmp_name' => $_FILES['attachment']['tmp_name'][$key],
            'error'    => $_FILES['attachment']['error'][$key],
            'size'     => $_FILES['attachment']['size'][$key],
          );
        }
      }

      foreach ($files as $_key => $_file) {
        if ($_file['error'] == UPLOAD_ERR_NO_FILE) {
          continue;
        }

        if ($_file['error'] != 0) {
          CAppUI::setMsg(CAppUI::tr("CFile-msg-upload-error-" . $_file["error"]), UI_MSG_ERROR);
          continue;
        }

        $attachment              = new CMailAttachments();
        $attachment->name        = $_file['name'];
        $content_type            = mime_content_type($_file['tmp_name']);
        $attachment->type        = $attachment->getTypeInt($content_type);
        $attachment->bytes       = $_file['size'];
        $attachment->mail_id     = $mail_id;
        $content_type            = explode('/', $content_type);
        $attachment->subtype     = strtoupper($content_type[1]);
        $attachment->disposition = 'ATTACHMENT';
        $attachment->extension   = substr(strrchr($attachment->name, '.'), 1);
        $attachment->part        = $mail->countBackRefs('mail_attachments') + 1;

        $attachment->store();

        $file = new CFile();

        $file->setObject($attachment);
        $file->author_id = CAppUI::$user->_id;
        $file->file_name = $attachment->name;
        $file->file_date = CMbDT::dateTime();
        $file->fillFields();
        $file->updateFormFields();
        $file->doc_size = $attachment->bytes;
        $file->file_type = mime_content_type($_file['tmp_name']);

        $file->moveFile($_file, true);

        if ($msg = $file->store()) {
          CAppUI::setMsg(CAppUI::tr('CMailAttachments-error-upload-file') . ':' . CAppUI::tr($msg), UI_MSG_ERROR);
          CApp::rip();
        }

        $attachment->file_id = $file->_id;
        if ($msg = $attachment->store()) {
          CAppUI::setMsg($msg, UI_MSG_ERROR);
          CApp::rip();
        }
      }

      CAppUI::setMsg('CMailAttachments-msg-added', UI_MSG_OK);
    }
    else {
      parent::doStore();
    }
  }
}

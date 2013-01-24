<?php

/**
 * Unlink an attachment
 *
 * @category Messagerie
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */
 
 
CCanDo::checkEdit();

$attachment_id = CValue::get("attachment_id");

//open the attachment
$attachment = new CMailAttachments();
$attachment->load($attachment_id);
$attachment->loadFiles();

//work
$file = $attachment->_file;
$file->setObject($attachment);
if (!$msg = $file->store()) {
  $attachment->file_id = "";
  if (!$msg2 = $attachment->store()) {
    CAppUI::setMsg("CMailAttachment-unlinked", UI_MSG_OK);
  }
  else {
    CAppUI::setMsg("CMailAttachment-unlinked-failed", UI_MSG_ERROR, $msg2);
  }
}


echo CAppUI::getMsg();
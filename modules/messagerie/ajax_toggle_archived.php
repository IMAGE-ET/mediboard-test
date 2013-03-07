<?php

/**
 * Toggle favorite 1 or 0 for a specificated email
 *
 * @category Messagerie
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */
 
 
CCanDo::checkEdit();

$mail_id = CValue::get("mail_id");

$mail = new CUserMail();
$mail->load($mail_id);

if (!$mail->_id) {
  CAppUI::stepAjax("CUserMail-mail-notfound-number%d", UI_MSG_ERROR, $mail->_id);
}

$arch = $mail->archived = ($mail->archived) ? 0 : 1;

if (!$mail->date_read) {
  $mail->date_read = CMbDT::dateTime();
}
if ($msg = $mail->store()) {
  CAppUI::stepAjax($msg, UI_MSG_ERROR);
}
else {
  CAppUI::stepAjax("CUserMail-toggle-archive-$arch", UI_MSG_OK);
}

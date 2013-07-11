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
 
 
CCanDo::checkRead();

$mail_id = CValue::get("mail_id");

$mail = new CUserMail();
$mail->load($mail_id);

if (!$mail->_id) {
  CAppUI::stepAjax("CUserMail-mail-notfound-number%d", UI_MSG_ERROR, $mail->_id);
}

$mail->favorite = ($mail->favorite) ? 0 : 1;
$fav = $mail->favorite;
if ($msg = $mail->store()) {
  CAppUI::stepAjax($msg, UI_MSG_ERROR);
}
else {
  CAppUI::stepAjax("CUserMail-toggle-favorite-$fav", UI_MSG_OK);
}

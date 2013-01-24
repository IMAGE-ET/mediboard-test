<?php

/**
 * list of attachment with radio button
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
$mail->loadRefsFwd();

//load files
foreach ($mail->_attachments as $_att) {
  $_att->loadFiles();
}

//check for inline attachment
$mail->checkInlineAttachments();
 
$smarty = new CSmartyDP();
$smarty->assign("mail", $mail);
$smarty->display("inc_vw_list_attachment.tpl");
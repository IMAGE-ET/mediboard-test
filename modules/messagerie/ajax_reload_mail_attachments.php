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

$mail_id = CValue::get('mail_id');

$mail = new CUserMail();
$mail->load($mail_id);
$mail->loadAttachments();

foreach ($mail->_attachments as $_attachment) {
  $_attachment->loadFiles();
}

$smarty = new CSmartyDP();
$smarty->assign('attachments', $mail->_attachments);
$smarty->display('inc_mail_attachments.tpl');
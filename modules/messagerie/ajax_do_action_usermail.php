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

CCanDo::checkRead();

$usermail_ids = json_decode(stripslashes(CValue::get('usermail_ids', '[]')));
$action = CValue::get('action');

if (empty($usermail_ids)) {
  CAppUI::stepAjax('CUserMail-msg-no_mail_selected', UI_MSG_WARNING);
  CApp::rip();
}

foreach ($usermail_ids as $usermail_id) {
  $mail = new CUserMail();
  $mail->load($usermail_id);

  switch ($action) {
    case 'unarchive':
      $mail->archived = 0;

      if (!$mail->date_read) {
        $mail->date_read = CMbDT::dateTime();
      }
      break;
    case 'archive':
      $mail->archived = 1;

      if (!$mail->date_read) {
        $mail->date_read = CMbDT::dateTime();
      }
      break;

    case 'unfavour':
      $mail->favorite = 0;
      break;
    case 'favour':
      $mail->favorite = 1;
      break;

    case 'delete':
      if ($msg = $mail->delete()) {
        CAppUI::stepAjax($msg, UI_MSG_ERROR);
      }
      break;

    case 'mark_read':
      $mail->date_read = CMbDT::dateTime();
      break;

    case 'mark_unread':
      $mail->date_read = '';
      break;

    default:
      break;
  }

  if ($action != 'delete') {
    if ($msg = $mail->store()) {
      CAppUI::stepAjax($msg, UI_MSG_ERROR);
    }
  }
}

$msg = "CUserMail-msg-$action";
if (count($usermail_ids) > 1) {
  $msg .= '-pl';
}

CAppUI::stepAjax($msg, UI_MSG_OK);
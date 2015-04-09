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


$user_dest_ids = json_decode(stripslashes(CValue::get("user_dest_ids", '[]')));
$action = CValue::get("action");
$value = CValue::get("value");

foreach ($user_dest_ids as $user_dest_id) {
  $usermessagedest = new CUserMessageDest();
  $usermessagedest->load($user_dest_id);

  switch ($action) {

    case 'archive':
      $usermessagedest->archived = $value;
      if (!$usermessagedest->datetime_read) {
        $usermessagedest->datetime_read = CMbDT::dateTime();
      }
      break;

    case 'star':
      $usermessagedest->starred = $value;
      break;

    case 'delete':
      if (is_null($usermessagedest->datetime_sent)) {
        $user_message   = $usermessagedest->loadRefMessage();

        $delete_message = true;
        $user_message->loadRefDests();
        foreach ($user_message->_ref_destinataires as $_user_dest) {
          if (is_null($_user_dest->datetime_sent)) {
            if ($msg = $_user_dest->delete()) {
              CAppUI::stepAjax($msg, UI_MSG_ERROR);
            }
          }
          else {
            $delete_message = false;
          }
        }

        if ($delete_message) {
          if ($msg = $user_message->delete()) {
            CAppUI::stepAjax($msg, UI_MSG_ERROR);
          }
        }
      }
      else {
        $usermessagedest->deleted = '1';
        if ($msg = $usermessagedest->store()) {
          CAppUI::stepAjax($msg, UI_MSG_ERROR);
        }
      }
      break;

    case 'mark_read':
      $usermessagedest->datetime_read = CMbDT::dateTime();
      break;

    case 'mark_unread':
      $usermessagedest->datetime_read = '';
      break;

    default:
      break;
  }

  if ($action != 'delete') {
    if ($msg = $usermessagedest->store()) {
      CAppUI::stepAjax($msg, UI_MSG_ERROR);
    }
  }
}

$msg = "CUserMessageDest-msg-$action";
if ($value) {
  $msg .= "-$value";
}
CAppUI::stepAjax($msg, UI_MSG_OK);
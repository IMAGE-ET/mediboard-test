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


$user_dest_id = CValue::get("user_dest_id");
$action = CValue::get("action");
$value = CValue::get("value");

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

  default:
    break;
}

if ($msg = $usermessagedest->store()) {
  CAppUI::stepAjax($msg, UI_MSG_ERROR);
}
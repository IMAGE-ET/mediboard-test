<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();
$user_id = CValue::get("user_id");

$log_pop = new CSourcePOP();
$log_pop->name = "user-pop-".$user_id;
$log_pop->loadMatchingObject();

$AcountSet = isset($log_pop->_id);
if (!$AcountSet) {
  CAppUI::setMsg("CSourcePOP-error-noAccount",UI_MSG_ERROR,$user_id);
}

//pop init
$pop = new CPop($log_pop);
$pop->open();
$unseen = $pop->search('UNSEEN');

if(count($unseen)>0){

  //how many
  if (count($unseen)>1) {
    CAppUI::setMsg("CPOP-msg-newMsgs",UI_MSG_OK,count($unseen));
  } else {
    CAppUI::setMsg("CPOP-msg-newMsg",UI_MSG_OK,count($unseen));
  }

  //traitement
  foreach($unseen as $_mail) {
    $mail_unseen = new CUserMail();
    $mail_unseen->loadHeaderFromSource($pop->header($_mail));
    $mail_unseen->loadMatchingObject();
    if(!$mail_unseen->_id) {
      $mail_unseen->user_id = $user_id;
      if ($msg = $mail_unseen->store()) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
      } else {
        CAppUI::setMsg("CUserMail-msg-mailAdded",UI_MSG_OK);
      }
    } else {
      mbTrace($mail_unseen);
    }

  }



}
else {
  CAppUI::setMsg("CPOP-msg-nonewMsg",UI_MSG_OK,$user_id);
}

$pop->close();

//affichage des messages
echo CAppUI::getMsg();
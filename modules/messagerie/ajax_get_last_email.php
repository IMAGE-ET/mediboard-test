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
    CAppUI::setMsg("CPop-msg-newMsgs",UI_MSG_OK,count($unseen));
  } else {
    CAppUI::setMsg("CPop-msg-newMsg",UI_MSG_OK,count($unseen));
  }

  //traitement
  foreach($unseen as $_mail) {
    $mail_unseen = new CUserMail();


    if(!$mail_unseen->loadMatchingFromSource($pop->header($_mail))) {
      $mail_unseen->loadContentFromSource($pop->getFullBody($_mail,false,false,true));
      $mail_unseen->user_id = $user_id;

      //text plain
      if($mail_unseen->_text_plain) {
        $textP = new CContentAny();
        $textP->content = $mail_unseen->_text_plain;
        if ($msg = $textP->store()) {
          CAppUI::setMsg($msg, UI_MSG_ERROR);
        }
        $mail_unseen->text_plain_id = $textP->_id;
      }

      //text html
      if($mail_unseen->_text_html) {
        $textH = new CContentHTML();
        $text = new CMbXMLDocument();
        $text = $text->sanitizeHTML($mail_unseen->_text_html); //cleanup
        $textH->content = $text;

        if ($msg = $textH->store()) {
          CAppUI::setMsg($msg, UI_MSG_ERROR);
        } else {
          $mail_unseen->text_html_id = $textH->_id;
        }
      } //if html

      if ($msg = $mail_unseen->store()) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
      }

      //attachments
      $attachs = $pop->getListAttachments($_mail,false);
      foreach ($attachs as $_attch) {
        $_attch->mail_id = $mail_unseen->_id;
        if($msg = $_attch->store()) {
          CAppUI::setMsg("CMailAttachments-error-unsave",UI_MSG_ERROR);
        }
      }
    } //if id




  } //foreach



}
else {
  CAppUI::setMsg("CPop-msg-nonewMsg",UI_MSG_OK,$user_id);
}

$pop->close();

//affichage des messages
echo CAppUI::getMsg();
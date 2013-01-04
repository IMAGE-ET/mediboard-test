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

if (!$log_pop->_id) {
    CAppUI::setMsg("CSourcePOP-error-noAccount", UI_MSG_ERROR, $user_id);
}

//pop init
$pop = new CPop($log_pop);
$pop->open();
$unseen = $pop->search('UNSEEN');

if (count($unseen)>0) {
  //how many
  if (count($unseen)>1) {
    CAppUI::setMsg("CPop-msg-newMsgs", UI_MSG_OK, count($unseen));
  }
  else {
    CAppUI::setMsg("CPop-msg-newMsg", UI_MSG_OK, count($unseen));
  }

  // traitement
  foreach ($unseen as $_mail) {
    $mail_unseen = new CUserMail();

    if (!$mail_unseen->loadMatchingFromSource($pop->header($_mail))) {
      $mail_unseen->loadContentFromSource($pop->getFullBody($_mail, false, false, true));
      $mail_unseen->user_id = $user_id;


      //text plain
      if ($mail_unseen->_text_plain) {
        $textP = new CContentAny();
        $textP->content = $mail_unseen->_text_plain;
        if ($msg = $textP->store()) {
          CAppUI::setMsg($msg, UI_MSG_ERROR);
        }
        $mail_unseen->text_plain_id = $textP->_id;
      }

      //text html
      if ($mail_unseen->_text_html) {
        $textH = new CContentHTML();
        $text = new CMbXMLDocument();
        $text = $text->sanitizeHTML($mail_unseen->_text_html); //cleanup
        $textH->content = $text;

        if ($msg = $textH->store()) {
          CAppUI::setMsg($msg, UI_MSG_ERROR);
        }
        else {
          $mail_unseen->text_html_id = $textH->_id;
        }
      }


      //store the usermail
      if ($msg = $mail_unseen->store()) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
      }

      //attachments list
      $attachs = $pop->getListAttachments($_mail);

      foreach ($attachs as $_attch) {
        $_attch->mail_id = $mail_unseen->_id;
        $_attch->loadMatchingObject();
        if (!$_attch->_id) {
          if ($msg = $_attch->store()) {
            CAppUI::setMsg("CMailAttachments-error-unsave", UI_MSG_ERROR);
          }
        }

        //attachments CFILE
        //si preference à vrai OU que la piece jointe est incluse au texte
        if (CAppUI::pref("getAttachmentOnUpdate") || $_attch->disposition == "INLINE") {

          $file = new CFile();
          $file->setObject($_attch);
          $file->private = 0;
          $file->author_id  = CAppUI::$user->_id;
          $file->loadMatchingObject();

          if (!$file->_id) {
            $file_pop = $pop->decodeMail($_attch->encoding, $pop->openPart($mail_unseen->uid, $_attch->getpartDL()));
            $file->file_name  = $_attch->name;
            $file->file_type  = $_attch->getType($_attch->type, $_attch->subtype);
            $file->fillFields();
            $file->putContent($file_pop);
            if ($str = $file->store()) {
              CAppUI::setMsg($str, UI_MSG_ERROR);
            }
          }
        }
      }
    }
    else {
      //mail is unread on server and read on mediboard
      if ($mail_unseen->date_read) {
        if ($pop->setflag($_mail, "\\Seen")) {
          CAppUI::setMsg("CPop-msg-unreadfromPopReadfromMb", UI_MSG_OK, count($unseen));
        }
      }
    }

  } //foreach
}
else {
  CAppUI::setMsg("CPop-msg-nonewMsg", UI_MSG_OK, $user_id);
}

$pop->close();

//affichage des messages
echo CAppUI::getMsg();
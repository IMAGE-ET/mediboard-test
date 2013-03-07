<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();
CPop::checkImapLib();

$user_id = CValue::get("user_id");

$user = new CMediusers();
$user->load($user_id);

$size_required = CAppUI::pref("getAttachmentOnUpdate");
if ($size_required == "") {
  $size_required = 0;
}

$log_pop = new CSourcePOP();
$log_pop->active = 1;         //only activated ones
$log_pop->object_class = $user->_class;
$log_pop->object_id = $user->_id;
$log_pops = $log_pop->loadMatchingList();


foreach ($log_pops as $_pop) {
  //pop init
  $pop = new CPop($_pop);
  if (!$pop->open()) {
    //disable the account because of a problem
    //$_pop->active = 0;
    //$_pop->store();
    CAppUI::stepAjax("CPop-error-imap_open-disable", UI_MSG_ERROR);
  }
  $unseen = $pop->search('UNSEEN');

  if (count($unseen)>0) {
    //how many
    if (count($unseen)>1) {
      CAppUI::stepAjax("CPop-msg-newMsgs", UI_MSG_OK, count($unseen));
    }
    else {
      CAppUI::stepAjax("CPop-msg-newMsg", UI_MSG_OK, count($unseen));
    }

    // traitement
    foreach ($unseen as $_mail) {
      $mail_unseen = new CUserMail();
      $mail_unseen->account_id = $_pop->_id;

      if (!$mail_unseen->loadMatchingFromSource($pop->header($_mail))) {
        $mail_unseen->loadContentFromSource($pop->getFullBody($_mail, false, false, true));


        //text plain
        if ($mail_unseen->_text_plain) {
          $textP = new CContentAny();
          //apicrypt
          if (CModule::getActive("apicrypt") && $mail_unseen->_is_apicrypt == "plain") {
            $textP->content = CApicrypt::uncryptBody($user->_id, $mail_unseen->_text_plain);
          }
          else {
            $textP->content = $mail_unseen->_text_plain;
          }

          if ($msg = $textP->store()) {
            CAppUI::stepAjax($msg, UI_MSG_ERROR);
          }
          $mail_unseen->text_plain_id = $textP->_id;
        }

        //text html
        if ($mail_unseen->_text_html) {
          $textH = new CContentHTML();
          $text = new CMbXMLDocument();
          $text = $text->sanitizeHTML($mail_unseen->_text_html); //cleanup
          //apicrypt
          if (CModule::getActive("apicrypt") && $mail_unseen->_is_apicrypt == "html") {
            $textH->content = CApicrypt::uncryptBody($user->_id, $text);
          }
          else {
            $textH->content = $text;
          }


          if ($msg = $textH->store()) {
            CAppUI::stepAjax($msg, UI_MSG_ERROR);
          }
          else {
            $mail_unseen->text_html_id = $textH->_id;
          }
        }


        //store the usermail
        if ($msg = $mail_unseen->store()) {
          CAppUI::stepAjax($msg, UI_MSG_ERROR);
        }

        //attachments list
        $attachs = $pop->getListAttachments($_mail);

        foreach ($attachs as $_attch) {
          $_attch->mail_id = $mail_unseen->_id;
          $_attch->loadMatchingObject();
          if (!$_attch->_id) {
            if ($msg = $_attch->store()) {
              CAppUI::stepAjax("CMailAttachments-error-unsave", UI_MSG_ERROR);
            }
          }
          //si preference taille ok OU que la piece jointe est incluse au texte => CFile
          if (($_attch->bytes <= $size_required ) || $_attch->disposition == "INLINE") {

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
                CAppUI::stepAjax($str, UI_MSG_ERROR);
              }
            }
          }
        }
      }
      else {
        //le mail est non lu sur MB mais lu sur IMAP => on le flag
        if ($mail_unseen->date_read) {
          if ($pop->setflag($_mail, "\\Seen")) {
            CAppUI::stepAjax("CPop-msg-unreadfromPopReadfromMb", UI_MSG_OK, count($unseen));
          }
        }
      }

    } //foreach
  }
  else {
    CAppUI::stepAjax("CPop-msg-nonewMsg", UI_MSG_OK, $_pop->libelle);
  }

  $_pop->last_update = CMbDT::dateTime();
  $_pop->store();

  $pop->close();
}
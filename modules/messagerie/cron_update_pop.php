<?php

/**
 * Update the source pop account
 *
 * @category Messagerie
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */
 
 
CCanDo::checkRead();
CPop::checkImapLib();

$nbAccount = CAppUI::conf("messagerie CronJob_nbMail");
$older = CAppUI::conf("messagerie CronJob_olderThan");

$user_id = CValue::get("user_id");


$size_required = CAppUI::pref("getAttachmentOnUpdate");
if ($size_required == "") {
  $size_required = 0;
}

$source = new CSourcePOP();
$where = array();
$where["active"] = "= '1'";
if ($user_id) {
  $where["object_class"] = " = 'CMediusers'";
  $where["object_id"] = " = '$user_id'";
}
//$where["last_update"] = "< (NOW() - INTERVAL $older MINUTE)"; //doit avoir �t� updat� il y a plus de 5 minutes
$order = "'last_update' ASC";
$limit = "0, $nbAccount";
$sources = $source->loadList($where, $order, $limit);


//for each POP/IMAP source
foreach ($sources as $_source) {

  //no user => next
  if (!$_source->user) {
    continue;
  }

  // when a mail is copied in mediboard, will it be marked as read on the server ?
  $markReadServer = 0;
  $pref = CPreferences::get($_source->object_id);   //for user_id
  $markReadServer = (isset($pref["markMailOnServerAsRead"])) ? $pref["markMailOnServerAsRead"] : CAppUI::pref("markMailOnServerAsRead");


  $pop = new CPop($_source);
  if (!$pop->open()) {
    continue;
  }
  $unseen = $pop->search('UNSEEN');

  if (count($unseen)>0) {

    //how many
    if ($user_id) {
      if (count($unseen)>1) {
        CAppUI::stepAjax("CPop-msg-newMsgs", UI_MSG_OK, count($unseen));
      }
      else {
        CAppUI::stepAjax("CPop-msg-newMsg", UI_MSG_OK, count($unseen));
      }
    }


    //set email as read in imap/pop server
    if ($markReadServer) {
      $pop->setFlag(implode(",", $unseen), "\\Seen");
    }

    foreach ($unseen as $_mail) {

      $mail_unseen = new CUserMail();
      $mail_unseen->account_id = $_source->_id;

      if (!$mail_unseen->loadMatchingFromSource($pop->header($_mail))) {
        $mail_unseen->loadContentFromSource($pop->getFullBody($_mail, false, false, true));


        //text plain
        if ($mail_unseen->_text_plain) {
          $textP = new CContentAny();
          //apicrypt
          if (CModule::getActive("apicrypt") && $mail_unseen->_is_apicrypt == "plain") {
            $textP->content = CApicrypt::uncryptBody($_source->object_id, $mail_unseen->_text_plain);
          }
          else {
            $textP->content = $mail_unseen->_text_plain;
          }
          $textP->store();
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

          if (!$msg = $textH->store()) {
            $mail_unseen->text_html_id = $textH->_id;
          }
        }


        //store the usermail
        $mail_unseen->store();

        //attachments list
        $attachs = $pop->getListAttachments($_mail);

        foreach ($attachs as $_attch) {
          $_attch->mail_id = $mail_unseen->_id;
          $_attch->loadMatchingObject();
          if (!$_attch->_id) {
            $_attch->store();
          }
          //si preference taille ok OU que la piece jointe est incluse au texte => CFile
          if (($_attch->bytes <= $size_required ) || $_attch->disposition == "INLINE") {

            $file = new CFile();
            $file->setObject($_attch);
            $file->author_id  = CAppUI::$user->_id;

            if (!$file->loadMatchingObject()) {
              $file_pop = $pop->decodeMail($_attch->encoding, $pop->openPart($mail_unseen->uid, $_attch->getpartDL()));
              $file->file_name  = $_attch->name;
              $file->file_type  = $_attch->getType($_attch->type, $_attch->subtype);
              $file->fillFields();
              $file->updateFormFields();
              $file->putContent($file_pop);
              $file->store();
            }
          }
        }
      }
      else {
        //le mail est non lu sur MB mais lu sur IMAP => on le flag
        if ($mail_unseen->date_read) {
          $pop->setflag($_mail, "\\Seen");
        }
      }

    } //foreach
  }
  else {
    CAppUI::stepAjax("CPop-msg-nonewMsg", UI_MSG_OK, $_source->libelle);
  }

  $_source->last_update = CMbDT::dateTime();
  $_source->store();
  $pop->close();
}

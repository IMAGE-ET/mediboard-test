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

$account_id = CValue::get("account_id");

//source
$source = new CSourcePOP();
$where = array();
$where["active"] = "= '1'";

if ($account_id) {
  $where["source_pop_id"] = " = '$account_id'";
}
$order = "'last_update' ASC";
$limit = "0, $nbAccount";
$sources = $source->loadList($where, $order, $limit);

//$where["last_update"] = "< (NOW() - INTERVAL $older MINUTE)"; //doit avoir été updaté il y a plus de 5 minutes

/** @var $sources CSourcePOP[] */
foreach ($sources as $_source) {

  $user = $_source->loadRefMetaObject();

  //no user => next
  if (!$_source->user) {
    continue;
  }

  // when a mail is copied in mediboard, will it be marked as read on the server ?
  $markReadServer = 0;
  $pref = CPreferences::get($_source->object_id);   //for user_id
  $markReadServer = (isset($pref["markMailOnServerAsRead"])) ? $pref["markMailOnServerAsRead"] : CAppUI::pref("markMailOnServerAsRead");

  //last email uid from mediboard
  $mbMailUid = (CUserMail::getLastMailUid($_source->_id)) ? CUserMail::getLastMailUid($_source->_id) : 0;

  //limit by conf
  $limitMail = CAppUI::conf("messagerie limit_external_mail");

  $pop = new CPop($_source);
  if (!$pop->open()) {
    continue;
  }
  //reception
  $unseen = $pop->search('ALL');
  $total = count($unseen);

  //get mail > last mb mail
  foreach ($unseen as $key => $_unseen) {
    if ($_unseen < $mbMailUid) {
      unset($unseen[$key]);
    }
  }
  array_splice($unseen, CAppUI::conf("messagerie limit_external_mail"));

  if (count($unseen)>0) {
    $unread = 0;    //unseen mail
    $loop = 0;      //loop of foreach
    $created = 0;
    foreach ($unseen as $_mail) {

      $mail_unseen = new CUserMail();
      $mail_unseen->account_id = $_source->_id;

      //mail non existant
      if (!$mail_unseen->loadMatchingFromSource($pop->header($_mail))) {
        $mail_unseen->loadContentFromSource($pop->getFullBody($_mail, false, false, true));

        //text plain
        $mail_unseen->getPlainText($_source->object_id);
        //text html
        $mail_unseen->getHtmlText($_source->object_id);

        //sent ?
        if (strpos($mail_unseen->from, $_source->user) !== false) {
          $mail_unseen->sent = 1;
        }

        //unread increment
        if (!$mail_unseen->date_read) {
          $unread++;
        }

        //store the usermail
        if (!$msg = $mail_unseen->store()) {
          $created++;
        }

        //attachments list
        $attachs = $pop->getListAttachments($_mail);
        $mail_unseen->attachFiles($attachs, $pop);
      }
      // mail existe
      else {
        // si le mail est lu sur MB mais non lu sur IMAP => on le flag
        if ($mail_unseen->date_read) {
          $pop->setflag($_mail, "\\Seen");
        }
      }
      $loop++;
    } //foreach

    //set email as read in imap/pop server
    if ($markReadServer) {
      $pop->setFlag(implode(",", $unseen), "\\Seen");
    }


    //number of mails gathered
    CAppUI::stepAjax("CPop-msg-newMsgs", UI_MSG_OK, $unread, $created, $total);
  }
  else {
    CAppUI::stepAjax("CPop-msg-nonewMsg", UI_MSG_OK, $_source->libelle);
  }

  $_source->last_update = CMbDT::dateTime();
  $_source->store();
  $pop->close();
}

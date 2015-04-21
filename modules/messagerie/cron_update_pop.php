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

$limitMail = CValue::get("limit", CAppUI::conf("messagerie limit_external_mail")+1);

$account_id = CValue::get("account_id");
$import = CValue::get("import", 0);

//source
$source = new CSourcePOP();
$where = array();
$where["active"] = "= '1'";
$where["cron_update"] = "= '1'";

if ($account_id) {
  $where["source_pop_id"] = " = '$account_id'";
}
$order = "'last_update' ASC";
$limit = "0, $nbAccount";
$sources = $source->loadList($where, $order, $limit);

/** @var $sources CSourcePOP[] */
foreach ($sources as $_source) {

  $user = $_source->loadRefMetaObject();

  //no user => next
  if (!$_source->user) {
    CAppUI::stepAjax("pas d'utilisateur pour cette source %s", UI_MSG_WARNING, $_source->_view);
    continue;
  }

  // when a mail is copied in mediboard, will it be marked as read on the server ?
  $markReadServer = 0;
  $prefs = CPreferences::get($_source->object_id);   //for user_id
  $markReadServer = (isset($prefs["markMailOnServerAsRead"])) ? $prefs["markMailOnServerAsRead"] : CAppUI::pref("markMailOnServerAsRead");
  $archivedOnReception = (isset($prefs["mailReadOnServerGoToArchived"])) ? $prefs["mailReadOnServerGoToArchived"] : CAppUI::pref("mailReadOnServerGoToArchived");

  //last email uid from mediboard
  $mbMailUid = (CUserMail::getLastMailUid($_source->_id)) ? CUserMail::getLastMailUid($_source->_id) : 0;

  // last email datetime
  if ($import) {
    $firstEmailDate = CUserMail::getFirstMailDate($_source->_id);
    $firstCheck = $firstEmailDate;
    $firstCheck = CMbDT::dateTime("+1 DAY", $firstCheck);
    $month_number = CMbDT::format($firstCheck, "%m");
    $month = reset(array_keys(CFTP::$month_to_number, $month_number));
    $dateIMAP = CMbDT::format($firstCheck, "%d-$month-%Y");
  }
  else {
    $lastEmailDate = CUserMail::getLastMailDate($_source->_id);
    $firstCheck = $lastEmailDate;
    $firstCheck = CMbDT::dateTime("-1 DAY", $firstCheck);
    $month_number = CMbDT::format($firstCheck, "%m");
    $month = reset(array_keys(CFTP::$month_to_number, $month_number));
    $dateIMAP = CMbDT::format($firstCheck, "%d-$month-%Y");
  }


  //pop open account
  $pop = new CPop($_source);
  if (!$pop->open()) {
    CAppUI::stepAjax("Impossible de se connecter à la source (open) %s", UI_MSG_WARNING, $_source->_view);
    continue;
  }

  //If import mode (get before actual)
  if ($import) {
    $unseen = $pop->search('BEFORE "'.$dateIMAP.'"', true);
  }
  else {
    $unseen = $pop->search('SINCE "'.$dateIMAP.'"', true);
  }

  $results = count($unseen);
  $total = imap_num_msg($pop->_mailbox);

  //if get last email => check if uid server is > maxuidMb
  // @TODO : temporarly removed, we already get the more recent mail for filter
  /*if (!$import) {
    foreach ($unseen as $key => $_unseen) {
      if ($_unseen < $mbMailUid) {
        unset($unseen[$key]);
      }
    }
  }*/
  array_splice($unseen, $limitMail);

  if (count($unseen)>0) {
    $unread = 0;    //unseen mail
    $loop = 0;      //loop of foreach
    $created = 0;
    foreach ($unseen as $_mail) {
      $pop->cleanTemp();

      $mail_unseen = new CUserMail();
      $mail_unseen->account_id = $_source->_id;
      $mail_unseen->account_class = $_source->_class;

      //mail non existant
      $header = $pop->header($_mail);
      $content = $pop->getFullBody($_mail, false, false, true);
      $hash = $mail_unseen->makeHash($header, $content);

      if (!$mail_unseen->loadMatchingFromHash($hash)) {
        $mail_unseen->setHeaderFromSource($header);
        $mail_unseen->setContentFromSource($pop->getFullBody($_mail, false, false, true));

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

        //read on server + pref + not sent => archived !
        if ($mail_unseen->date_read && $archivedOnReception && !$mail_unseen->sent) {
          $mail_unseen->archived = 1;
        }

        //store the usermail
        if (!$msg = $mail_unseen->store()) {
          $created++;
        }

        //attachments list
        $pop->cleanTemp();
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
    CAppUI::stepAjax("CPop-msg-newMsgs", UI_MSG_OK, $unread, $created, $results, $total);
  }
  else {
    CAppUI::stepAjax("CPop-msg-nonewMsg", UI_MSG_OK, $_source->libelle);
  }

  $_source->last_update = CMbDT::dateTime();
  $_source->store();
  $pop->close();
}

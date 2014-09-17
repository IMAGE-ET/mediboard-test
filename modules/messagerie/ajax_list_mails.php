<?php /** $Id$ **/

/**
 * @package    Mediboard
 * @subpackage messagerie
 * @version    $Revision$
 * @author     SARL OpenXtrem
 */

CCanDo::checkRead();

$account_id   = CValue::get("account_id");

//user connected
$user = CMediusers::get();

//filters
$mode = CValue::get("mode", "unread");

//others
$page         = CValue::get("page", 0);
$limit_list   = CAppUI::pref("nbMailList", 20);

//account POP
$account_pop = new CSourcePOP();
$account_pop->load($account_id);

if (($account_pop->object_id != $user->_id) && $account_pop->is_private) {
  CAppUI::stepAjax("CSourcePOP-error-not_your_account_private", UI_MSG_ERROR);
}

//no account_id, first of account of user
$where = array();
$where["object_class"] = " = 'CMediusers'";
$where["object_id"] = " = '$user->_id'";
$account_pop->loadObject($where);

$where = array();
//mails
$mail = new CUserMail();

switch ($mode) {
  case 'inbox':
    $where['account_id'] = "= '$account_id' ";
    $where['account_class'] = "= 'CSourcePOP' ";
    $where['archived'] = "= '0' ";
    $where['sent'] = "= '0' ";
    //$where['date_read'] = ' IS NULL';
    break;

  case 'archived':
    $where['account_id'] = "= '$account_id'";
    $where['account_class'] = "= 'CSourcePOP'";
    $where['archived'] = "= '1' ";
    break;

  case 'favorited' :
    $where['account_id'] = "= '$account_id'";
    $where['account_class'] = "= 'CSourcePOP'";
    $where['favorite'] = "= '1' ";
    break;

  case 'sent':
    $source_smtp = CExchangeSource::get("mediuser-$user->_id", "smtp");
    if ($source_smtp->_id) {
      $where[] = "(account_id = '$account_id' AND account_class = 'CSourcePOP') OR (account_id = '$source_smtp->_id' AND account_class = 'CSourceSMTP')";
    }
    else {
      $where['account_id'] = "= '$account_id'";
      $where['account_class'] = "= 'CSourcePOP'";
    }
    $where['sent'] = " = '1' ";
    break;
}

$order = "date_inbox DESC";
$limit= "$page, $limit_list";

$nb_mails = $mail->countList($where);
$mails = $mail->loadList($where, $order, $limit);

/** @var $mails CUserMail[] */
foreach ($mails as $_mail) {
  $_mail->loadReadableHeader();
  $_mail->loadRefsFwd();
  $_mail->checkApicrypt();
}

//smarty
$smarty = new CSmartyDP();
$smarty->assign("mails", $mails);
$smarty->assign("page", $page);
$smarty->assign("nb_mails", $nb_mails);
$smarty->assign("account_id", $account_id);
$smarty->assign("user", $user);
$smarty->assign("mode", $mode);
$smarty->assign("account_pop", $account_pop);
$smarty->display("inc_list_mails.tpl");
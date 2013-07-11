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

$user = CMediusers::get();

//account POP
$account_pop = new CSourcePOP();
$account_pop->load($account_id);

//no account_id, first of account of user
$where = array();
$where["object_id"] = " = '$user->_id'";
$account_pop->loadObject($where);

$where = array();
//mails
$mail = new CUserMail();
$where['account_id'] = "= '$account_id'";
switch ($mode) {
  case 'inbox':
    $where['archived'] = "= '0' ";
    $where['sent'] = "= '0'";
    //$where['date_read'] = ' IS NULL';
    break;

  case 'archived':
    $where['archived'] = "= '1' ";
    break;

  case 'favorited' :
    $where['favorite'] = "= '1' ";
    break;

  case 'sent':
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
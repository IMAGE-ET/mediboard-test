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
$mode = CValue::get("mode", "inbox");

//others
$page         = CValue::get("page", 0);
$limit_list   = CAppUI::pref("nbMailList", 20);

//account POP
$account_pop = new CSourcePOP();
$account_pop->load($account_id);

if (($account_pop->object_id != $user->_id) && $account_pop->is_private) {
  CAppUI::stepAjax("CSourcePOP-error-private_account", UI_MSG_ERROR);
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
    $nb_mails = CUserMail::countInbox($account_id);
    $mails = CUserMail::loadInbox($account_id, $page, $limit_list);
    break;

  case 'archived':
    $nb_mails = CUserMail::countArchived($account_id);
    $mails = CUserMail::loadArchived($account_id, $page, $limit_list);
    break;

  case 'favorites' :
    $nb_mails = CUserMail::countFavorites($account_id);
    $mails = CUserMail::loadFavorites($account_id, $page, $limit_list);
    break;

  case 'sentbox':
    $nb_mails = CUserMail::countSent($account_id);
    $mails = CUserMail::loadSent($account_id, $page, $limit_list);
    break;
  case 'drafts':
    $nb_mails = CUserMail::countDrafted($account_id);
    $mails = CUserMail::loadDrafted($account_id, $page, $limit_list);
    break;
}

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
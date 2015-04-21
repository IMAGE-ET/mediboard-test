man telnet<?php

/**
 * $Id$
 *  
 * @category Messagerie
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$user_connected = CMediusers::get();
$account_id = CValue::get("account_id");
$selected_folder = CValue::get('selected_folder', 'inbox');

$account = new CSourcePOP();
$account->load($account_id);
if ($account_id) {
  CValue::setSession("account_id", $account_id);
}

//user is atempting to see an account private from another medisuers
if (($account->object_id != $user_connected->_id) && ($account->is_private)) {
  CAppUI::stepAjax("CSourcePOP-error-private_account", UI_MSG_ERROR);
}

$folders = array(
  'inbox' => CUserMail::countUnread($account_id),
  'archived' => CUserMail::countArchived($account_id),
  'favorites' => CUserMail::countFavorites($account_id),
  'sentbox' => CUserMail::countSent($account_id),
  'drafts' => CUserMail::countDrafted($account_id)
);

//smarty
$smarty = new CSmartyDP();
$smarty->assign("account", $account);
$smarty->assign('folders', $folders);
$smarty->assign('selected_folder', $selected_folder);
$smarty->display("vw_account_mail.tpl");
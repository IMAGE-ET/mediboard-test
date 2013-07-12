<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$user_connected = CMediusers::get();
$user_id = CValue::get("user_id", $user_connected->_id);
$account_id = CValue::getOrSession("account_id");

//user
$user = new CMediusers();
$user->load($user_id);

//CSourcePOP account
$account = new CSourcePOP();
$group = "object_id";   //all accounts from an unique mediuser are grouped
$account->object_class = "CMediusers";

//all accounts linked to a mediuser
$accounts_available = $account->loadMatchingList(null, null, $group);
if (!$account_id) {
  $account_temp = reset($accounts_available);
  $account_id = $account_temp->_id;
}

//all accounts to the selected user
$account->object_id = $user->_id;
$accounts_user = $account->loadMatchingList();

$users = array();
/** @var CSourcePOP[] $accounts_available */
foreach ($accounts_available as $_account) {
  $userPop = $_account->loadRefMetaObject();
  $users[] = $userPop;
  $libelle = $_account->libelle ? $_account->libelle : $_account->_id;
}

//switching account check, if session account_id not in user_account, reset account_id
if (!array_key_exists($account_id, $accounts_user)) {
  $account_temp = reset($accounts_user);
  $account_id = $account_temp->_id;
}

//smarty
$smarty = new CSmartyDP();
$smarty->assign("user",  $user);
$smarty->assign("users", $users);
$smarty->assign("mails", $accounts_user);
$smarty->assign("account_id", $account_id);
$smarty->display("vw_list_externalMessages.tpl");
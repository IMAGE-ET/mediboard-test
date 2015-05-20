<?php 

/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage messagerie
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */


CCanDo::checkRead();

$current_user = CMediusers::get();

/* Getting the list of users */
$users_list = $current_user->loadListWithPerms(PERM_EDIT);

$selected_user_id = CValue::get('selected_user');

$selected_user = new CMediusers();
$selected_user->load($selected_user_id);
if (!$selected_user->_id) {
  $selected_user = $current_user;
}

/* Getting the list of the CSourcePop linked to the selected user */
$account = new CSourcePOP();
$where = array();

$where = array();
$where["source_pop.is_private"]   = "= '0'";
$where["source_pop.object_class"] = "= 'CMediusers'";
$where["users_mediboard.function_id"] = "= '$current_user->function_id'";
$where["users_mediboard.user_id"] = CSQLDataSource::prepareIn(array_keys($users_list));
$ljoin = array();
$ljoin["users_mediboard"] = "source_pop.object_id = users_mediboard.user_id AND source_pop.object_class = 'CMediusers'";

//all accounts linked to a mediuser
//all accounts from an unique mediuser are grouped, in order to have the mediusers list
/** @var CSourcePOP[] $accounts_available */
$accounts_available = $account->loadList($where, null, null, null, $ljoin);

//getting user list
$users = array();
foreach ($accounts_available as $_account) {
  $userPop = $_account->loadRefMetaObject();
  $users[$userPop->_id] = $userPop;
}

//all accounts to the selected user
$where["source_pop.object_id"] = " = '$selected_user->_id'";

//if user connected, show the private source pop
if ($current_user->_id == $selected_user->_id) {
  $where["source_pop.is_private"] = " IS NOT NULL";
}

$accounts_user = $account->loadList($where, null, null, null, $ljoin);

/** @var CSourcePOP[] $pop_accounts */
$pop_accounts = $account->loadList($where, null, null, null, $ljoin);

$mssante_account = false;
if (CModule::getActive('mssante') && CModule::getCanDo('mssante')->read) {
  $mssante_account = CMSSanteUserAccount::getAccountFor($selected_user);
}

$smarty = new CSmartyDP('modules/messagerie');
$smarty->assign('user', $current_user);
$smarty->assign('selected_user', $selected_user);
$smarty->assign('users_list', $users);
$smarty->assign('pop_accounts', $accounts_user);
$smarty->assign('mssante_account', $mssante_account);
$smarty->display('vw_messagerie.tpl');
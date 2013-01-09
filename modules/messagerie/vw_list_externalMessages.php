<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */


CCanDo::checkRead();
$user = CMediusers::get();
//unseen

$account = new CSourcePOP();
$account->object_class = $user->_class;
$account->object_id = $user->_id;
$accounts = $account->loadMatchingList();

$mailbox = array();

foreach ($accounts as $_account) {
  $libelle = $_account->libelle ? $_account->libelle : $_account->_id;
  $mailbox[$_account->_id]     = $libelle;
}

//smarty
$smarty = new CSmartyDP();
$smarty->assign("user",  $user);
$smarty->assign("mails", $mailbox);
$smarty->display("vw_list_externalMessages.tpl");
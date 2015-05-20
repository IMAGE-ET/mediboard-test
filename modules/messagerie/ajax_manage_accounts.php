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

$user = CMediusers::get();

$source_smtp = CSourceSMTP::get("mediuser-$user->_id", 'smtp', true, null, false);
$sources_smtp = array();
if ($source_smtp->_id) {
  $sources_smtp[] = $source_smtp;
}

$sources_pop = $user->loadRefsSourcePop();

if (CModule::getActive('mssante') && CModule::getCanDo('mssante')->read) {
  $mssante_account = CMSSanteUserAccount::getAccountForCurrentUser();
}
else {
  $mssante_account = false;
}

if (CModule::getActive('apicrypt') && CModule::getCanDo('apicrypt')->read) {
  $apicrypt_account = CExchangeSource::get("mediuser-$user->_id-apicrypt", "smtp", true, null, false);
}
else {
  $apicrypt_account = false;
}

$smarty = new CSmartyDP();
$smarty->assign('sources_smtp', $sources_smtp);
$smarty->assign('sources_pop', $sources_pop);
$smarty->assign('mssante_account', $mssante_account);
$smarty->assign('apicrypt_account', $apicrypt_account);
$smarty->display('inc_manage_accounts.tpl');
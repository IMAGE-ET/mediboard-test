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

$source_smtp = CExchangeSource::get("mediuser-".$user->_id, "smtp", true, null, false);

$source_pop = new CSourcePOP();
$source_pop->object_class = $user->_class;
$source_pop->object_id    = $user->_id;
$source_pop->name = 'SourcePOP-' . $user->_id . '-' . ($source_pop->countMatchingList() + 1);

$mssante = false;
if (CModule::getActive('mssante') && CModule::getCanDo('mssante')->read) {
  $mssante = true;
}

$apicrypt = false;
if (CModule::getActive('apicrypt') && CModule::getCanDo('apicrypt')->read) {
  $apicrypt = true;
}

$smarty = new CSmartyDP();
$smarty->assign('source_smtp', $source_smtp);
$smarty->assign('source_pop', $source_pop);
$smarty->assign('mssante', $mssante);
$smarty->assign('apicrypt', $apicrypt);
$smarty->display('inc_add_account.tpl');
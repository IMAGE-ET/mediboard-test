<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision: 17062 $
 * @author Thomas despoix
 */

$do = new CUserMailController();
$do->doBind();
if (intval(CValue::read($do->request, 'del'))) {
  $do->doDelete();
}
else {
  $do->doStore();
}

$smarty = new CSmartyDP;
$messages = CAppUI::getMsg();
$smarty->assign('messages', $messages);
$smarty->display('inc_callback_modal.tpl');

$do->doRedirect();
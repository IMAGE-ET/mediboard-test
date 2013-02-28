<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

CApp::setTimeLimit(600);
ignore_user_abort(1);
CValue::setSession(CValue::postOrSession("private"));
ini_set("upload_max_filesize", CAppUI::conf("dPfiles upload_max_filesize"));

$do = new CFileAddEdit;
$do->doBind();
if (intval(CValue::read($do->request, 'del'))) {
  $do->doDelete();
} else {
  $do->doStore();
}

$smarty = new CSmartyDP;
$messages = CAppUI::getMsg();
$smarty->assign("messages", $messages);
$smarty->display("../../dPfiles/templates/inc_callback_upload.tpl");

$do->doRedirect();

?>

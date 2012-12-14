<?php /** $Id$ **/

/**
 * @package    Mediboard
 * @subpackage messagerie
 * @version    $Revision$
 * @author     SARL OpenXtrem
 */

CCanDo::checkRead();

$user = CMediusers::get();
$mail_id = CValue::get("mail_id",0);

$log_pop = new CSourcePOP();
$log_pop->name = "user-pop-".$user->_id;
$log_pop->loadMatchingObject();

if(!$log_pop) {
  CAppUI::stepAjax("Source POP indisponible",UI_MSG_ERROR);
}

if (!$mail_id) {
  CAppUI::stepAjax("CSourcePOP-error-mail_id",UI_MSG_ERROR);
}

$pop = new CPop($log_pop);
$pop->open();
  $mail = new CUserMail();
  $mail->loadHeaderFromSource($pop->header($mail_id));
  $mail->loadContentFromSource($pop->getFullBody($mail_id));
$pop->close();


//Smarty
$smarty = new CSmartyDP();
$smarty->assign("mail", $mail);
$smarty->display("ajax_open_pop_email.tpl");

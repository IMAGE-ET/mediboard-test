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
$mail_id = CValue::get("mail_id");

$log_pop = new CSourcePOP();
$log_pop->name = "user-pop-".$user->_id;
$log_pop->loadMatchingObject();

if(!$log_pop) {
  CAppUI::stepAjax("Source POP indisponible",UI_MSG_ERROR);
}

if (!$mail_id) {
  CAppUI::stepAjax("CSourcePOP-error-mail_id",UI_MSG_ERROR);
}

//pop init


  //mail
  $mail = new CUserMail();
  $mail->_id = $mail_id;
  $mail->loadMatchingObject();
  $mail->loadRefsFwd();

  if (!$mail->date_read) {
    $pop = new CPop($log_pop);
    $pop->open();
    if ($pop->setflag($mail->uid,"\\Seen")) {
      $mail->date_read = $mail->date_read = mbDateTime();
      $mail->store();
    }

    $pop->close();
  }

//Smarty
$smarty = new CSmartyDP();
$smarty->assign("mail", $mail);
$smarty->display("ajax_open_external_email.tpl");

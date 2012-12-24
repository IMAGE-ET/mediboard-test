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
$pop = new CPop($log_pop);
$pop->open();

//mail
$mail = new CUserMail();
$head = $pop->header($mail_id);
$mail->uid = $head[0]->uid;
$mail->loadMatchingObject();
if ($mail->_id && !$mail->text_plain_id) {
	$mail->loadHeaderFromSource($head);
	$mail->loadContentFromSource($pop->getFullBody($mail_id,false,false,true));
	$mail->date_read = mbDateTime();
  $mail->user_id = $user->_id;


	//text plain
	if($mail->_text_plain) {
		$textP = new CContentAny();
		$textP->content = $mail->_text_plain;
		if ($msg = $textP->store()) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
		}
		$mail->text_plain_id = $textP->_id;
	}

	//text html
	if($mail->_text_html) {
    $textH = new CContentHTML();
	    $text = new CMbXMLDocument();
      $text = $text->sanitizeHTML($mail->_text_html); //cleanup
	  $textH->content = $text;

		if ($msg = $textH->store()) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
		} else {
      $mail->text_html_id = $textH->_id;
    }

	}

  $msg = $mail->store();
  if($msg) {
  	CAppUI::setMsg($msg, UI_MSG_ERROR);
  }

}

$mail->loadRefsFwd();
$pop->close();



mbTrace($mail->_text_html->content);
//Smarty
$smarty = new CSmartyDP();
$smarty->assign("mail", $mail);
$smarty->display("ajax_open_pop_email.tpl");

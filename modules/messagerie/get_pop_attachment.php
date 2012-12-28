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
$part = CValue::get("part");


$log_pop = new CSourcePOP();
$log_pop->name = "user-pop-".$user->_id;
$log_pop->loadMatchingObject();

if(!$log_pop) {
  CAppUI::stepAjax("Source POP indisponible",UI_MSG_ERROR);
}

if (!$mail_id) {
  CAppUI::stepAjax("CSourcePOP-error-mail_id",UI_MSG_ERROR);
}

$mail = new CUserMail();
$mail->_id = $mail_id;
$mail->loadMatchingObject();


$pop = new CPop($log_pop);
$pop->open();

  $attach  = new CMailAttachments();
  $struct = $pop->structure($mail->uid);
  $parts = explode(".",$part);  //recursive parts
  foreach($parts as $key=>$value) {
    $struct = $struct->parts[$value];
  }

  $attach->loadFromHeader($struct);
  $attach->part = $part;
  $attach->loadContentFromPop($pop->openPart($mail->uid,$attach->getpartDL()));
  echo "<img src=\"data:image/jpeg;base64,".$attach->content."\" alt=\"\" />";

  mbTrace($attach);


//$attachment->content = $pop->openPart($mail->uid,$part);

$pop->close();

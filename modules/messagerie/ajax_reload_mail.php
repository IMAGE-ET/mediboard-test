<?php /** $Id$ **/

/**
 * @package    Mediboard
 * @subpackage messagerie
 * @version    $Revision$
 * @author     SARL OpenXtrem
 */

CCanDo::checkRead();
$mail_id = CValue::get("mail_id");

//mail
$mail = new CUserMail();
$mail->load($mail_id);

//account
$account = new CSourcePOP();
$account->load($mail->account_id);

//let's go
$pop = new CPop($account);
$pop->open();


$pop->close();


echo CAppUI::getMsg();
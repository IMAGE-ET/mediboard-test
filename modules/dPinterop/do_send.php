<?php /* $Id: do_send.php,v 1.1 2005/08/23 16:14:37 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision: 1.1 $
* @author Romain OLLIVIER
*/

global $AppUI, $msg;

require_once( $AppUI->getLibraryClass('phpmailer/class.phpmailer'));

$mail = new PHPMailer();

$mail->From     = $_POST["from"];
$mail->FromName = "Mediboard Internal Message";
$mail->Host     = "mail.openxtrem.com";
$mail->Mailer   = "smtp";
$mail->AddAddress($_POST["to"], "");
$mail->Subject = $_POST["subject"];
$mail->Body    = $_POST["body"];
$mail->AddAttachment("/var/www/icons/alert.black.png", "piece_jointe.png");

if(!$mail->Send())
  echo "Error";

$AppUI->redirect();
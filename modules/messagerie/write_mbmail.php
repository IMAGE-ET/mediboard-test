<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPportail
* @version $Revision$
* @author Thomas Despoix
*/

CCanDo::checkRead();
$user = CUser::get();
$mbmail = new CMbMail();
$mbmail->from    = $user->_id;
$mbmail->to      = CValue::get("to");
$mbmail->subject = CValue::get("subject");
$mbmail->load(CValue::getOrSession("mbmail_id"));
$mbmail->loadRefsFwd();

// Vérifiction de la première lecture par le destinataire
if ($mbmail->to == $user->_id && $mbmail->date_sent && ! $mbmail->date_read) {
  $mbmail->date_read = mbDateTime();
  $mbmail->store();
}

if ($mbmail->to) {
  $mbmail->loadRefUserTo();
}

// Historique des messages avec le destinataire
$where = array();
$where[] = "(mbmail.from = '$mbmail->from' AND mbmail.to = '$mbmail->to')".
           "OR (mbmail.from = '$mbmail->to' AND mbmail.to = '$mbmail->from')";

$historique = $mbmail->loadList($where, "date_sent DESC", "20");
CMbObject::massLoadFwdRef($historique, "from");
CMbObject::massLoadFwdRef($historique, "to");

foreach ($historique as $_mail) {
  $_mail->loadRefUserFrom(1);
  $_mail->loadRefUserTo(1);
}

// Initialisation de CKEditor
$templateManager = new CTemplateManager();
$templateManager->editor = "ckeditor";
$templateManager->simplifyMode = true;

if ($mbmail->date_sent) {
  $templateManager->printMode = true;
}

$templateManager->initHTMLArea();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("mbmail"   , $mbmail);
$smarty->assign("historique", $historique);

$smarty->display("write_mbmail.tpl");

?>
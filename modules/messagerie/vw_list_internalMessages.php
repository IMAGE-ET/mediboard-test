<?php /** $Id$ **/

/**
* @package Mediboard
* @subpackage messagerie
* @version $Revision$
* @author Fabien
*/

CCanDo::checkRead();
$user = CUser::get();

$usermessage = new CUserMessage();

// Liste des messages reçus
$where = array();
$where["to"]        = "= '$user->_id'";
$where["date_sent"] = "IS NOT NULL";
$where["archived"]  = "!= '1'";
$order = "date_sent DESC";
$listInbox = $usermessage->loadList($where, $order);
foreach ($listInbox as &$mail) {
  $mail->loadRefsFwd();
}

// Liste des messages archivés
$where = array();
$where["to"]        = "= '$user->_id'";
$where["date_sent"] = "IS NOT NULL";
$where["archived"]  = "= '1'";
$order = "date_sent DESC";
$listArchived = $usermessage->loadList($where, $order);
foreach ($listArchived as &$mail) {
  $mail->loadRefsFwd();
}

// Liste des messages envoyés
$where = array();
$where["from"]      = "= '$user->_id'";
$where["date_sent"] = "IS NOT NULL";
$order = "date_sent DESC";
$listSent = $usermessage->loadList($where, $order);
foreach ($listSent as &$mail) {
  $mail->loadRefsFwd();
}

// Liste des brouillons
$where = array();
$where["from"]      = "= '$user->_id'";
$where["date_sent"] = "IS NULL";
$order = "date_sent DESC";
$listDraft = $usermessage->loadList($where, $order);
foreach ($listDraft as &$mail) {
  $mail->loadRefsFwd();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listInbox"   , $listInbox);
$smarty->assign("listArchived", $listArchived);
$smarty->assign("listSent"    , $listSent);
$smarty->assign("listDraft"   , $listDraft);

$smarty->display("vw_list_usermessages.tpl");

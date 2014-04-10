<?php /** $Id$ **/

/**
* @package Mediboard
* @subpackage messagerie
* @version $Revision$
* @author Fabien
*/

CCanDo::checkRead();
$user = CMediusers::get();

$usermessage = new CUserMessageDest();

// Liste des messages reçus
$where = array();
$where["to_user_id"]   = "= '$user->_id'";
$where["datetime_sent"] = "IS NOT NULL";
$where["archived"]  = "!= '1'";
/** @var CUserMessageDest[] $listInbox */
$listInbox = $usermessage->countList($where);
$where["datetime_read"] = "IS NULL";
$listInboxUnread = $usermessage->countList($where);

// Liste des messages archivés
$where = array();
$where["to_user_id"]   = "= '$user->_id'";
$where["datetime_sent"] = "IS NOT NULL";
$where["archived"]  = "= '1'";
/** @var CUserMessageDest[] $listArchived */
$listArchived = $usermessage->countList($where);

// Liste des messages envoyés
$where = array();
$where["from_user_id"]   = "= '$user->_id'";
$where["datetime_sent"] = "IS NOT NULL";
/** @var CUserMessageDest[] $listSent */
$listSent = $usermessage->countList($where);

// Liste des brouillons
$usermessage = new CUserMessage();
$where = array();
$where["creator_id"]   = "= '$user->_id'";
/** @var CUserMessage[] $listDraft */
$listDraft = $usermessage->loadList($where);
foreach ($listDraft as $key => $_mail) {
  $dests = $_mail->loadRefDests();
  foreach ($dests as $_dest) {
    if ($_dest->datetime_sent) {
      unset($listDraft[$key]);
      continue 2;
    }
  }
}
$countListDraft = count($listDraft);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("user"            , $user);
$smarty->assign("listInbox"       , $listInbox);
$smarty->assign("listInboxUnread" , $listInboxUnread);
$smarty->assign("listArchived"    , $listArchived);
$smarty->assign("listSent"        , $listSent);
$smarty->assign("listDraft"       , $countListDraft);

$smarty->display("vw_list_usermessages.tpl");

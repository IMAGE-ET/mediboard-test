<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $canRead, $canEdit, $m;

global $mp_status;

if (!$canRead) {
  $AppUI->redirect("m=system&a=access_denied");
}

// Récupération du message à ajouter/éditer
$message = new CMessage;
$message->load(mbGetValueFromGetOrSession("message_id"));
$message->loadRefs();

// Récupération de la liste des messages
$filter_status = mbGetValueFromGetOrSession("filter_status");
$messages = new CMessage;
$messages = $messages->loadPublications($filter_status);

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("message"      , $message      );
$smarty->assign("messages"     , $messages     );
$smarty->assign("mp_status"    , $mp_status    );
$smarty->assign("filter_status", $filter_status);

$smarty->display("view_messages.tpl");

?>
<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision$
* @author Thomas Despoix
*/

global $can;

$can->needsRead();

// Récupération du message à ajouter/éditer
$message = new CMessage;
$message->deb = mbDateTime();
$message->load(mbGetValueFromGetOrSession("message_id"));
$message->loadRefs();

// Récupération de la liste des messages
$filter = new CMessage;
$filter->_status = mbGetValueFromGetOrSession("_status");
$messages = $filter->loadPublications($filter->_status);
foreach ($messages as $_message) {
	$_message->loadRefs();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("message" , $message      );
$smarty->assign("messages", $messages     );
//$smarty->assign("modules" , CModule::$installed);
$smarty->assign("filter"  , $filter);

$smarty->display("view_messages.tpl");

?>
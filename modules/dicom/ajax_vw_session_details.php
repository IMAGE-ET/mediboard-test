<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage 
 * @version $Revision$
 * @author SARL OpenXtrem
 */

CCanDo::checkRead();

$session_guid = CValue::get("session_guid");

$session = CMbObject::loadFromGuid($session_guid);

$session->loadRefActor();
$session->loadRefGroups();
$session->loadRefDicomExchange();
$session->updateFormFields();
$session->loadMessages();

$smarty = new CSmartyDP();
$smarty->assign("session", $session);
$smarty->display("inc_session_details.tpl");

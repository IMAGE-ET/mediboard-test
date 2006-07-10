<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision$
* @author Thomas Despoix
*/

require_once($AppUI->getModuleClass("system", "message"));
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CMessage", "message_id");
$do->createMsg = "Message créé";
$do->modifyMsg = "Message modifié";
$do->deleteMsg = "Message supprimé";
$do->doIt();
?>
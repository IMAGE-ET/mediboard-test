<?php /* $Id: do_message_aed.php,v 1.1 2006/02/06 18:26:14 mytto Exp $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: 1.1 $
* @author Thomas Despoix
*/

require_once($AppUI->getModuleClass("system", "message"));
require_once($AppUI->getSystemClass('doobjectaddedit'));

$do = new CDoObjectAddEdit("CMessage", "message_id");
$do->createMsg = "Message créé";
$do->modifyMsg = "Message modifié";
$do->deleteMsg = "Message supprimé";
$do->doIt();
?>
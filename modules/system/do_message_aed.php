<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision$
* @author Thomas Despoix
*/

$do = new CDoObjectAddEdit("CMessage", "message_id");
$do->createMsg = "Message créé";
$do->modifyMsg = "Message modifié";
$do->deleteMsg = "Message supprimé";
$do->doIt();
?>
<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPportail
 *  @version $Revision: $
 *  @author Fabien
 */

global $AppUI;

$do = new CDoObjectAddEdit("CForumMessage", "forum_message_id");
$do->createMsg = "Message créé";
$do->modifyMsg = "Message modifié";
$do->deleteMsg = "Message supprimé";
$do->doIt();

?>

<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPportail
 *  @version $Revision: $
 *  @author Fabien
 */

global $AppUI;

$do = new CDoObjectAddEdit("CForumThread", "forum_thread_id");
$do->createMsg = "Thread créé";
$do->modifyMsg = "Thread modifié";
$do->deleteMsg = "Thread supprimé";
$do->doIt();

?>

<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPportail
 *  @version $Revision: $
 *  @author Fabien
 */

global $AppUI;

$do = new CDoObjectAddEdit("CForumTheme", "forum_theme_id");
$do->createMsg = "Thème créé";
$do->modifyMsg = "Thème modifié";
$do->deleteMsg = "Thème supprimé";
$do->doIt();

?>

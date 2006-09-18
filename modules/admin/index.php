<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage admin
* @version $Revision$
* @author Romain Ollivier
*/

$tabs = array();
$tabs[] = array("vw_edit_users", "Utilisateurs", 0);
$tabs[] = array("edit_perms", "Gestion des droits", 1);
$default = "vw_edit_users";

$index = new CTabIndex($tabs, $default);
$index->show();

?>
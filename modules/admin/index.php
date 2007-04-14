<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage admin
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_edit_users", "Utilisateurs"           , TAB_READ);
$module->registerTab("edit_perms"   , "Gestion des droits"     , TAB_EDIT);
$module->registerTab("edit_prefs"   , null, TAB_EDIT);

?>
<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage admin
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_edit_users", null, TAB_READ);
$module->registerTab("edit_perms"   , null, TAB_EDIT);
$module->registerTab("edit_prefs"   , null, TAB_EDIT);

?>
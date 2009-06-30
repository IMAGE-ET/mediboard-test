<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_edit_users", null, TAB_READ);
$module->registerTab("edit_perms"   , null, TAB_EDIT);
$module->registerTab("edit_prefs"   , null, TAB_EDIT);
$module->registerTab("vw_all_perms" , null, TAB_EDIT);

?>
<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_edit_users", TAB_READ);
$module->registerTab("edit_perms"   , TAB_EDIT);
$module->registerTab("edit_prefs"   , TAB_EDIT);
$module->registerTab("vw_all_perms" , TAB_EDIT);

?>
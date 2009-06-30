<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI;

$tempUserName    = mbGetValueFromPost("temp_user_name", "");
$permission_user = mbGetValueFromPost("permission_user", "");
$delPermissions  = mbGetValueFromPost("delPerms", false);

// pull user_id for unique user_username (templateUser)
$tempUser = new CUser;
$where = array();
$where["user_username"] = "= '$tempUserName'";
$tempUser->loadObject($where);

$user = new CUser;
$user->user_id = $permission_user;
$msg = $user->copyPermissionsFrom($tempUser->user_id, $delPermissions);

$AppUI->setMsg("Permissions");
$AppUI->setMsg($msg ? $msg : "copied from template", $msg ? UI_MSG_ERROR : UI_MSG_OK, true);
$AppUI->redirect();

?>
<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$tempUserName    = CValue::post("temp_user_name", "");
$permission_user = CValue::post("permission_user", "");
$delPermissions  = CValue::post("delPerms", false);

// pull user_id for unique user_username (templateUser)
$tempUser = new CUser;
$where = array();
$where["user_username"] = "= '$tempUserName'";
$tempUser->loadObject($where);

$user = new CUser;
$user->user_id = $permission_user;
$msg = $user->copyPermissionsFrom($tempUser->user_id, $delPermissions);

CAppUI::setMsg("Permissions");
CAppUI::setMsg($msg ? $msg : "copied from template", $msg ? UI_MSG_ERROR : UI_MSG_OK, true);
CAppUI::redirect();

?>
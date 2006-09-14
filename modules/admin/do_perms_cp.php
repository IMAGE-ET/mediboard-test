<?php

global $AppUI;

$tempUserName    = mbGetValueFromPost("temp_user_name", "");
$permission_user = mbGetValueFromPost("permission_user", "");
$delPermissions  = mbGetValueFromPost("delPerms", false);

// pull user_id for unique user_username (templateUser)
$sql = "SELECT user_id FROM users WHERE user_username = '$tempUserName'";
$res = db_loadList($sql);
$tempUserId = $res[0]["user_id"];

$user = new CUser;
$user->user_id = $permission_user;
$msg = $user->copyPermissionsFrom($tempUserId, $delPermissions);

$AppUI->setMsg("Permissions");
$AppUI->setMsg($msg ? $msg : "copied from template", $msg ? UI_MSG_ERROR : UI_MSG_OK, true);
$AppUI->redirect();

?>
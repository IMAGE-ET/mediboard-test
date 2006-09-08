<?php /* ADMIN $Id$ */
global $AppUI, $user_id, $canEdit, $tab, $mb_module_active;

// Get the installed modules

// Create fake 'all' module
$moduleAll = new CMbModule();
$moduleAll->mod_name = "all";
$modules["all"] = $moduleAll;

$modules = array_merge($modules, CMbModule::getInstalled());

$pgos["admin"] = array(
  "table"       => "users", 
  "table_alias" => "us", 
  "id_field"    => "user_id",
  "name_field"  => "user_username"
);

// Should be externalized in each module
if (array_key_exists("mediusers", $modules)) {
  $pgos["mediusers"] = array(
    "table"       => "functions_mediboard", 
    "table_alias" => "fu", 
    "id_field"    => "function_id",
    "name_field"  => "text"
  );
}
if (array_key_exists("dPetablissement", $modules)) {
  $pgos["dPetablissement"] = array(
    "table"       => "groups_mediboard", 
    "table_alias" => "gr", 
    "id_field"    => "group_id",
    "name_field"  => "text"
  );
}
if (array_key_exists("dPmateriel", $modules)) {
  $pgos["dPmateriel"] = array(
    "table"       => "materiel_category", 
    "table_alias" => "mat", 
    "id_field"    => "category_id",
    "name_field"  => "category_name"
  );
}

$permItemValues = array(
  PERM_EDIT => "ReadWrite",
  PERM_DENY => "Deny",
  PERM_READ => "ReadOnly"
);

$permModuleValues = array(
  HIDDEN_READNONE_EDITNONE => "HiddenReadNoneEditNone",
  HIDDEN_READNONE_EDITALL  => "HiddenReadNoneEditAll",
  HIDDEN_READALL_EDITNONE  => "HiddenReadAllEditNone",
  HIDDEN_READALL_EDITALL   => "HiddenReadAllEditAll",
  VISIBLE_READNONE_EDITNONE=> "VisibleReadNoneEditNone",
  VISIBLE_READNONE_EDITALL => "VisibleReadNoneEditAll",
  VISIBLE_READALL_EDITNONE => "VisibleReadAllEditNone",
  VISIBLE_READALL_EDITALL  => "VisibleReadAllEditAll"
);

// Get existing user permissions
$sql = "SELECT ";

foreach($pgos as $module => $pgo) {
  $sql .= "\n{$pgo['table_alias']}.{$pgo['id_field']}, {$pgo['table_alias']}.{$pgo['name_field']} AS {$pgo['table_alias']}{$pgo['name_field']},";
}  
 
$sql .= "\np.permission_item, p.permission_id, p.permission_grant_on, p.permission_value" .
  "\nFROM permissions p";

foreach($pgos as $module => $pgo) {
  $sql .= "\nLEFT JOIN {$pgo['table']} {$pgo['table_alias']} " .
    "ON {$pgo['table_alias']}.{$pgo['id_field']} = p.permission_item " .
    "AND '{$module}' = p.permission_grant_on ";
}  

$sql .= "\nWHERE p.permission_user = $user_id ORDER BY p.permission_grant_on, p.permission_item";

$res = db_exec($sql);

// Get user perms
$userPerms = array();
while ($row = db_fetch_assoc( $res )) {
  // Mediboard version 
  $module = $row["permission_grant_on"];
  $name_field = @$pgos[$module]["table_alias"].@$pgos[$module]["name_field"];
  $item_name = $row["permission_item"] == PERM_ALL ? "module" : $row[$name_field];
  $perm_value_name = $row["permission_item"] == PERM_ALL ? $permModuleValues[$row["permission_value"]] : $permItemValues[$row["permission_value"]];
  
  $userPerms[] = array (
    "perm_id" => $row["permission_id"],
    "perm_item" => $row["permission_item"],
    "perm_value" => $row["permission_value"],
    "perm_module" => $module,
    "perm_item_name" => $item_name,
    "perm_value_name" => $perm_value_name
  );
}


// Pull list of users for permission duplication from template user
// prevent from copying from users with no permissions
$sql = "SELECT DISTINCT(user_id), user_username " .
  "FROM users, permissions " .
  "WHERE user_id != $user_id " .
  "AND permission_user = user_id " .
  "ORDER BY user_username";
$res = db_loadList($sql);

// Creates the array of other users
$otherUsers = array();
foreach ( $res as $row ) {
	$otherUsers[$row['user_username']]= $row['user_username'];
}

// Create Perm object to Edit
$permSel = new CPermission;
$permSel->load(mbGetValueFromGetOrSession("perm_id"));

// Template creation
require_once( $AppUI->getSystemClass ("smartydp" ) );
$smarty = new CSmartyDP(1);

$smarty->assign("user_id", $user_id);
$smarty->assign("permItemValues", $permItemValues);
$smarty->assign("permModuleValues", $permModuleValues);
$smarty->assign("pgos", $pgos);
$smarty->assign("userPerms", $userPerms);
$smarty->assign("permSel", $permSel);
$smarty->assign("otherUsers", $otherUsers);
$smarty->assign("modules", $modules);

$smarty->display("vw_usr_perms.tpl");
?>

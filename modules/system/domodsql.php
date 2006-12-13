<?php /* SYSTEM $Id$ */
##
## Activate or move a module entry
##

global $AppUI;

$cmd      = mbGetValueFromGet("cmd", "0");
$mod_id   = intval(mbGetValueFromGet("mod_id", "0"));
$mod_name = mbGetValueFromGet("mod_name", "0");

$module = new CModule();
if($mod_id) {
	$module->load($mod_id);
} else {
  $module->mod_version = "all";
	$module->mod_name    = $mod_name;
}

$ok = @include_once($AppUI->cfg["root_dir"]."/modules/$module->mod_name/setup.php");

if (!$ok) {
	if ($module->mod_type != "core") {
		$AppUI->setMsg("Module setup file could not be found", UI_MSG_ERROR);
		$AppUI->redirect();
	}
}
$setupclass = "CSetup".$config["mod_name"];
if (! $setupclass) {
  if ($module->mod_type != "core") {
    $AppUI->setMsg("Module does not have a valid setup class defined", UI_MSG_ERROR);
    $AppUI->redirect();
  }
}
else {
  $setup = new $setupclass();
}

switch ($cmd) {
	case "moveup":
	case "movedn":
		$module->move($cmd);
		$AppUI->setMsg("Module re-ordered", UI_MSG_OK);
		break;
	case "toggle":
	// just toggle the active state of the table entry
		$module->mod_active = 1 - $module->mod_active;
		$module->store();
		$AppUI->setMsg("Module state changed", UI_MSG_OK);
		break;
	case "toggleMenu":
	  // just toggle the active state of the table entry
		$module->mod_ui_active = 1 - $module->mod_ui_active;
		$module->store();
    $AppUI->setMsg("Module menu state changed", UI_MSG_OK);
		break;
	case "remove":
    $success = $setup->remove();
    if($success !== null){
      $module->remove();
      $AppUI->setMsg("Module removed", $success ? UI_MSG_OK : UI_MSG_ERROR, true);
    }
		break;
  case "install":
    $newVersion = $setup->upgrade($module->mod_version);
    if ($newVersion) {
      $module->bind( $config );
      $topVersion = $module->mod_version;
      $module->mod_version = $newVersion;
      $module->install();
      if ($newVersion == $topVersion)
        $AppUI->setMsg("Installation de '$module->mod_name' à la version $newVersion", UI_MSG_OK, true);
      else
        $AppUI->setMsg("Installation de '$module->mod_name' à la version $newVersion sur $topVersion", UI_MSG_WARNING, true);
    } else {
      $AppUI->setMsg("Module '$module->mod_name' non installé", UI_MSG_ERROR, true);
    }
    break;
	case "upgrade":
    $newVersion = $setup->upgrade($module->mod_version);
		if ($newVersion) {
			$module->bind($config);
      $topVersion = $module->mod_version;
      $module->mod_version = $newVersion;
			$module->store();
      if ($newVersion == $topVersion)
			  $AppUI->setMsg("Mise à jour de '$module->mod_name' à la version $newVersion", UI_MSG_OK, true);
      else
        $AppUI->setMsg("Mise à jour de '$module->mod_name' à la version $newVersion sur $topVersion", UI_MSG_WARNING, true);
		} else {
			$AppUI->setMsg("Module '$module->mod_name' non mis à jour", UI_MSG_ERROR, true);
		}
		break;
	case "configure":
		if ($setup->configure()) { 	//returns true if configure succeeded
		}
		else {
			$AppUI->setMsg("Module configuration failed", UI_MSG_ERROR);
		}
		break;
	default:
		$AppUI->setMsg("Unknown Command", UI_MSG_ERROR);
		break;
}
$AppUI->redirect();
?>

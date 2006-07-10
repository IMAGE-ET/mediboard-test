<?php /* SYSTEM $Id$ */
##
## Activate or move a module entry
##

global $AppUI;

$cmd = mbGetValueFromGet("cmd", "0");
$mod_id = intval(mbGetValueFromGet("mod_id", "0"));
$mod_directory = mbGetValueFromGet("mod_directory", "0");

require_once($Appui->getModuleClass("system", "system"));

$obj = new CModule();
if ($mod_id) {
	$obj->load( $mod_id );
} else {
  $obj->mod_version = "all";
	$obj->mod_directory = $mod_directory;
}

$ok = @include_once($AppUI->cfg["root_dir"]."/modules/$obj->mod_directory/setup.php");

if (!$ok) {
	if ($obj->mod_type != "core") {
		$AppUI->setMsg("Module setup file could not be found", UI_MSG_ERROR);
		$AppUI->redirect();
	}
}
$setupclass = $config["mod_setup_class"];
if (! $setupclass) {
  if ($obj->mod_type != "core") {
    $AppUI->setMsg("Module does not have a valid setup class defined", UI_MSG_ERROR);
    $AppUI->redirect();
  }
}
else
  $setup = new $setupclass();

switch ($cmd) {
	case "moveup":
	case "movedn":
		$obj->move($cmd);
		$AppUI->setMsg("Module re-ordered", UI_MSG_OK);
		break;
	case "toggle":
	// just toggle the active state of the table entry
		$obj->mod_active = 1 - $obj->mod_active;
		$obj->store();
		$AppUI->setMsg("Module state changed", UI_MSG_OK);
		break;
	case "toggleMenu":
	// just toggle the active state of the table entry
		$obj->mod_ui_active = 1 - $obj->mod_ui_active;
		$obj->store();
		$AppUI->setMsg("Module menu state changed", UI_MSG_OK);
		break;
	case "remove":
    $msg = $setup->remove();
		$AppUI->setMsg($msg, UI_MSG_ALERT);
    if(!$msg) {
      $obj->remove();
      $AppUI->setMsg("Module removed", UI_MSG_ALERT);
    }
		break;
  case "install":
    $newVersion = $setup->upgrade($obj->mod_version);
    if ($newVersion) {
      $obj->bind( $config );
      $topVersion = $obj->mod_version;
      $obj->mod_version = $newVersion;
      $obj->install();
      if ($newVersion == $topVersion)
        $AppUI->setMsg("Installation de '$obj->mod_name' à la version $newVersion", UI_MSG_OK);
      else
        $AppUI->setMsg("Installation de '$obj->mod_name' à la version $newVersion sur $topVersion", UI_MSG_WARNING);
    } else {
      $AppUI->setMsg("Module '$obj->mod_name' non installé", UI_MSG_ERROR);
    }
    break;
	case "upgrade":
    $newVersion = $setup->upgrade($obj->mod_version);
		if ($newVersion) {
			$obj->bind($config);
      $topVersion = $obj->mod_version;
      $obj->mod_version = $newVersion;
			$obj->store();
      if ($newVersion == $topVersion)
			  $AppUI->setMsg("Mise à jour de '$obj->mod_name' à la version $newVersion", UI_MSG_OK);
      else
        $AppUI->setMsg("Mise à jour de '$obj->mod_name' à la version $newVersion sur $topVersion", UI_MSG_WARNING);
		} else {
			$AppUI->setMsg("Module '$obj->mod_name' non mis à jour", UI_MSG_ERROR);
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

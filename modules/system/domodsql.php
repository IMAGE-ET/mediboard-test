<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * Activate or move a module entry
 */

global $AppUI;

$cmd      = mbGetValueFromGet("cmd", "0");
$mod_id   = intval(mbGetValueFromGet("mod_id", "0"));
$mod_name = mbGetValueFromGet("mod_name", "0");

$module = new CModule();
if ($mod_id) {
	$module->load($mod_id);
}
else {
  $module->mod_version = "all";
	$module->mod_name    = $mod_name;
}

if (!class_exists($setupclass = "CSetup$module->mod_name")) {
  if ($module->mod_type != "core") {
    $AppUI->setMsg("CModule-msg-no-setup", UI_MSG_ERROR);
    $AppUI->redirect();
  }
}

if ($module->mod_type == "core" && in_array($cmd, array("remove", "install", "toggle"))) {
  $AppUI->setMsg("Core modules can't be uninstalled or disactivated", UI_MSG_ERROR);
  $AppUI->redirect();
}

$setup = new $setupclass;

switch ($cmd) {
	case "moveup":
	case "movedn":
	$module->move($cmd);
	$AppUI->setMsg("CModule-msg-reordered", UI_MSG_OK);
	break;
		
	case "toggle":
	// just toggle the active state of the table entry
	$module->mod_active = 1 - $module->mod_active;
	$module->store();
	$AppUI->setMsg("CModule-msg-state-changed", UI_MSG_OK);
	break;

	case "toggleMenu":
  // just toggle the active state of the table entry
	$module->mod_ui_active = 1 - $module->mod_ui_active;
	$module->store();
   $AppUI->setMsg("CModule-msg-state-changed", UI_MSG_OK);
	break;

	case "remove":
  $success = $setup->remove();
  if($success !== null){
    $module->remove();
    $AppUI->setMsg("CModule-msg-removed", $success ? UI_MSG_OK : UI_MSG_ERROR, true);
  }
  break;

  case "install":
  if ($module->mod_version = $setup->upgrade($module->mod_version)) {
    $module->mod_type = $setup->mod_type;
    $module->install();
    
    if ($setup->mod_version == $module->mod_version) {
      $AppUI->setMsg("Installation de '$module->mod_name' à la version $setup->mod_version", UI_MSG_OK, true);
    }
    else {
      $AppUI->setMsg("Installation de '$module->mod_name' à la version $module->mod_version sur $setup->mod_version", UI_MSG_WARNING, true);
    }
  } 
  else {
    $AppUI->setMsg("Module '$module->mod_name' non installé", UI_MSG_ERROR, true);
  }
  
	// In case the setup has added some user prefs
  $AppUI->reloadPrefs();
  break;

  case "upgrade":
  if ($module->mod_version = $setup->upgrade($module->mod_version)) {
    $module->mod_type = $setup->mod_type;
    $module->store();

    if ($setup->mod_version == $module->mod_version) {
      $AppUI->setMsg("Installation de '$module->mod_name' à la version $setup->mod_version", UI_MSG_OK, true);
    }
    else {
      $AppUI->setMsg("Installation de '$module->mod_name' à la version $module->mod_version sur $setup->mod_version", UI_MSG_WARNING, true);
    }
	} 
	else {
		$AppUI->setMsg("Module '$module->mod_name' non mis à jour", UI_MSG_ERROR, true);
	}

	// In case the setup has added some user prefs
  $AppUI->reloadPrefs();
	break;

	case "configure":
	if ($setup->configure()) { 	//returns true if configure succeeded
	}
	else {
		$AppUI->setMsg("CModule-msg-config-failed", UI_MSG_ERROR);
	}
	break;

	default:
	$AppUI->setMsg("Unknown Command", UI_MSG_ERROR);
	break;
}

$AppUI->redirect();
?>

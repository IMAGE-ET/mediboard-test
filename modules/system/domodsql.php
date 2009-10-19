<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * Activate or move a module entry
 */

global $can;

$can->needsAdmin();

$cmd      = mbGetValueFromGet("cmd");
$mod_id   = mbGetValueFromGet("mod_id");
$mod_name = mbGetValueFromGet("mod_name");

$module = new CModule();
if ($mod_id) {
  $module->load($mod_id);
  $module->checkModuleFiles();
}
else {
  $module->mod_version = "all";
  $module->mod_name    = $mod_name;
}

if (!class_exists($setupclass = "CSetup$module->mod_name")) {
  if ($module->mod_type != "core" && !$module->_files_missing) {
    CAppUI::setMsg("CModule-msg-no-setup", UI_MSG_ERROR);
    CAppUI::redirect();
  }
}

if ($module->mod_type == "core" && in_array($cmd, array("remove", "install", "toggle"))) {
  CAppUI::setMsg("Core modules can't be uninstalled or disactivated", UI_MSG_ERROR);
  CAppUI::redirect();
}

if (!$module->_files_missing) {
  $setup = new $setupclass;
}

switch ($cmd) {
	case "moveup":
	case "movedn":
	$module->move($cmd);
	CAppUI::setMsg("CModule-msg-reordered", UI_MSG_OK);
	break;
		
	case "toggle":
	// just toggle the active state of the table entry
	$module->mod_active = 1 - $module->mod_active;
	$module->store();
	CAppUI::setMsg("CModule-msg-state-changed", UI_MSG_OK);
	break;

	case "toggleMenu":
  // just toggle the active state of the table entry
	$module->mod_ui_active = 1 - $module->mod_ui_active;
	$module->store();
   CAppUI::setMsg("CModule-msg-state-changed", UI_MSG_OK);
	break;

	case "remove":
  if (!$module->_files_missing) {
    $success = $setup->remove();
  } else {
    $success = true;
  }
  if($success !== null){
    $module->remove();
    CAppUI::setMsg("CModule-msg-removed", $success ? UI_MSG_OK : UI_MSG_ERROR, true);
  }
  break;

  case "install":
  if ($module->mod_version = $setup->upgrade($module->mod_version)) {
    $module->mod_type = $setup->mod_type;
    $module->install();
    
    if ($setup->mod_version == $module->mod_version) {
      CAppUI::setMsg("Installation de '$module->mod_name' à la version $setup->mod_version", UI_MSG_OK, true);
    }
    else {
      CAppUI::setMsg("Installation de '$module->mod_name' à la version $module->mod_version sur $setup->mod_version", UI_MSG_WARNING, true);
    }
  } 
  else {
    CAppUI::setMsg("Module '$module->mod_name' non installé", UI_MSG_ERROR, true);
  }
  
	// In case the setup has added some user prefs
  CAppUI::reloadPrefs();
  break;

  case "upgrade":
  if ($module->mod_version = $setup->upgrade($module->mod_version)) {
    $module->mod_type = $setup->mod_type;
    $module->store();

    if ($setup->mod_version == $module->mod_version) {
      CAppUI::setMsg("Installation de '$module->mod_name' à la version $setup->mod_version", UI_MSG_OK, true);
    }
    else {
      CAppUI::setMsg("Installation de '$module->mod_name' à la version $module->mod_version sur $setup->mod_version", UI_MSG_WARNING, true);
    }
	} 
	else {
		CAppUI::setMsg("Module '$module->mod_name' non mis à jour", UI_MSG_ERROR, true);
	}

	// In case the setup has added some user prefs
  CAppUI::reloadPrefs();
	break;

	case "configure":
	if (!$setup->configure()) { //returns true if configure succeeded
		CAppUI::setMsg("CModule-msg-config-failed", UI_MSG_ERROR);
	}
	break;

	default:
	CAppUI::setMsg("Unknown Command", UI_MSG_ERROR);
	break;
}

CAppUI::redirect();
?>

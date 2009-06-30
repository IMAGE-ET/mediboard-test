<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI;

$ds = CSQLDataSource::get("std");
$a = mbGetValueFromGet("a",null);
$del = isset($_POST["del"]) ? $_POST["del"] : 0;

$obj = new CPreferences();
$obj->pref_user = isset($_POST["pref_user"]) ? $_POST["pref_user"] : 0;

foreach ($_POST["pref_name"] as $name => $value) {
	$obj->pref_name = $name;
	$obj->pref_value = stripslashes_deep($value);

	// prepare (and translate) the module name ready for the suffix
	if ($del) {
		if ($msg = $obj->delete()) {
			$AppUI->setMsg($msg, UI_MSG_ERROR);
		} else {
			$AppUI->setMsg("CPreferences-msg-delete", UI_MSG_ALERT);
		}
	} else {
		if ($msg = $obj->store()) {
			$AppUI->setMsg($msg, UI_MSG_ERROR);
		} else {
			$AppUI->setMsg("CPreferences-msg-modify", UI_MSG_OK);
		}
	}
}

// Reload user preferences
if ($obj->pref_user) {
  $AppUI->loadPrefs($AppUI->user_id);
}

// Redirect
if ($a){
  $AppUI->defaultRedirect = "m=$m&a=$a&user_id=".$_POST["pref_user"];
  $AppUI->state["SAVEDPLACE"] = null;
  $AppUI->redirect();
}
else {
  echo $AppUI->getMsg();
  CApp::rip();
}

?>

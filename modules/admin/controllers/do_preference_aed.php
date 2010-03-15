<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI;

$prefs = CValue::post("pref", array());
$user_id = CValue::post("user_id", 0);

// @todo: voir à utiliser CDoObjectAddEdit
foreach ($prefs as $key => $value) {
	$pref = new CPreferences();
	$pref->user_id = $user_id;
	$pref->key = $key;
  $pref->loadMatchingObject();
  
	$pref->value = stripslashes($value);

	if ($msg = $pref->store()) {
		CAppUI::setMsg($msg, UI_MSG_ERROR);
	} else {
		CAppUI::setMsg("CPreferences-msg-modify", UI_MSG_OK);
	}
}

// Reload user preferences
if ($pref->user_id) {
  CAppUI::loadPrefs($AppUI->user_id);
}

echo CAppUI::getMsg();
CApp::rip();

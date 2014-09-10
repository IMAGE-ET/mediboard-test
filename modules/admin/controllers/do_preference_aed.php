<?php

/**
 * $Id$
 *
 * @category Admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$prefs      = CValue::post("pref", array());
$user_id    = CValue::post("user_id");
$restricted = CValue::post("restricted");

$pref = new CPreferences();
$ds   = $pref->getDS();

// @todo: voir à utiliser CDoObjectAddEdit
foreach ($prefs as $key => $value) {
  $pref = new CPreferences();

  $where = array(
    "user_id" => ($user_id) ? $ds->prepare("= '$user_id'") : "IS NULL",
    "key"     => $ds->prepare("= '$key'")
  );

  $pref->loadObject($where);

  $pref->user_id    = $user_id;
  $pref->key        = $key;
  $pref->value      = stripslashes($value);
  $pref->restricted = ($restricted) ? "1" : "0";

  if ($msg = $pref->store()) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
  }
  else {
    CAppUI::setMsg("CPreferences-msg-modify", UI_MSG_OK);
  }
}

// Reload user preferences
if ($pref->user_id) {
  CAppUI::buildPrefs();
}

if ($redirect = CValue::post("postRedirect")) {
  echo $redirect;
  CAppUI::redirect($redirect);
}
else {
  echo CAppUI::getMsg();
  CApp::rip();
}
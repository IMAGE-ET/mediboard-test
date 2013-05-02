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

CCanDo::checkAdmin();

$key = CValue::get("key");

// Load preferences
$preference = new CPreferences();
$where["key"  ] = "= '$key'";
$where["value"] = "IS NOT NULL";
$preferences = $preference->loadList($where);

// Mass preloading
$users    = CMbObject::massLoadFwdRef($preferences, "user_id");
$profiles = CMbObject::massLoadFwdRef($users, "profile_id");

// Attach preferences to users
foreach ($preferences as $_preference) {
  if (!$_preference->user_id) {
    $default = $_preference;
    continue;
  }
  $users[$_preference->user_id]->_ref_preference = $_preference;
}

// Build profile hierarchy
$hierarchy = array(
  "default" => array()
);

foreach ($users as $_user) {
  if ($_user->profile_id && isset($users[$_user->profile_id])) {
    $hierarchy[$_user->profile_id][] = $_user->_id;
  }
  else {
    $hierarchy["default"][] = $_user->_id;
  }
}


CSQLDataSource::$trace = false;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("key"        , $key);
$smarty->assign("users"      , $users);
$smarty->assign("default"    , $default);
$smarty->assign("hierarchy"  , $hierarchy);

$smarty->display("report_prefs.tpl");

<?php

/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage admin
 * @version    $Revision$
 * @author     SARL OpenXtrem
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkEdit();

$user_id = CCanDo::edit() ? CValue::getOrSession("user_id", "default") : null;
$user    = CUser::get($user_id);
$prof    = $user->profile_id ? CUser::get($user->profile_id) : new CUser();

if ($user_id == "default") {
  $user->_id = null;
}

$prefvalues = array(
  "default"  => CPreferences::get(null, true),
  "template" => $user->profile_id ? CPreferences::get($user->profile_id, true) : array(),
  "user"     => $user->_id !== "" ? CPreferences::get($user->_id, true) : array(),
);

// common sera toujours au debut
$prefs = array(
  "common" => array()
);

// Classement par module et par permission fonctionnelle
CPreferences::loadModules(true);
foreach (CPreferences::$modules as $modname => $prefnames) {
  $module  = CModule::getActive($modname);
  $canRead = $module ? CPermModule::getPermModule($module->_id, PERM_READ, $user_id) : false;

  if ($modname == "common" || $user_id == "default" || $canRead) {
    $prefs[$modname] = array();
    foreach ($prefnames as $prefname) {
      $prefs[$modname][$prefname] = array(
        "default"  => CMbArray::extract($prefvalues["default"], $prefname),
        "template" => CMbArray::extract($prefvalues["template"], $prefname),
        "user"     => CMbArray::extract($prefvalues["user"], $prefname),
      );
    }
  }
}

// Warning: user clone necessary!
// Some module index change $user global
$user_clone = $user;
// Chargement des modules
$modules = CPermModule::getVisibleModules();
foreach ($modules as $module) {
  // Module might not be present
  @include "./modules/$module->mod_name/index.php";
}
$user = $user_clone;

$smarty = new CSmartyDP();
$smarty->assign("user", $user);
$smarty->assign("prof", $prof);
$smarty->assign("user_id", $user_id);
$smarty->assign("modules", $modules);
$smarty->assign("prefs", $prefs);

$smarty->display("vw_edit_functional_perms.tpl");
<?php 

/**
 * $Id$
 *  
 * @category Mediusers
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

// init
$step = 35;
$group = CGroups::loadCurrent();

//filters
$user_id        = CValue::get("user_id");
$page           = intval(CValue::getOrSession('page', 0));
$filter         = CValue::getOrSession("filter",    "");
$type           = CValue::getOrSession("_user_type");
$inactif        = CValue::get("inactif");
$ldap_bound     = CValue::get("ldap_bound", false);
$user_loggable  = CValue::get("user_loggable");
$human          = CValue::get("human",      false);
$robot          = CValue::get("robot",      false);
$locked         = CValue::get("locked");
$function_id    = CValue::get("function_id");
$order_way      = CValue::getOrSession("order_way", "ASC");
$order_col      = CValue::getOrSession("order_col", "function_id");
$user_id        = CValue::getOrSession("user_id");

// search
// Liste des utilisateurs
$mediuser = new CMediusers();

$ljoin = array();
$ljoin["users"]               = "users.user_id = users_mediboard.user_id";
$ljoin["functions_mediboard"] = "functions_mediboard.function_id = users_mediboard.function_id";

$where = array();
$where["functions_mediboard.group_id"] = "= '$group->_id'";


if ($type) {
  if ($type == "ps") {
    $user_types = array("Chirurgien", "Anesthésiste", "Médecin", "Infirmière", "Rééducateur", "Sage Femme", "Diététicien");
    $utypes_flip = array_flip(CUser::$types);

    if (is_array($user_types)) {
      foreach ($user_types as $key => $value) {
        $user_types[$key] = $utypes_flip[$value];
      }

      $where["users.user_type"] = CSQLDataSource::prepareIn($user_types);
    }
  }
  else {
    $where["users.user_type"] = "= '$type'";
  }
}

if ($inactif == "1") {
  $where["users_mediboard.actif"] = "= '0'";
}
if ($inactif == "0") {
  $where["users_mediboard.actif"] = "= '1'";
}

if ($locked == "1") {
  $where["users.user_login_errors"] = " >= '".CAppUI::conf('admin CUser max_login_attempts')."'";
}
if ($locked == "0") {
  $where["users.user_login_errors"] = " < '".CAppUI::conf('admin CUser max_login_attempts')."'";
}

if ($function_id) {
  $where["users_mediboard.function_id"] = " = '$function_id'";
}

if ($ldap_bound) {
  $ldap_tag = CAppUI::conf("admin LDAP ldap_tag");

  if ($ldap_tag) {
    $ljoin["id_sante400"] = "id_sante400.object_id = users.user_id";
    $where["id_sante400.object_class"] = " = 'CUser'";
    $where["id_sante400.tag"] = " = '".$ldap_tag."'";
  }
}


if ($user_loggable) {
  $robots = array();
  $ds  = CSQLDataSource::get("std");
  $tag = CMediusers::getTagSoftware();
  if ($tag) {
    $query = "SELECT users.user_id
            FROM users
            LEFT JOIN id_sante400 ON users.user_id = id_sante400.object_id
            WHERE (id_sante400.object_class = 'CMediusers'
              AND id_sante400.tag = ?)
              OR users.dont_log_connection = '1'
            GROUP BY users.user_id";

    $query = $ds->prepare($query, $tag);
  }
  else {
    $query = "SELECT users.user_id
            FROM users
            WHERE users.dont_log_connection = '1'";
  }
  $robots = $ds->loadColumn($query);

  if ($user_loggable == "robot") {
    if (count($robots)) {
      $where["users.user_id"] = $ds->prepareIn($robots);
    }
    else {
      $where[] = " 1 = 0";
    }
  }
  elseif ($user_loggable == "human") {
    if (count($robots)) {
      $where["users.user_id"] = $ds->prepareNotIn($robots);
    }
  }
}

$order = null;

if ($order_col == "function_id") {
  $order = "functions_mediboard.text $order_way, users.user_last_name ASC, users.user_first_name ASC";
}
if ($order_col == "user_username") {
  $order = "users.user_username $order_way, users.user_last_name ASC, users.user_first_name ASC";
}
if ($order_col == "user_last_name") {
  $order = "users.user_last_name $order_way, users.user_first_name ASC";
}
if ($order_col == "user_first_name") {
  $order = "users.user_first_name $order_way, users.user_last_name ASC";
}
if ($order_col == "user_type") {
  $order = "users.user_type $order_way, users.user_last_name ASC, users.user_first_name ASC";
}

if ($filter) {
  $mediusers = $mediuser->seek($filter, $where, "$page, $step", true, $ljoin, $order);
  $total_mediuser = $mediuser->_totalSeek;
}
else {
  $total_mediuser = $mediuser->countList($where, null, $ljoin);
  $mediusers = $mediuser->loadList($where, $order, "$page, $step", "users_mediboard.user_id", $ljoin);
}


/** @var CMediusers[] $mediusers */

foreach ($mediusers as $_mediuser) {
  $_mediuser->loadRefFunction();
  $_mediuser->loadRefProfile();
  $_mediuser->loadRefUser();
  $_mediuser->_ref_user->isLDAPLinked();
  $_mediuser->getLastLogin();
}


$smarty = new CSmartyDP();
$smarty->assign("mediusers", $mediusers);
$smarty->assign("user_id"            , $user_id);
$smarty->assign("total_mediuser", $total_mediuser);
$smarty->assign("order_col", $order_col);
$smarty->assign("order_way", $order_way);
$smarty->assign("page", $page);
$smarty->display("inc_search_mediusers.tpl");
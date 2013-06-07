<?php

/**
 * View mediusers
 *
 * @category Mediusers
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$page       = intval(CValue::getOrSession('page', 0));
$pro_sante  = CValue::get("pro_sante", false);
$inactif    = CValue::get("inactif", false);
$ldap_bound = CValue::get("ldap_bound", false);
$filter     = CValue::getOrSession("filter", "");
$order_way  = CValue::getOrSession("order_way", "ASC");
$order_col  = CValue::getOrSession("order_col", "function_id");
$user_id    = CValue::getOrSession("user_id");

$step = 25;

// Récupération des fonctions
$group = CGroups::loadCurrent();
$group->loadFunctions();

// Liste des utilisateurs
$mediuser = new CMediusers();

$ljoin = array();
$ljoin["users"] = "users.user_id = users_mediboard.user_id";
$ljoin["functions_mediboard"] = "functions_mediboard.function_id = users_mediboard.function_id";

$where = array();
$where["functions_mediboard.group_id"] = "= '$group->_id'";

// FIXME: utiliser le seek
if ($filter) {

  $filters = explode(" ", $filter);

  $re = "/(\d+)\s*(jour|mois|an)/i";

  foreach ($filters as $_filter) {
    if (preg_match($re, $_filter, $matches)) {
      $map = array("an" => "YEAR", "mois" => "MONTH", "jour" => "DAY");

      $nouvelle_date=CMbDT::dateTime("-".$matches[1]." ".$map[$matches[2]]);

      $where[] ="users.user_last_login <= '$nouvelle_date'";
    }
    else {
      $where[] ="functions_mediboard.text LIKE '%$_filter%' OR
              users.user_last_name LIKE '$_filter%' OR
              users.user_first_name LIKE '$_filter%' OR
              users.user_username LIKE '$_filter%' ";
    }
  }
}

if ($pro_sante) {
  $user_types = array("Chirurgien", "Anesthésiste", "Médecin", "Infirmière", "Rééducateur", "Sage Femme");
  $utypes_flip = array_flip(CUser::$types);
  if (is_array($user_types)) {
    foreach ($user_types as $key => $value) {
      $user_types[$key] = $utypes_flip[$value];
    }

    $where["users.user_type"] = CSQLDataSource::prepareIn($user_types);
  }
}

if ($inactif) {
  $where["users_mediboard.actif"] = "!= '1'";
}

if ($ldap_bound) {
  $ljoin["id_sante400"] = "id_sante400.object_id = users.user_id";
  $where["id_sante400.object_class"] = " = 'CUser'"; 
  $where["id_sante400.tag"] = " = '".CAppUI::conf("admin LDAP ldap_tag")."'";
}

$order=null;

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
if ($order_col == "user_last_login") {
  $order = "users.user_last_login ";
  $order .= $order_way == "ASC" ? "DESC" : "ASC";
  $order .= ", users.user_last_name ASC, users.user_first_name ASC";
}

$total_mediuser = $mediuser->countList($where, null, $ljoin);
/** @var CMediusers[] $mediusers */
$mediusers = $mediuser->loadList($where, $order, "$page, $step", "users_mediboard.user_id", $ljoin);
foreach ($mediusers as $_mediuser) {
  $_mediuser->loadRefFunction();
  $_mediuser->loadRefProfile();
  $_mediuser->loadRefUser();
  $_mediuser->_ref_user->isLDAPLinked();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("utypes"        , CUser::$types);
$smarty->assign("total_mediuser", $total_mediuser);
$smarty->assign("page"          , $page);
$smarty->assign("pro_sante"     , $pro_sante);
$smarty->assign("inactif"       , $inactif);
$smarty->assign("ldap_bound"    , $ldap_bound);
$smarty->assign("filter"        , $filter);
$smarty->assign("mediusers"     , $mediusers);
$smarty->assign("user_id"       , $user_id);
$smarty->assign("group"         , $group);
$smarty->assign("order_way"     , $order_way);
$smarty->assign("order_col"     , $order_col);
$smarty->assign("step"          , $step);
$smarty->assign("tag_mediuser"          , CMediusers::getTagMediusers($group->_id));
$smarty->assign("no_association"        , CValue::get("no_association"));
$smarty->assign("ldap_user_actif"       , CValue::get("ldap_user_actif"));
$smarty->assign("ldap_user_deb_activite", CValue::get("ldap_user_deb_activite"));
$smarty->assign("ldap_user_fin_activite", CValue::get("ldap_user_fin_activite"));

$smarty->display("vw_idx_mediusers.tpl");

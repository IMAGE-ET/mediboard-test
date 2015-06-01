<?php

/**
 * View mediusers
 *
 * @category Mediusers
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$filter  = CValue::getOrSession("filter",    "");
$user_id = CValue::get("user_id");
$type    = CValue::getOrSession("_user_type");
$locked  = CValue::getOrSession("locked");

//ldap
$no_association         = CValue::get("no_association");
$ldap_user_actif        = CValue::get("ldap_user_actif");
$ldap_user_deb_activite = CValue::get("ldap_user_deb_activite");
$ldap_user_fin_activite = CValue::get("ldap_user_fin_activite");

// Récupération des fonctions
$group = CGroups::loadCurrent();
$group->loadFunctions();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("utypes"                , CUser::$types);
$smarty->assign("filter"                , $filter);
$smarty->assign("user_id"               , $user_id);
$smarty->assign("type"                  , $type);
$smarty->assign("locked"                , $locked);
$smarty->assign("group"                 , $group);
$smarty->assign("no_association"        , $no_association);
$smarty->assign("ldap_user_actif"       , $ldap_user_actif);
$smarty->assign("ldap_user_deb_activite", $ldap_user_deb_activite);
$smarty->assign("ldap_user_fin_activite", $ldap_user_fin_activite);
$smarty->display("vw_idx_mediusers.tpl");

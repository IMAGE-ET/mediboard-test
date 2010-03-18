<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

$page    = intval(CValue::get('page', 0));
$inactif = CValue::get("inactif", array());
$page_userfunction = intval(CValue::get('page_userfunction', 0));

// Récupération des groupes
$group = new CGroups;
$order = "text";
$groups = $group->loadListWithPerms(PERM_EDIT, null, $order);

$function = new CFunctions();
$where["actif"] = $inactif ? "!= '1'" : "= '1'";
$where["group_id"] = "= '".CGroups::loadCurrent()->_id."'";
$total_functions = $function->countList($where);

$order = "text ASC";
$functions = $function->loadList($where, $order, "$page, 35");
foreach($functions as $_function) {
  $_function->loadRefs();
}
   
// Récupération de la fonction selectionnée
$userfunction = new CFunctions;
$userfunction->load(CValue::getOrSession("function_id", 0));
$primary_users       = array();
$total_userfunctions = null;
if($userfunction->_id) {
  $userfunction->loadRefsFwd();
  $userfunction->loadBackRefs("users");
  $total_userfunctions = $userfunction->countBackRefs("users");
  $primary_users = $userfunction->loadBackRefs("users", null, "$page_userfunction, 20");
  foreach($primary_users as $_user) {
    $_user->loadRefProfile();
  }

  $userfunction->loadBackRefs("secondary_functions");
  foreach($userfunction->_back["secondary_functions"] as &$_sec_function) {
    $_sec_function->loadRefUser();
    $_sec_function->_ref_user->loadRefProfile();
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("inactif"            , $inactif);
$smarty->assign("functions"          , $functions);
$smarty->assign("total_functions"    , $total_functions);
$smarty->assign("page"               , $page);
$smarty->assign("canSante400"        , CModule::getCanDo("dPsante400"));
$smarty->assign("userfunction"       , $userfunction);
$smarty->assign("primary_users"      , $primary_users);
$smarty->assign("total_userfunctions", $total_userfunctions);
$smarty->assign("page_userfunction"  , $page_userfunction);
$smarty->assign("groups"             , $groups  );
$smarty->assign("secondary_function" , new CSecondaryFunction());
$smarty->assign("utypes"             , CUser::$types );
$smarty->display("vw_idx_functions.tpl");

?>
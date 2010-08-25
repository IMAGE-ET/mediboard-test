<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkRead();

$page          = intval(CValue::get('page', 0));
$inactif       = CValue::get("inactif", array());
$type          = CValue::get("type");
$page_function = intval(CValue::get('page_function', 0));
$order_way     = CValue::getOrSession("order_way", "ASC");
$order_col     = CValue::getOrSession("order_col", "text");

$step              = 25;
$step_sec_function = 10;

// Récupération des groupes
$group = new CGroups;
$order = "text";
$groups = $group->loadListWithPerms(PERM_EDIT, null, $order);

$function = new CFunctions();
if ($type) {
  $where["type"] = "= '$type'";
}
$where["actif"] = $inactif ? "!= '1'" : "= '1'";
$where["group_id"] = "= '".CGroups::loadCurrent()->_id."'";
$total_functions = $function->countList($where);


$order = "";
if ($order_col == "text") {
  $order = "text $order_way";
} elseif ($order_col == "type") {
  $order = "type $order_way, text ASC";
}
$functions = $function->loadList($where, $order, "$page, $step");
foreach($functions as $_function) {
  //$_function = new CFunctions();
	$_function->loadRefs();
  $_function->loadBackRefs("secondary_functions");
}
   
// Récupération de la fonction selectionnée
$function = new CFunctions;
$function->load(CValue::getOrSession("function_id", 0));
$function->loadRefsNotes();
$primary_users       = array();
$total_sec_functions = null;
if($function->_id) {
  $function->loadRefsFwd();
  $function->loadBackRefs("users");
  $total_sec_functions = $function->countBackRefs("users");
  $primary_users = $function->loadBackRefs("users", null, "$page_function, $step_sec_function");
  foreach($primary_users as $_user) {
    $_user->loadRefProfile();
  }

  $function->loadBackRefs("secondary_functions");
  foreach($function->_back["secondary_functions"] as &$_sec_function) {
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
$smarty->assign("function"           , $function);
$smarty->assign("primary_users"      , $primary_users);
$smarty->assign("total_functions"    , $total_functions);
$smarty->assign("total_sec_functions" , $total_sec_functions);
$smarty->assign("page_function"      , $page_function);
$smarty->assign("groups"             , $groups  );
$smarty->assign("secondary_function" , new CSecondaryFunction());
$smarty->assign("utypes"             , CUser::$types );
$smarty->assign("type"               , $type );
$smarty->assign("order_way"          , $order_way);
$smarty->assign("order_col"          , $order_col);
$smarty->assign("step"               , $step);

$smarty->display("vw_idx_functions.tpl");

?>
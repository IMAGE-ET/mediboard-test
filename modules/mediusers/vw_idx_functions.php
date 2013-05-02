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

$step = 25;
$group = CGroups::loadCurrent();
$function = new CFunctions();
if ($type) {
  $where["type"] = "= '$type'";
}
$where["actif"] = $inactif ? "!= '1'" : "= '1'";
$where["group_id"] = "= '$group->_id'";
$total_functions = $function->countList($where);

$order = null;
if ($order_col == "text") {
  $order = "text $order_way";
} 
if ($order_col == "type") {
  $order = "type $order_way, text ASC";
}

$functions = $function->loadList($where, $order, "$page, $step");
foreach ($functions as $_function) {
  $_function->countBackRefs("users");
  $_function->countBackRefs("secondary_functions");
}

$function_id = CValue::getOrSession("function_id");
   
// Création du template
$smarty = new CSmartyDP();
$smarty->assign("inactif"            , $inactif);
$smarty->assign("functions"          , $functions);
$smarty->assign("total_functions"    , $total_functions);
$smarty->assign("page"               , $page);
$smarty->assign("function_id"        , $function_id);
$smarty->assign("type"               , $type );
$smarty->assign("order_way"          , $order_way);
$smarty->assign("order_col"          , $order_col);
$smarty->assign("step"               , $step);

$smarty->display("vw_idx_functions.tpl");

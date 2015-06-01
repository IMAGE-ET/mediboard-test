<?php

/**
 * View functions
 *
 * @category Mediusers
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id: vw_idx_functions.php 19463 2013-06-07 10:36:29Z lryo $
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$page          = intval(CValue::get('page', 0));
$inactif       = CValue::get("inactif", array());
$type          = CValue::get("type");
$filter        = CValue::getOrSession("filter",    "");
$page_function = intval(CValue::get('page_function', 0));
$order_way     = CValue::getOrSession("order_way", "ASC");
$order_col     = CValue::getOrSession("order_col", "text");

$step = 25;
$group = CGroups::loadCurrent();
$function = new CFunctions();
if ($type) {
  $where["type"] = "= '$type'";
}

if ($inactif == "1") {
  $where["actif"] = "= '0'";
}
if ($inactif == "0") {
  $where["actif"] = "= '1'";
}

$where["group_id"] = "= '$group->_id'";

$order = null;
if ($order_col == "text") {
  $order = "text $order_way";
}
if ($order_col == "type") {
  $order = "type $order_way, text ASC";
}

if ($filter) {
  $functions       = $function->seek($filter, $where, "$page, $step", true, null, $order);
  $total_functions = $function->_totalSeek;
}
else {
  $functions       = $function->loadList($where, $order, "$page, $step");
  $total_functions = $function->countList($where);
}

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

$smarty->display("inc_search_functions.tpl");

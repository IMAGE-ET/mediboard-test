<?php 

/**
 * $Id$
 *  
 * @category pmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

CCanDo::checkRead();

$cim = new CCIM10();
$cim->complete_name = CValue::get("words");
$modal = CValue::get("modal");

// Pagination
$current = CValue::get("current", 0);
$step    = 20;
$limit = "$current, $step";
$where["code"] = "IS NOT NULL";
$order = "code";

/** @var CCIM10[] $list_cim */
$list_cim = $cim->seek($cim->complete_name, $where, $limit, true, null, $order);
$total = $cim->_totalSeek;


$smarty = new CSmartyDP();
$smarty->assign("cim"      , $cim);
$smarty->assign("list_cim" , $list_cim);

$smarty->assign("current", $current);
$smarty->assign("step"   , $step);
$smarty->assign("total"  , $total);
$smarty->assign("modal"  , $modal);

$smarty->display("nomenclature_cim/inc_search_nomenclature_cim10.tpl");
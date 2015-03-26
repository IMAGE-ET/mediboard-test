<?php 

/**
 * $Id$
 *  
 * @category ImportTools
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$dsn          = CValue::get("dsn");
$table        = CValue::get("table");
$tooltip      = CValue::get("tooltip");

$start        = (int)CValue::get("start");
$count        = (int)CValue::getOrSession("count", 50);

$order_column = CValue::getOrSession("order_column");
$order_way    = CValue::getOrSession("order_way", "ASC");

$where_column = CValue::get("where_column");
$where_value  = CValue::get("where_value");

$ds = CSQLDataSource::get($dsn);

$columns = CImportTools::getColumnsInfo($ds, $table);

$orderby = "";
if ($order_column) {
  $order_column = preg_replace('/[^-_\w]/', "", $order_column);

  if (in_array($order_column, array_keys($columns))) {
    if (!in_array($order_way, array("ASC", "DESC"))) {
      $order_way = "ASC";
    }
    $orderby = "$order_column $order_way";
  }
}

$request = new CRequest();
$request->addTable($table);
$request->addSelect("*");
$request->setLimit("$start,$count");
if ($orderby) {
  $request->addOrder($orderby);
}
if ($where_column) {
  $where = array(
    $where_column => $ds->prepare("=?", $where_value)
  );
  $request->addWhere($where);
}

$rows = $ds->loadList($request->makeSelect());

$request->setLimit(null);
$request->order = null;
$total = $ds->loadResult($request->makeSelectCount());

$counts = array(
  10, 50, 100, 200, 500, 1000, 5000
);

$smarty = new CSmartyDP();
$smarty->assign("rows",    $rows);
$smarty->assign("columns", $columns);
$smarty->assign("tooltip", $tooltip);
$smarty->assign("dsn",     $dsn);
$smarty->assign("table",   $table);
$smarty->assign("total",   $total);
$smarty->assign("start",   $start);
$smarty->assign("count",   $count);
$smarty->assign("counts",  $counts);
$smarty->assign("order_column", $order_column);
$smarty->assign("order_way",    $order_way);
$smarty->assign("where_column", $where_column);
$smarty->assign("where_value",  $where_value);
$smarty->display("inc_vw_table_data.tpl");
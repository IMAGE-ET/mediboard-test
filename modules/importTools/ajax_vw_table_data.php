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

$dsn   = CValue::get("dsn");
$table = CValue::get("table");
$start = (int)CValue::get("start");
$count = (int)CValue::getOrSession("count", 50);

$ds = CSQLDataSource::get($dsn);

$rows = $ds->loadList("SELECT * FROM `{$table}` LIMIT $start,$count;");
$total = $ds->loadResult("SELECT COUNT(*) FROM `{$table}`;");

$columns = $ds->loadHashAssoc("SHOW COLUMNS FROM `{$table}`");

foreach ($columns as &$_column) {
  $_column["datatype"] = $_column["Type"]." ".($_column["Null"] == "YES" ? "NULL" : "NOT NULL");
  $_column["is_text"] = preg_match('/(char|text)/', $_column["Type"]);
}

$counts = array(
  10, 50, 100, 200, 500, 1000, 5000
);

$smarty = new CSmartyDP();
$smarty->assign("rows",    $rows);
$smarty->assign("columns", $columns);
$smarty->assign("dsn",     $dsn);
$smarty->assign("table",   $table);
$smarty->assign("total",   $total);
$smarty->assign("start",   $start);
$smarty->assign("count",   $count);
$smarty->assign("counts",  $counts);
$smarty->display("inc_vw_table_data.tpl");
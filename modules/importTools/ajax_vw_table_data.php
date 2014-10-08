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

$ds = CSQLDataSource::get($dsn);

$rows = $ds->loadList("SELECT * FROM `{$table}` LIMIT 50;");

$columns = $ds->loadHashAssoc("SHOW COLUMNS FROM `{$table}`");

foreach ($columns as &$_column) {
  $_column["datatype"] = $_column["Type"]." ".($_column["Null"] == "YES" ? "NULL" : "NOT NULL");
}

$smarty = new CSmartyDP();
$smarty->assign("rows",    $rows);
$smarty->assign("columns", $columns);
$smarty->assign("dsn",     $dsn);
$smarty->assign("table",   $table);
$smarty->display("inc_vw_table_data.tpl");
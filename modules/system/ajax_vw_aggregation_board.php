<?php
/**
 * $Id$
 *
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$tables = array(
  "access_log",
  "access_log_archive",
  "datasource_log",
  "datasource_log_archive"
);

$db = CAppUI::conf("db");
$db = $db["std"]["dbname"];

$ds = CSQLDataSource::get("std");

$stats = array();

foreach ($tables as $_table) {
  $query = "SELECT `aggregate`,
              COUNT(*) AS records,
              DATE(MIN(`period`)) AS date_min,
              DATE(MAX(`period`)) AS date_max
            FROM $_table
            GROUP BY `aggregate`
            ORDER BY `aggregate`";

  $stats[$_table] = array(
    "data" => $ds->loadList($query),
  );

  $query = "SELECT
              ROUND((data_length / 1024 / 1024), 2)  AS 'data_mb',
              ROUND((index_length / 1024 / 1024), 2) AS 'index_mb',
              ROUND((data_free / 1024 / 1024), 2)    AS 'data_free'
            FROM information_schema.TABLES
            WHERE `table_schema` = '$db'
              AND `table_name` = '$_table';";

  $meta                   = $ds->loadHash($query);
  $meta["total"]          = round($meta["data_mb"] + $meta["index_mb"], 2);

  $stats[$_table]["meta"] = $meta;
}

$smarty = new CSmartyDP();
$smarty->assign("stats", $stats);
$smarty->display("vw_aggregation_board.tpl");
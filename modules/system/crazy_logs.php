<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

$mode  = CValue::get("mode",  "find");
$ratio = CValue::get("ratio", 80);
$limit = CValue::get("limit", 1000);

$log = new CAccessLog();
$ds = $log->_spec->ds;

// Purge
$purged_count = null;
if ($mode == "purge") {
  /*
  $query = "DELETE `access_log`, `datasource_log`
            FROM `access_log`, `datasource_log`
            WHERE `access_log`.`duration` / `access_log`.`hits` > '$ratio'
              AND `access_log`.`accesslog_id` = `datasource_log`.`accesslog_id`
            LIMIT $limit;";
  */
  $query = "SELECT `accesslog_id`
            FROM `access_log`
            WHERE `duration` / `hits` > '$ratio'
            LIMIT $limit;";

  $result = $ds->loadList($query);
  $ids = CMbArray::pluck($result, "accesslog_id");

  if (!empty($ids)) {
    $ids = implode(", ", $ids);

    $query = "DELETE FROM `access_log`
              WHERE `accesslog_id` IN ($ids)
              LIMIT $limit";

    $ds->exec($query);
    $purged_count = $ds->affectedRows();

    $query = "DELETE FROM `datasource_log`
              WHERE `accesslog_id` IN ($ids)
              LIMIT $limit";

    $ds->exec($query);
  }
}

// Détection
$query = "SELECT module, action, COUNT(*) AS total FROM `access_log` 
  WHERE duration / hits > '$ratio'
  GROUP BY module, action";

$logs = $ds->loadList($query);

$smarty = new CSmartyDP();
$smarty->assign("logs",         $logs);
$smarty->assign("ratio",        $ratio);
$smarty->assign("purged_count", $purged_count);
$smarty->display("crazy_logs.tpl");
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

$mode  = CValue::get("mode", "find");
$ratio = CValue::get("ratio", 300);
$limit = CValue::get("limit", 1000);

CView::enforceSlave();

$log = new CAccessLog();
$ds  = $log->_spec->ds;

// Purge
$purged_count = null;
if ($mode == "purge") {
  $query = "SELECT `accesslog_id`
            FROM `access_log`
            WHERE `duration` / `hits` > '$ratio'
            LIMIT $limit;";

  $result = $ds->loadList($query);
  $ids    = CMbArray::pluck($result, "accesslog_id");

  if (!empty($ids)) {
    $ids = implode(", ", $ids);

    $query = "DELETE FROM `access_log`
              WHERE `accesslog_id` IN ($ids)
              LIMIT $limit";

    $ds->exec($query);
    $purged_count = $ds->affectedRows();
  }
}

// Détection
$query = "SELECT `module_action`.module AS _module,
                 `module_action`.action AS _action,
                 COUNT(*)               AS total
          FROM `access_log`, `module_action`
          WHERE duration / hits > '$ratio'
            AND `module_action`.`module_action_id` = `access_log`.`module_action_id`
          GROUP BY `module_action`.module, `module_action`.action";

$logs = $ds->loadList($query);

$smarty = new CSmartyDP();
$smarty->assign("logs", $logs);
$smarty->assign("ratio", $ratio);
$smarty->assign("purged_count", $purged_count);
$smarty->display("crazy_access_logs.tpl");
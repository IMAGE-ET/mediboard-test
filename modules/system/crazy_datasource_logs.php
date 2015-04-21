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
$ratio = CValue::get("ratio", 300);
$limit = CValue::get("limit", 1000);

CView::enforceSlave();

$log = new CDataSourceLog();
$ds = $log->_spec->ds;

// Purge
$purged_count = null;
if ($mode == "purge") {
  $query = "SELECT `datasourcelog_id`
            FROM `datasource_log`
            WHERE `duration` / `requests` > '$ratio'
            LIMIT $limit;";

  $result = $ds->loadList($query);
  $ids = CMbArray::pluck($result, "datasourcelog_id");

  if (!empty($ids)) {
    $ids = implode(", ", $ids);

    $query = "DELETE FROM `datasource_log`
              WHERE `datasourcelog_id` IN ($ids)
              LIMIT $limit";

    $ds->exec($query);
    $purged_count = $ds->affectedRows();
  }
}

// Détection
$query = "SELECT `module_action`.module AS _module,
                 `module_action`.action AS _action,
                 COUNT(*)               AS total
          FROM `datasource_log`, `module_action`
          WHERE duration / `requests` > '$ratio'
            AND `module_action`.`module_action_id` = `datasource_log`.`module_action_id`
          GROUP BY `module_action`.module, `module_action`.action";

$logs = $ds->loadList($query);

$smarty = new CSmartyDP();
$smarty->assign("logs",         $logs);
$smarty->assign("ratio",        $ratio);
$smarty->assign("purged_count", $purged_count);
$smarty->display("crazy_datasource_logs.tpl");
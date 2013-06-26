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

CApp::setTimeLimit(0);
CApp::setMemoryLimit("1024M");

$dry_run = CValue::get("dry_run", false);

// Check prerequisites
$ds = CSQLDataSource::get("std");

$query = "SELECT MAX(`accesslog_id`)
          FROM `access_log`;";

$last_ID = $ds->loadResult($query);

$month = CMbDT::transform("- 1 MONTH", CMbDT::dateTime(), "%Y-%m-%d 00:00:00");
$year  = CMbDT::transform("- 1 YEAR", CMbDT::dateTime(), "%Y-%m-%d 00:00:00");

// Get the oldest log to aggregate
$query = "SELECT MIN(`period`)
          FROM `access_log`
          WHERE `period` < '$year'
            AND `aggregate` < '1440';";

$oldest = $ds->loadResult($query);

// Take the 6 months period to aggregate (for logs older than 1 year)
$year = min(CMbDT::transform("+ 6 MONTH", $oldest, "%Y-%m-%d 00:00:00"), $year);

// Dry run mode, just compute the number of logs to aggregate
if ($dry_run) {
  // Récupération des IDs de journaux à supprimer
  $query = "SELECT
            count(`accesslog_id`)
          FROM `access_log`
          WHERE
          (
            `period` < '$year'
             AND `aggregate` < '1440'
          )
          OR
          (
            `period` < '$month'
            AND `period` >= '$year'
            AND `aggregate` < '60'
          )";

  $IDs_to_aggregate = $ds->loadResult($query);

  CAppUI::stepAjax("%d access logs to aggregate", UI_MSG_OK, $IDs_to_aggregate);
  return;
}

// Récupération des IDs de journaux à supprimer
$query = "SELECT
            CAST(GROUP_CONCAT(`accesslog_id` SEPARATOR ', ') AS CHAR) AS ids,
            `module`,
            `action`,
            date_format(`period`, '%Y-%m-%d 00:00:00'),
            `bot`
          FROM `access_log`
          WHERE `period` < '$year'
            AND `aggregate` < '1440'
          GROUP BY `module`, `action`, date_format(`period`, '%Y-%m-%d 00:00:00'), `bot`
          ORDER BY `accesslog_id`";

$year_IDs_to_aggregate = $ds->loadList($query);

$query = "SELECT
            CAST(GROUP_CONCAT(`accesslog_id` SEPARATOR ', ') AS CHAR) AS ids,
            `module`,
            `action`,
            date_format(`period`, '%Y-%m-%d 00:00:00'),
            `bot`
          FROM `access_log`
          WHERE `period` < '$month'
            AND `period` >= '$year'
            AND `aggregate` < '60'
          GROUP BY `module`, `action`, date_format(`period`, '%Y-%m-%d 00:00:00'), `bot`
          ORDER BY `accesslog_id`";

$month_IDs_to_aggregate = $ds->loadList($query);

$IDs_to_aggregate = array_merge($year_IDs_to_aggregate, $month_IDs_to_aggregate);
$IDs_to_aggregate = CMbArray::pluck($IDs_to_aggregate, "ids");

// Compression des journaux de plus d'une année à la journée
$query = "INSERT INTO `access_log` (
            `module`,
            `action`,
            `period`,
            `aggregate`,
            `bot`,
            `hits`,
            `duration`,
            `request`,
            `size`,
            `errors`,
            `warnings`,
            `notices`,
            `processus`,
            `processor`,
            `peak_memory`
          )
          SELECT
            `module`,
            `action`,
            date_format(`period`, '%Y-%m-%d 00:00:00'),
            '1440',
            `bot`,
            SUM(`hits`),
            SUM(`duration`),
            SUM(`request`),
            SUM(`size`),
            SUM(`errors`),
            SUM(`warnings`),
            SUM(`notices`),
            SUM(`processus`),
            SUM(`processor`),
            SUM(`peak_memory`)
          FROM `access_log`
          WHERE `period` < '$year'
            AND `aggregate` < '1440'
          GROUP BY `module`, `action`, date_format(`period`, '%Y-%m-%d 00:00:00'), `bot`
          ORDER BY `accesslog_id`";

$ds->exec($query);

// Suppression des journaux vieux de plus d'une année
$query = "DELETE
          FROM `access_log`
          WHERE `period` < '$year'
            AND `aggregate` < '1440'";

$ds->exec($query);

// Compression des journaux vieux de plus d'un mois et de moins d'une année
$query = "INSERT INTO `access_log` (
            `module`,
            `action`,
            `period`,
            `aggregate`,
            `bot`,
            `hits`,
            `duration`,
            `request`,
            `size`,
            `errors`,
            `warnings`,
            `notices`,
            `processus`,
            `processor`,
            `peak_memory`
          )
          SELECT
            `module`,
            `action`,
            date_format(`period`, '%Y-%m-%d 00:00:00'),
            '60',
            `bot`,
            SUM(`hits`),
            SUM(`duration`),
            SUM(`request`),
            SUM(`size`),
            SUM(`errors`),
            SUM(`warnings`),
            SUM(`notices`),
            SUM(`processus`),
            SUM(`processor`),
            SUM(`peak_memory`)
          FROM `access_log`
          WHERE `period` < '$month'
            AND `period` >= '$year'
            AND `aggregate` < '60'
          GROUP BY `module`, `action`, date_format(`period`, '%Y-%m-%d 00:00:00'), `bot`
          ORDER BY `accesslog_id`";

$ds->exec($query);

// Suppression des journaux vieux de plus d'un mois et de moins d'une année
$query = "DELETE
          FROM `access_log`
          WHERE `period` < '$month'
            AND `period` >= '$year'
            AND `aggregate` < '60'";

$ds->exec($query);

// Récupération des IDs des journaux agrégés de plus d'un an qui viennent d'être insérés
$query = "SELECT `accesslog_id`, `accesslog_id`
          FROM `access_log`
          WHERE `period` < '$year'
            AND `aggregate` > '60'
            AND `accesslog_id` > '$last_ID'
          ORDER BY `accesslog_id`";

$year_IDs_to_insert = $ds->loadHashList($query);

// Récupération des IDs des journaux agrégés de plus d'un mois et moins d'un an qui viennent d'être insérés
$query = "SELECT `accesslog_id`, `accesslog_id`
          FROM `access_log`
          WHERE `period` < '$month'
            AND `period` >= '$year'
            AND `aggregate` > '10'
            AND `accesslog_id` > '$last_ID'
          ORDER BY `accesslog_id`";

$month_IDs_to_insert = $ds->loadHashList($query);

$IDs_to_insert = array_merge($year_IDs_to_insert, $month_IDs_to_insert);
CAppUI::stepAjax("%d access logs inserted", UI_MSG_OK, count($IDs_to_insert));

foreach ($IDs_to_aggregate as $key => $ids) {
  $id = $IDs_to_insert[$key];

  // Compression des journaux de sources de données
  $query = "INSERT INTO `datasource_log` (
            `datasource`,
            `requests`,
            `duration`,
            `accesslog_id`
          )
          SELECT
            `datasource`,
            SUM(`requests`),
            SUM(`duration`),
            $id
          FROM `datasource_log`
          WHERE `accesslog_id` IN ($ids)
          GROUP BY `datasource`";

  $ds->exec($query);

  // Compression des journaux de sources de données
  $query = "DELETE
            FROM `datasource_log`
            WHERE `accesslog_id` IN ($ids)";

  $ds->exec($query);
}

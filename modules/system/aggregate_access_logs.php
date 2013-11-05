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

$sup_agg = 1440;
$avg_agg =   60;
$std_agg =   10;

// Check prerequisites
$ds = CSQLDataSource::get("std");

$query = "SELECT MAX(`accesslog_id`)
          FROM `access_log`;";

$last_ID = $ds->loadResult($query);

$last_month = CMbDT::transform("- 1 MONTH", CMbDT::dateTime(), "%Y-%m-%d 00:00:00");
$last_year  = CMbDT::transform("- 1 YEAR",  CMbDT::dateTime(), "%Y-%m-%d 00:00:00");

// Get the oldest log to aggregate
$query = "SELECT `period`
          FROM `access_log`
          WHERE `period` <= '$last_month'
            AND `aggregate` <= IF (`period` <= '$last_year', '$avg_agg', '$std_agg')
          ORDER BY `period` LIMIT 1;";

$oldest_from = $ds->loadResult($query);

if (!$oldest_from) {
  CAppUI::stepAjax("No log to aggregate", UI_MSG_OK);
  return;
}

// Take the 6 months period to aggregate
$oldest_to = min(CMbDT::transform("+ 6 MONTHS", $oldest_from, "%Y-%m-%d 00:00:00"), $last_month);

// Dry run mode, just compute the number of logs to aggregate
if ($dry_run) {
  // Récupération des IDs de journaux à supprimer
  $query = "SELECT count(`accesslog_id`) as count
            FROM `access_log`
            WHERE `period` BETWEEN '$oldest_from' AND '$oldest_to'
               AND `aggregate` <= IF (`period` <= '$last_year', '$avg_agg', '$std_agg');";

  $count = $ds->loadResult($query);

  $msg = "%d access logs to aggregate from %s to %s";
  CAppUI::stepAjax($msg, UI_MSG_OK, $count, CMbDT::date($oldest_from), CMbDT::date($oldest_to));
  return;
}

// Récupération des IDs de journaux à supprimer
$query = "SELECT
            CAST(GROUP_CONCAT(`accesslog_id` SEPARATOR ', ') AS CHAR) AS ids,
            `module`,
            `action`,
            `period`,
            `bot`
          FROM `access_log`
          WHERE `period` BETWEEN '$oldest_from' AND '$oldest_to'
            AND `period` <= '$last_year'
            AND `aggregate` < '$sup_agg'
          GROUP BY `module`, `action`, date_format(`period`, '%Y-%m-%d 00:00:00'), `bot`";

$year_IDs_to_aggregate = $ds->loadList($query);

if ($year_IDs_to_aggregate) {
  // Insert aggregated logs then delete previous logs, insert aggregated datasource logs and delete previous logs, step by step
  foreach ($year_IDs_to_aggregate as $_aggregate) {
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
                '$sup_agg',
                `bot`,
                @hits        := SUM(`hits`),
                @duration    := SUM(`duration`),
                @request     := SUM(`request`),
                @size        := SUM(`size`),
                @errors      := SUM(`errors`),
                @warnings    := SUM(`warnings`),
                @notices     := SUM(`notices`),
                @processus   := SUM(`processus`),
                @processor   := SUM(`processor`),
                @peak_memory := SUM(`peak_memory`)
              FROM `access_log`
              WHERE `accesslog_id` IN (".$_aggregate['ids'].")
              GROUP BY `module`, `action`, DATE_FORMAT(`period`, '%Y-%m-%d 00:00:00'), `bot`
              ON DUPLICATE KEY UPDATE
                `hits`        = `hits`        + @hits,
                `duration`    = `duration`    + @duration,
                `request`     = `request`     + @request,
                `size`        = `size`        + @size,
                `errors`      = `errors`      + @errors,
                `warnings`    = `warnings`    + @warnings,
                `notices`     = `notices`     + @notices,
                `processus`   = `processus`   + @processus,
                `processor`   = `processor`   + @processor,
                `peak_memory` = `peak_memory` + @peak_memory";

    if (!$ds->exec($query)) {
      trigger_error("Failed to insert aggregated access logs", E_USER_ERROR);
      return;
    }

    $last_insert_id = $ds->insertId();

    // Delete previous logs
    $query = "DELETE
              FROM `access_log`
              WHERE `accesslog_id` IN (".$_aggregate['ids'].")";

    $ds->exec($query);

    // Compression des journaux de sources de données
    $query = "INSERT INTO `datasource_log` (
                `datasource`,
                `requests`,
                `duration`,
                `accesslog_id`
              )
              SELECT
                `datasource`,
                @requests := SUM(`requests`),
                @duration := SUM(`duration`),
                $last_insert_id
              FROM `datasource_log`
              WHERE `accesslog_id` IN (".$_aggregate['ids'].")
              GROUP BY `datasource`
              ON DUPLICATE KEY UPDATE
                `requests` = `requests` + @requests,
                `duration` = `duration` + @duration";

    if (!$ds->exec($query)) {
      trigger_error("Failed to insert aggregated datasource logs", E_USER_ERROR);
      return;
    }

    // Delete previous logs
    $query = "DELETE
              FROM `datasource_log`
              WHERE `accesslog_id` IN (".$_aggregate['ids'].")";

    $ds->exec($query);
  }
}

$query = "SELECT
            CAST(GROUP_CONCAT(`accesslog_id` SEPARATOR ', ') AS CHAR) AS ids,
            `module`,
            `action`,
            `period`,
            `bot`
          FROM `access_log`
          WHERE `period` BETWEEN '$oldest_from' AND '$oldest_to'
            AND `period` <= '$last_month'
            AND `period`  > '$last_year'
            AND `aggregate` < '$avg_agg'
          GROUP BY `module`, `action`, date_format(`period`, '%Y-%m-%d %H:00:00'), `bot`";

$month_IDs_to_aggregate = $ds->loadList($query);

if ($month_IDs_to_aggregate) {
  // Insert aggregated logs then delete previous logs, insert aggregated datasource logs and delete previous logs, step by step
  foreach ($month_IDs_to_aggregate as $_aggregate) {
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
                date_format(`period`, '%Y-%m-%d %H:00:00'),
                '$avg_agg',
                `bot`,
                @hits        := SUM(`hits`),
                @duration    := SUM(`duration`),
                @request     := SUM(`request`),
                @size        := SUM(`size`),
                @errors      := SUM(`errors`),
                @warnings    := SUM(`warnings`),
                @notices     := SUM(`notices`),
                @processus   := SUM(`processus`),
                @processor   := SUM(`processor`),
                @peak_memory := SUM(`peak_memory`)
              FROM `access_log`
              WHERE `accesslog_id` IN (".$_aggregate['ids'].")
              GROUP BY `module`, `action`, DATE_FORMAT(`period`, '%Y-%m-%d %H:00:00'), `bot`
              ON DUPLICATE KEY UPDATE
                `hits`        = `hits`        + @hits,
                `duration`    = `duration`    + @duration,
                `request`     = `request`     + @request,
                `size`        = `size`        + @size,
                `errors`      = `errors`      + @errors,
                `warnings`    = `warnings`    + @warnings,
                `notices`     = `notices`     + @notices,
                `processus`   = `processus`   + @processus,
                `processor`   = `processor`   + @processor,
                `peak_memory` = `peak_memory` + @peak_memory";

    if (!$ds->exec($query)) {
      trigger_error("Failed to insert aggregated access logs", E_USER_ERROR);
      return;
    }

    $last_insert_id = $ds->insertId();

    // Delete previous logs
    $query = "DELETE
              FROM `access_log`
              WHERE `accesslog_id` IN (".$_aggregate['ids'].")";

    $ds->exec($query);

    // Compression des journaux de sources de données
    $query = "INSERT INTO `datasource_log` (
                `datasource`,
                `requests`,
                `duration`,
                `accesslog_id`
              )
              SELECT
                `datasource`,
                @requests := SUM(`requests`),
                @duration := SUM(`duration`),
                $last_insert_id
              FROM `datasource_log`
              WHERE `accesslog_id` IN (".$_aggregate['ids'].")
              GROUP BY `datasource`
              ON DUPLICATE KEY UPDATE
                `requests` = `requests` + @requests,
                `duration` = `duration` + @duration";

    if (!$ds->exec($query)) {
      trigger_error("Failed to insert aggregated datasource logs", E_USER_ERROR);
      return;
    }

    // Delete previous logs
    $query = "DELETE
              FROM `datasource_log`
              WHERE `accesslog_id` IN (".$_aggregate['ids'].")";

    $ds->exec($query);
  }
}

$IDs_to_aggregate = array_merge($year_IDs_to_aggregate, $month_IDs_to_aggregate);

$msg = "%d access logs inserted from %s to %s";
CAppUI::stepAjax($msg, UI_MSG_OK, count($IDs_to_aggregate), CMbDT::date($oldest_from), CMbDT::date($oldest_to));

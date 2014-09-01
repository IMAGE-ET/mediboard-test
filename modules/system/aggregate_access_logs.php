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
$table   = CValue::get("table");

switch ($table) {
  case "access_log":
    CAccessLog::aggregate(10, 60, 1440, $dry_run);
    break;

  case "access_log_archive":
    CAccessLogArchive::aggregate(10, 60, 1440, $dry_run);
    break;

  case "datasource_log":
    CDataSourceLog::aggregate(10, 60, 1440, $dry_run);
    break;

  case "datasource_log_archive":
    CDataSourceLogArchive::aggregate(10, 60, 1440, $dry_run);
    break;

  default:
    CAccessLog::aggregate(10, 60, 1440, $dry_run);
    CAccessLogArchive::aggregate(10, 60, 1440, $dry_run);
    CDataSourceLog::aggregate(10, 60, 1440, $dry_run);
    CDataSourceLogArchive::aggregate(10, 60, 1440, $dry_run);
}

echo CAppUI::getMsg();
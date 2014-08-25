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

CAccessLog::aggregate(10, 60, 1440, $dry_run);
CDataSourceLog::aggregate(10, 60, 1440, $dry_run);

echo CAppUI::getMsg();
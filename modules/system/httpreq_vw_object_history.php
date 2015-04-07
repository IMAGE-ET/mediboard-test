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

CCanDo::check();
$object = mbGetObjectFromGet("object_class", "object_id", "object_guid");
CView::enforceSlave();

$log = new CUserLog;
$log->setObject($object);
$count = $log->countMatchingList();

/** @var CUserLog[] $logs */
$logs = $log->loadMatchingList("date DESC", 10);

foreach ($logs as $key => $_log) {
  $_log->setObject($object);
  $_log->loadRefUser();
  $_log->getOldValues();
}

$more = $count - count($logs);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("logs", $logs);
$smarty->assign("more", $more);

$smarty->display("vw_object_history.tpl");

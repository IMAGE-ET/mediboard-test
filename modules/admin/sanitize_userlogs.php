<?php

/**
 * $Id$
 *
 * @category Admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();
/** @var int $step */
$step   = CView::get("step", "num default|100000");
/** @var int $offset */
$offset = CView::get("offset", "num default|1");
/** @var bool $purge */
$purge  = CView::get("purge", "bool default|0", true);
/** @var bool $auto */
$auto   = CView::get("auto", "bool", true);
CView::checkin();

// Triplets defining user log entries to be removed
$removers = array(
  array("CSourceFTP"              , "store", "counter"        ),
  array("CPrisePosologie"         , "store", "last_generation"),
  array("CPrescriptionLineElement", "store", "last_generation"),
  array("CTriggerMark", "create", ""),
  array("CTriggerMark", "store", "mark"),
  array("CTriggerMark", "store", "mark done"),
  // IMPORTANT: Leave at list one empty array() or you will purge all logs
  array("", "", ""),
);

$request = new CRequest();

// Primary key clauses
$min = $offset;
$max = $offset + $step - 1;
$request->addWhereClause("user_log_id", "BETWEEN $min AND $max");
$request->addForceIndex("PRIMARY");

// Removers clauses
$triplets = array();
foreach($removers as $_remover) {
  list($object_class, $type, $fields) = $_remover;
  $triplets[] = "$object_class-$type-$fields";
}
$request->addWhere("CONCAT_WS('-', `object_class`, `type`, `fields`) "
  . CSQLDataSource::prepareIn($triplets));

$offset = $max+1;

// Actual query
$log = new CUserLog();
$query = $request->getCountRequest($log);
$ds = $log->_spec->ds;
$count = $ds->loadResult($query);

// Stop auto if end is reached
$log->loadMatchingObject("user_log_id DESC");
if ($log->_id < $offset) {
  $auto = 0;
}
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("log"     , $log);
$smarty->assign("auto"    , $auto);
$smarty->assign("purge"   , $purge);
$smarty->assign("removers", $removers);
$smarty->assign("min"     , $min);
$smarty->assign("max"     , $max);
$smarty->assign("count"   , $count);
$smarty->assign("offset"  , $offset);
$smarty->assign("step"    , $step);

$smarty->display("sanitize_userlogs.tpl");
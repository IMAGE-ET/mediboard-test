<?php
/**
 * $Id: plage_selector.php 20893 2013-11-06 17:00:48Z nicolasld $
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 20893 $
 */

CCanDo::checkAdmin();

/** @var int $limit */
$limit  = CView::get("limit", "num default|1", true);
/** @var bool $remine */
$remine = CView::get("remine", "bool default|0", true);
/** @var string $miner_class */
$miner_class  = CView::get("miner_class", "str", true);
// Important for session board reloading
CView::get("automine", "bool default|0", true);
CView::checkin();

/** @var COperationMiner $miner */
$miner = new $miner_class;
if (!$miner instanceof COperationMiner) {
  trigger_error("Wrong miner class", E_USER_ERROR);
  return;
}

$report = $miner->mineSome($limit, $remine);

CAppUI::stepAjax("Miner: %s. Success mining count is '%s'", UI_MSG_OK, $miner_class, $report["success"]);

if ($report["failure"]) {
  CAppUI::stepAjax("Miner: %s. Failure mining counts is '%s'", UI_MSG_ERROR, $miner_class, $report["failure"]);
}
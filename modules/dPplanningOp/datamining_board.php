<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

/** @var bool $automine */
$automine  = CView::get("automine", "bool", true);
/** @var int $limit */
$limit  = CView::get("limit", "num default|1", true);
CView::checkin();

$counts = COperationMiner::makeOperationCounts();

$miner_classes = CApp::getChildClasses("COperationMiner");
$miners = array();
foreach ($miner_classes as $_class) {
  /** @var COperationMiner $miner */
  $miner = new $_class;
  $miner->loadMatchingObject("date DESC");
  $miner->makeMineCounts();
  $miners[] = $miner;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("counts"  , $counts);
$smarty->assign("miners"  , $miners);
$smarty->assign("automine", $automine);
$smarty->assign("limit"   , $limit);
$smarty->display("datamining_board.tpl");

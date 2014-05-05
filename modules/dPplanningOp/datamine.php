<?php
/**
 * $Id: datamine.php 22983 2014-04-29 13:34:36Z kgrisel $
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 22983 $
 */

CCanDo::checkAdmin();

$classes = CApp::getChildClasses("COperationMiner");
$limit   = CAppUI::conf("dataminer_limit");

foreach ($classes as $_class) {
  /** @var COperationMiner $miner */
  $miner  = new $_class;
  $report = $miner->mineSome($limit, false);

  $dt = CMbDT::dateTime();
  echo "<$dt> Miner: $_class. Success mining count is '" . $report["success"] . "'";
  if (!$report["failure"]) {
    echo "\n<$dt> Miner: $_class. Failure mining counts is '" . $report["failure"] . "'";
  }
}

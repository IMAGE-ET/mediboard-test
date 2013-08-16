<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

$group = CGroups::loadCurrent();

$graphs     = CSupervisionGraph::getAllFor($group);
$timed_data = CSupervisionTimedData::getAllFor($group);
$packs      = CSupervisionGraphPack::getAllFor($group);

foreach ($graphs as $_graph) {
  $_axes = $_graph->loadRefsAxes();

  foreach ($_axes as $_axis) {
    $_axis->loadBackRefs("series");
  }
}

CSupervisionGraph::includeFlot();

$smarty = new CSmartyDP();
$smarty->assign("graphs",     $graphs);
$smarty->assign("packs",      $packs);
$smarty->assign("timed_data", $timed_data);
$smarty->display("inc_list_supervision_graph.tpl");

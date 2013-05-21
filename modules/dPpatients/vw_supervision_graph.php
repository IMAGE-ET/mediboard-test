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

$supervision_graph_id = CValue::getOrSession("supervision_graph_id");

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
$smarty->assign("supervision_graph_id", $supervision_graph_id);
$smarty->display("vw_supervision_graph.tpl");

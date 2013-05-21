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

$supervision_graph_id = CValue::get("supervision_graph_id");

$graph = new CSupervisionGraph;
$graph->load($supervision_graph_id);
$axes = $graph->loadRefsAxes();

$sample = array();

$minute = 60000;
$start = 1291196760000;
$end   = $start + $minute*45;
$times = range($start, $end, $minute);

foreach ($axes as $_axis) {
  $_series = $_axis->loadRefsSeries();
  
  foreach ($_series as $_serie) {
    $sample[$_serie->value_type_id][$_serie->value_unit_id] = $_serie->getSampleData($times);
  }
}

$data = $graph->buildGraph($sample, $start-2*$minute, $end+2*$minute);

$smarty = new CSmartyDP();
$smarty->assign("data",  $data);
$smarty->assign("times", $times);
$smarty->assign("supervision_graph_id", $supervision_graph_id);
$smarty->display("inc_preview_supervision_graph.tpl");

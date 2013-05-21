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

foreach ($axes as $_axis) {
  $_axis->loadBackRefs("series");
}

$smarty = new CSmartyDP();
$smarty->assign("axes",  $axes);
$smarty->assign("graph", $graph);
$smarty->display("inc_list_supervision_graph_axes.tpl");

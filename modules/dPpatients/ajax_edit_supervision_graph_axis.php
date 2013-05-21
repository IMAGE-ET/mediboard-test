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

$supervision_graph_axis_id = CValue::get("supervision_graph_axis_id");
$supervision_graph_id = CValue::get("supervision_graph_id");

$graph = new CSupervisionGraph;
$graph->load($supervision_graph_id);

$axis = new CSupervisionGraphAxis;
if (!$axis->load($supervision_graph_axis_id)) {
  $axis->supervision_graph_id = $supervision_graph_id;
}
$axis->loadRefsNotes();

$smarty = new CSmartyDP();
$smarty->assign("axis",  $axis);
$smarty->assign("graph",  $graph);
$smarty->display("inc_edit_supervision_graph_axis.tpl");

<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$supervision_graph_series_id = CValue::get("supervision_graph_series_id");
$supervision_graph_axis_id   = CValue::get("supervision_graph_axis_id");

$axis = new CSupervisionGraphAxis;
$axis->load($supervision_graph_axis_id);

$series = new CSupervisionGraphSeries;
if (!$series->load($supervision_graph_series_id)) {
  $series->supervision_graph_axis_id = $supervision_graph_axis_id;
}
$series->loadRefsNotes();

$smarty = new CSmartyDP();
$smarty->assign("series", $series);
$smarty->assign("axis",   $axis);
$smarty->display("inc_edit_supervision_graph_series.tpl");

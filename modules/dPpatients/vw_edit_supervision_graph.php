<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$supervision_graph_id = CValue::get("supervision_graph_id");

$graph = new CSupervisionGraph;
$graph->load($supervision_graph_id);
$graph->loadRefsNotes();

$group_id = CGroups::loadCurrent()->_id;
$where = array(
  "owner_class" => "= 'CGroups'",
  "owner_id"    => "= '$group_id'",
);
$graphs = $graph->loadList($where, "title");

foreach($graphs as $_graph) {
  $_axes = $_graph->loadRefsAxes();
  
  foreach($_axes as $_axis) {
    $_axis->loadBackRefs("series");
  }
}

$smarty = new CSmartyDP();
$smarty->assign("graph",  $graph);
$smarty->assign("graphs", $graphs);
$smarty->display("vw_edit_supervision_graph.tpl");

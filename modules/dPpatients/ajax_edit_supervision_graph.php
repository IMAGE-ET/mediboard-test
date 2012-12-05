<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$supervision_graph_id = CValue::getOrSession("supervision_graph_id");

$graph = new CSupervisionGraph;
$graph->load($supervision_graph_id);
$graph->loadRefsNotes();

if (!$graph->_id) {
  $graph->height = 200;
}

$smarty = new CSmartyDP();
$smarty->assign("graph",  $graph);
$smarty->display("inc_edit_supervision_graph.tpl");

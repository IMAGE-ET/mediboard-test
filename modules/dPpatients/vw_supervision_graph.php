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

CSupervisionGraph::includeFlot();

$smarty = new CSmartyDP();
$smarty->assign("supervision_graph_id", $supervision_graph_id);
$smarty->display("vw_supervision_graph.tpl");

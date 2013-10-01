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

$axis = new CSupervisionGraphAxis();
$axis->load($supervision_graph_axis_id);

$labels = $axis->loadRefsLabels();

$smarty = new CSmartyDP();
$smarty->assign("labels",  $labels);
$smarty->assign("axis", $axis);
$smarty->display("inc_list_supervision_graph_labels.tpl");

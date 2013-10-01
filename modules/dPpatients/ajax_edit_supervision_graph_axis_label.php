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

$supervision_graph_axis_label_id = CValue::get("supervision_graph_axis_label_id");
$supervision_graph_axis_id       = CValue::get("supervision_graph_axis_id");

$axis = new CSupervisionGraphAxis();
$axis->load($supervision_graph_axis_id);

$label = new CSupervisionGraphAxisValueLabel();
if (!$label->load($supervision_graph_axis_label_id)) {
  $label->supervision_graph_axis_id = $supervision_graph_axis_id;
}
$label->loadRefsNotes();

$smarty = new CSmartyDP();
$smarty->assign("label", $label);
$smarty->assign("axis",   $axis);
$smarty->display("inc_edit_supervision_graph_axis_label.tpl");

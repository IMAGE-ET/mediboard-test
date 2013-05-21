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

$supervision_graph_pack_id = CValue::get("supervision_graph_pack_id");

$pack = new CSupervisionGraphPack();
$pack->load($supervision_graph_pack_id);
$pack->loadRefsNotes();

$smarty = new CSmartyDP();
$smarty->assign("pack", $pack);
$smarty->display("inc_edit_supervision_graph_pack.tpl");

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

$supervision_graph_pack_id    = CValue::get("supervision_graph_pack_id");
$supervision_graph_to_pack_id = CValue::get("supervision_graph_to_pack_id");
$graph_class                  = CValue::get("graph_class");

$pack = new CSupervisionGraphPack();
$pack->load($supervision_graph_pack_id);

$link = new CSupervisionGraphToPack();
if ($supervision_graph_to_pack_id) {
  $link->load($supervision_graph_to_pack_id);
  $link->loadRefsNotes();
  $graph_class = $link->graph_class;
}
else {
  if (!$graph_class) {
    return;
  }

  $link->graph_class = $graph_class;
  $link->rank = 1;
}

if ($supervision_graph_pack_id) {
  $link->pack_id = $supervision_graph_pack_id;
}

$item = new $graph_class;
if (!$item instanceof CSupervisionTimedEntity) {
  return;
}

$group = CGroups::loadCurrent();
$item->owner_class = $group->_class;
$item->owner_id    = $group->_id;
$items = $item->loadMatchingList();

$smarty = new CSmartyDP();
$smarty->assign("items", $items);
$smarty->assign("pack",  $pack);
$smarty->assign("link",  $link);
$smarty->display("inc_edit_supervision_graph_to_pack.tpl");

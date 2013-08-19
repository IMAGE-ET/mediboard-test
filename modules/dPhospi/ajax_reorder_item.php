<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$item_id_move = CValue::get("item_id_move");
$direction    = CValue::get("direction");
$item_id      = CValue::getOrSession("item_id");

$item = new CItemPrestation;
$item->load($item_id_move);

switch ($direction) {
  case "up"  :
    $item->rank--;
    break;
  case "down":
    $item->rank++;
}

$item_to_move = new CItemPrestation;
$item_to_move->object_class = $item->object_class;
$item_to_move->object_id    = $item->object_id;
$item_to_move->rank         = $item->rank;
$item_to_move->loadMatchingObject();

if ($item_to_move->_id) {
  $direction == "up" ? $item_to_move->rank++ : $item_to_move->rank--;
  $item_to_move->store();
}

$item->store();

$prestation = new $item->object_class;
$prestation->load($item->object_id);

$items = $prestation->loadBackRefs("items", "rank");

$i = 1;
foreach ($items as $item) {
  $item->rank = $i;
  $item->store();
  $i++;
}

$item = new CItemPrestation;
$item->load($item_id);

$smarty = new CSmartyDP;

$smarty->assign("item"      , $item);
$smarty->assign("items"     , $items);
$smarty->assign("prestation", $prestation);
$smarty->assign("item_id"   , $item_id);

$smarty->display("inc_list_items_prestation.tpl");

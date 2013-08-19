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

$item_id      = CValue::getOrSession("item_id");
$object_class = CValue::getOrSession("object_class");
$object_id    = CValue::getOrSession("object_id");

$item = new CItemPrestation;
$item->load($item_id);
$item->loadRefsNotes();

if (!$item->_id) {
  $item->object_class = $object_class;
  $item->object_id    = $object_id;
  $item->rank         = 1;
  
  if ($object_class == "CPrestationJournaliere") {
    /** @var CPrestationJournaliere $prestation */
    $prestation = new $object_class;
    $prestation->load($object_id);
    $item->rank = ($prestation->countBackRefs("items") + 1);
  }
}

$smarty = new CSmartyDP;

$smarty->assign("item", $item);

$smarty->display("inc_edit_item_prestation.tpl");


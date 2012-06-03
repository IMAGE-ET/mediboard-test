<?php /* $Id: vw_prestations.php $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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
    $prestation = new $object_class;
    $prestation->load($object_id);
    $item->rank = ($prestation->countBackRefs("items") + 1);
  }
}

$smarty = new CSmartyDP;

$smarty->assign("item", $item);

$smarty->display("inc_edit_item_prestation.tpl");

?>
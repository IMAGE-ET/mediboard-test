<?php /* $Id: ajax_lit_liaison_item_autocomplete.php $ */

/**
 * @package Mediboard
 * @subpackage Hospi
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$keywords = CValue::get("keywords", "%%");
$lit_id   = CValue::get("lit_id");

$lit = new CLit;
$lit->load($lit_id);

$liaisons_items = $lit->loadRefsLiaisonsItems();

$items_prestations = CMbObject::massLoadFwdRef($liaisons_items, "item_prestation_id");
$items_prestations_ids = CMbArray::pluck($items_prestations, "object_id");

// Un niveau unique par prestation
$where = array();
$where["object_id"] = CSQLDataSource::prepareNotIn($items_prestations_ids);
$where["object_class"] = " = 'CPrestationJournaliere'";
$item_prestation = new CItemPrestation;
$items_prestations = $item_prestation->seek($keywords, $where);

$items_by_prestation = array();
$prestations = array();

foreach ($items_prestations as $_item_prestation) {
  if (!isset($items_by_prestation[$_item_prestation->object_id])) {
    $items_by_prestation[$_item_prestation->object_id] = array();
  }
  $items_by_prestation[$_item_prestation->object_id][$_item_prestation->rank] = $_item_prestation;
  
  if (!isset($prestations[$_item_prestation->object_id])) {
    $prestations[$_item_prestation->object_id] = $_item_prestation->loadRefObject();
  }
}

foreach ($items_by_prestation as &$_items) {
  ksort($_items);
}

$smarty = new CSmartyDP;

$smarty->assign("items_by_prestation", $items_by_prestation);
$smarty->assign("prestations", $prestations);

$smarty->display("inc_lit_liaison_item_autocomplete.tpl");
?>
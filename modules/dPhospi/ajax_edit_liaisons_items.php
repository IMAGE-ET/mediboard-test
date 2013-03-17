<?php /* $Id: CLitLiaisonItem.class.php $ */

/**
 * @package Mediboard
 * @subpackage Hospi
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$lit_id = CValue::get("lit_id");

$lit_liaison_item = new CLitLiaisonItem;

$lit_liaison_item->lit_id = $lit_id;

$lits_liaisons_items = $lit_liaison_item->loadMatchingList();

CMbObject::massLoadFwdRef($lits_liaisons_items, "item_prestation_id");

foreach ($lits_liaisons_items as $_lit_liaison_item) {
  $_lit_liaison_item->loadRefItemPrestation();
}

$smarty = new CSmartyDP;

$smarty->assign("lits_liaisons_items", $lits_liaisons_items);
$smarty->assign("lit_id", $lit_id);

$smarty->display("inc_edit_liaisons_items.tpl");
?>
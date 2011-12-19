<?php /* $Id: vw_prestations.php $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prestation = mbGetObjectFromGet("object_class", "object_id");
$item_id    = CValue::getOrSession("item_id");

$items = $prestation->loadBackRefs("items", "rank");

$item = new CItemPrestation;
$item->load($item_id);

$smarty = new CSmartyDP;

$smarty->assign("item"      , $item);
$smarty->assign("items"     , $items);
$smarty->assign("item_id"   , $item_id);
$smarty->assign("prestation", $prestation);

$smarty->display("inc_list_items_prestation.tpl");

?>
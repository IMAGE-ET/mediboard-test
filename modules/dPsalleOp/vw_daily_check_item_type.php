<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $g;
$can->needsAdmin();

$item_type_id = mbGetValueFromGetOrSession('item_type_id');

$item_type = new CDailyCheckItemType;
$item_type->load($item_type_id);

$item_category = new CDailyCheckItemCategory;
$item_categories_list = $item_category->loadList(null, 'title');

foreach($item_categories_list as $cat) {
  $cat->loadBackRefs('item_types', 'title');
  foreach($cat->_back['item_types'] as $id => $type) {
    if ($type->group_id != $g) {
      unset($cat->_back['item_types'][$id]);
    }
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("item_type", $item_type);
$smarty->assign("item_categories_list", $item_categories_list);
$smarty->display("vw_daily_check_item_type.tpl");

?>
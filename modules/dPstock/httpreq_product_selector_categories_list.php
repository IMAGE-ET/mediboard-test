<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$keywords = mbGetValueFromGet('keywords');
$category_id = mbGetValueFromGet('category_id');
$selected_category = mbGetValueFromGet('selected_category');

// Loads the required Category and the complete list
$category = new CProductCategory();
$total = null;
$count = null;

if ($keywords) {
  $where = array();
  $where['name'] = "LIKE '%$keywords%'";
  $list_categories = $category->loadList($where, 'name', 20);
  $total = $category->countList($where);
} else {
  $list_categories = $category->loadList(null, 'name');
  $total = count($list_categories);
}
$count = count($list_categories);
if ($total == $count) $total = null;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('list_categories', $list_categories);
$smarty->assign('category_id', $category_id);
$smarty->assign('selected_category', $selected_category);
$smarty->assign('count', $count);
$smarty->assign('total', $total);

$smarty->display('inc_product_selector_categories_list.tpl');

?>
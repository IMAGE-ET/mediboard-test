<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkEdit();

// Gets objects ID from Get or Session
$product_id  = CValue::getOrSession('product_id');
$societe_id  = CValue::getOrSession('societe_id');
$category_id = CValue::getOrSession('category_id');
$keywords    = CValue::getOrSession('keywords');
$letter      = CValue::getOrSession('letter', "%");
$show_all    = CValue::getOrSession('show_all');

$filter = new CProduct;
$filter->societe_id = $societe_id;
$filter->category_id = $category_id;

// Loads the required Category the complete list
$category = new CProductCategory();
$list_categories = $category->loadList(null, 'name');

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('product_id',      $product_id);
$smarty->assign('list_categories', $list_categories);
$smarty->assign('filter',          $filter);
$smarty->assign('keywords',        $keywords);
$smarty->assign('letter',          $letter);
$smarty->assign('show_all',        $show_all);

$smarty->display('vw_idx_product.tpl');

?>
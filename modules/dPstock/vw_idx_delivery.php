<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $g;
CCanDo::checkEdit();

$category_id = CValue::getOrSession('category_id');

// Loads the required Category the complete list
$category = new CProductCategory();
$list_categories = $category->loadList(null, 'name');

$list_services = CProductStockGroup::getServicesList();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('category_id',     $category_id);
$smarty->assign('list_categories', $list_categories);
$smarty->assign('list_services',   $list_services);

$smarty->display('vw_idx_delivery.tpl');

?>
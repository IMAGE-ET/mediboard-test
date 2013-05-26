<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Stock
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$service_id  = CValue::getOrSession('service_id');
$category_id = CValue::getOrSession('category_id');

// Services list
$list_services = CProductStockGroup::getServicesList();

// Loads the required Category and the complete list
$category = new CProductCategory();
$list_categories = $category->loadList(null, 'name');

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('service_id', $service_id);
$smarty->assign('list_services', $list_services);

$smarty->assign('category_id', $category_id);
$smarty->assign('list_categories', $list_categories);

$smarty->display('vw_idx_discrepancy.tpl');


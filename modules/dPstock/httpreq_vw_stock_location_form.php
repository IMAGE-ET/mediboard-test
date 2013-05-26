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

$stock_location_id = CValue::getOrSession('stock_location_id');

$stock_location = new CProductStockLocation();
$stock_location->load($stock_location_id);
$stock_location->loadRefsStocks();
$stock_location->loadTargetObject();
$stock_location->_type = $stock_location->_id ? $stock_location->_ref_object->_guid : null;

$classes = $stock_location->_specs["object_class"]->_locales;
$where = array(
  "group_id" => "='".CGroups::loadCurrent()->_id."'"
);

$types = array();
foreach ($classes as $_class => $_locale) {
  $object = new $_class;
  $types[$_locale] = $object->loadListWithPerms(PERM_READ, $where);
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign('stock_location', $stock_location);
$smarty->assign('types', $types);
$smarty->assign("host_group_id", CProductStockGroup::getHostGroup());
$smarty->display('inc_form_stock_location.tpl');

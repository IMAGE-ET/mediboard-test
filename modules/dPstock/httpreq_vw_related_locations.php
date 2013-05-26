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
 
CCanDo::checkRead();

$owner_guid  = CValue::get('owner_guid');
$exclude_location_id  = CValue::get('exclude_location_id');

$parts = explode("-", $owner_guid);
$where = array(
  "object_class" => " = '{$parts[0]}'",
  "object_id" => " = '{$parts[1]}'",
);

if ($exclude_location_id) {
  $where["stock_location_id"] = " != '$exclude_location_id'";
}

$location = new CProductStockLocation;
$locations = $location->loadList($where);

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign('locations', $locations);
$smarty->display('inc_autocomplete_related_locations.tpl');

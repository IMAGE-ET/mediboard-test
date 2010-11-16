<?php /* $Id: httpreq_vw_references_list.php 9929 2010-08-30 13:51:07Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 9929 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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

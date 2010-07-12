<?php 
/* $Id: httpreq_vw_list_categories.php 9329 2010-07-01 12:48:40Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision: 9329 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */
 
CCanDo::checkRead();

$barcode = CValue::get("barcode");

$parsed = CBarcodeParser::parse($barcode);
$comp = $parsed['comp'];
$matches = array();

if (isset($comp['lot'])) {
  $object = new CProductOrderItemReception;
  $matches = $object->seek($comp['lot']);
}

if ( empty($matches)) {
  $object = new CProduct;
  
  $keys = array("scc-prod", "ref", "cip", "raw");
  $values = array_intersect_key($comp, array_flip($keys));
  
  foreach ($values as $field=>$value) {
    $matches += $object->seek($value);
  }
  
  $reception = new CProductOrderItemReception;
  $ljoin = array(
    "product_order_item" => "product_order_item_reception.order_item_id = product_order_item.order_item_id",
    "product_reference" => "product_order_item.reference_id = product_reference.reference_id",
  );
  
  foreach ($matches as $_match) {
    $_match->loadRefsFwd();
    
    $where = array(
      "product_reference.product_id"=>"= '$_match->_id'"
    );
    
    $_match->_lots = $reception->loadList($where, null, null, null, $ljoin);
  }
}

$smarty = new CSmartyDP();
$smarty->assign("parsed", $parsed);
$smarty->assign("matches", $matches);
$smarty->display("inc_parse_barcode.tpl");

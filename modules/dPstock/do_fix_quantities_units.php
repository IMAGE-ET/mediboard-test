<?php /* $Id: httpreq_vw_delivery_stock_item.php 11025 2011-01-05 13:25:17Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 11025 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

set_time_limit(1200);

function append_user_logs(&$all_logs, $logs) {
  foreach($logs as $_log) {
    $all_logs[$_log->date][$_log->_id] = $_log;
  }
}

//CSQLDataSource::$trace = true;

// ---------------------

CProductOrderItemReception::$_load_lite = true;
$oir = new CProductOrderItemReception;
$oir_where = array(
  "product_order_item_reception.units_fixed" => "= '0'",
);
$oir_ljoin = array(
  "product_order_item" => "product_order_item_reception.order_item_id = product_order_item.order_item_id",
  "product_reference"  => "product_order_item.reference_id = product_reference.reference_id",
  "product"            => "product_reference.product_id = product.product_id",
);

// ---------------------

CProductOrderItem::$_load_lite = true;
$oi = new CProductOrderItem;
$oi_where = array(
  "product_order_item.units_fixed" => "= '0'",
);
$oi_ljoin = array(
  "product_reference"  => "product_order_item.reference_id = product_reference.reference_id",
  "product"            => "product_reference.product_id = product.product_id",
  "user_log"           => "product_order_item.order_item_id = user_log.object_id AND user_log.object_class = 'CProductOrderItem'",
);

// ---------------------

CProductReference::$_load_lite = true;
$ref = new CProductReference;
$ref_where = array(
  "product_reference.units_fixed = '0'",
);


$product = new CProduct;
$pr_ljoin = array(
  "product_reference"  => "product.product_id = product_reference.product_id",
);
$product->load(1177);
$products = array($product); //$product->loadList($ref_where, null, 50, null, $pr_ljoin);
mbTrace(count($products));
// ---------------------

// Chargement des CProduct
foreach($products as $_product) {
	mbTrace($_product->_view, " ######################## ");
	$old_date = mbDateTime();
  
  //$_references = $_product->loadRefsReferences();
  $ref_where["product_reference.product_id"] = "= '$_product->_id'";
  $_references = $ref->loadList($ref_where);
	
	if (empty($_references)) continue;
	
  $product_logs = array();
	
	// LOG CProduct quantity
	$product_logs_quantity = $_product->loadLogsForField("quantity", true, null, true);
  append_user_logs($product_logs, $product_logs_quantity);
	
	foreach($_references as $_reference) {
    $quantity_product   = $_product->quantity;
		$quantity_reference = $_reference->orig_quantity;
    $price_reference    = $_reference->orig_price;
		
		$logs = $product_logs;
		
    // LOG CProductReference quantity AND price
    $reference_logs = $_reference->loadLogsForField(array("quantity", "price"), true, null, true);
		append_user_logs($logs, $reference_logs);
  
	  // Tri par date decroissante
	  krsort($logs);
		
		$dummy_log = new CUserLog;
		$dummy_log->date = "1970-01-01 00:00:00";
		$logs[$dummy_log->date][] = $dummy_log;
	  
	  foreach($logs as $_date => $_logs) {
	    foreach($_logs as $_log) {
				if ($_date == $old_date) {
					mbTrace("DATE IDEM");
					continue;
				}
				
        mbTrace($_log->getOldValues(), "$_log->object_class : $old_date >> $_date");
        
        // CProductOrderItem : quantity and price
        // need to join on the latest log :(
        $oi_where["user_log.date"] = " > '$_date' AND user_log.date <= '$old_date'";
        $oi_where["product.product_id"] = "= '$_product->_id'";
        $ois = $oi->loadList($oi_where, "user_log.date DESC", null, "product_order_item.order_item_id", $oi_ljoin);
        //mbTrace("OI  ".count($ois));
				
				foreach($ois as $_oi) {
					mbTrace(sprintf("%d\t >> %d\t", $_oi->quantity, $_oi->quantity * $quantity_reference * $quantity_product), "OI  QTY");
          $_oi->quantity = $_oi->quantity * $quantity_reference * $quantity_product;
					
          mbTrace(sprintf("%.5f\t >> %.5f\t", $_oi->unit_price, $_oi->unit_price / ($quantity_reference * $quantity_product)), "OI  PRI");
          $_oi->unit_price = $_oi->unit_price / ($quantity_reference * $quantity_product);
					
					$_oi->units_fixed = 1;
					$_oi->store();
				}
				
				// CProductOrderItemReception : quantity
        $oir_where["product_order_item_reception.date"] = " > '$_date' AND product_order_item_reception.date <= '$old_date'";
        $oir_where["product.product_id"] = "= '$_product->_id'";
				$oirs = $oir->loadList($oir_where, null, null, null, $oir_ljoin);
        //mbTrace("OIR ".count($oirs));
        
        foreach($oirs as $_oir) {
          mbTrace(sprintf("%d\t >> %d\t", $_oir->quantity, $_oir->quantity * $quantity_reference * $quantity_product), "OIR QTY");
          $_oir->quantity = $_oir->quantity * $quantity_reference * $quantity_product;
          
          $_oir->units_fixed = 1;
          $_oir->store();
        }
				
				// Update values and date
        $old_date = $_date;
        $old_values = $_log->getOldValues();
        
        if (empty($old_values)) {
        	mbTrace("OLD VALUES EMPTY");
        	continue;
				}
				
        switch ($_log->object_class) {
          case "CProduct":
            $quantity_product   = CValue::read($old_values, "quantity", $quantity_product);
            break;
            
          case "CProductReference":
            $quantity_reference = CValue::read($old_values, "quantity", $quantity_reference);
            $price_reference    = CValue::read($old_values, "price", $price_reference);
						
						// Update the log
            if (isset($old_values["quantity"])) {
              if (isset($old_values["orig_quantity"])) {
                $old_values["quantity"] = $old_values["orig_quantity"] * $quantity_product;
              }
              else {
                $old_values["orig_quantity"] = $old_values["quantity"];
                $old_values["quantity"] = $old_values["quantity"] * $quantity_product;
              }
            }
						
						if (isset($old_values["price"])) {
							if (isset($old_values["orig_price"])) {
                $old_values["price"] = $old_values["orig_price"] / ($quantity_product * $quantity_reference);
							}
							else {
								$old_values["orig_price"] = $old_values["price"];
                $old_values["price"] = $old_values["price"] / ($quantity_product * $quantity_reference);
							}
						}
						
						//mbTrace($old_values);
						$new_values = json_encode($old_values);
						$_log->extra = $new_values;
						$_log->store();
						
            break;
        }
	    }
	  }
		
	  // process last CProductReference
    $_reference->units_fixed = 1;
		$_reference->store();
	}
}

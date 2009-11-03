<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsRead();

$code = CValue::getOrSession('code');

$codes = array();
$products = array();

if (strlen($code) > 2) {
	$where = array(
	  'code' => "LIKE '%$code%'"
	);
	
	$delivery = new CProductDeliveryTrace();
	$list_deliveries = $delivery->loadList($where, 'date_delivery');
	
	$reception = new CProductOrderItemReception();
	$list_order_reception = $reception->loadList($where, 'date');

	foreach ($list_order_reception as $trace) {
	  if (!isset($codes[$trace->code])) {
	    $codes[$trace->code] = array();
	  }
    $trace->loadRefsFwd();
	  
	  if (!isset($products[$trace->code])) {
      $trace->loadRefOrderItem();
      $trace->_ref_order_item->loadReference();
      $trace->_ref_order_item->_ref_reference->loadRefsFwd();
      $products[$trace->code] = $trace->_ref_order_item->_ref_reference->_ref_product;
	  }
	  
	  // date_reception
		if ($trace->date) {
		  if (!isset($codes[$trace->code][$trace->date])) {
		    $codes[$trace->code][$trace->date] = array(
          'reception' => null,
          'delivery' => null,
          'delivery_reception' => null,
          'administration' => null
		    );
		  }
		  $codes[$trace->code][$trace->date]['reception'] = $trace;
		}
	}
	
	foreach ($list_deliveries as $trace) {
		if (!isset($codes[$trace->code])) {
			$codes[$trace->code] = array();
		}
		$trace->loadRefsFwd();
    $trace->_ref_delivery->loadRefsFwd();
		
	  if (!isset($products[$trace->code])) {
	  	$stock = $trace->getStock();
	  	$stock->loadRefsFwd();
      $products[$trace->code] = $stock->_ref_product;
    }
		
		// date_delivery
		if ($trace->date_delivery) {
		  if (!isset($codes[$trace->code][$trace->date_delivery])) {
		    $codes[$trace->code][$trace->date_delivery] = array(
          'reception' => null,
          'delivery' => null,
          'delivery_reception' => null,
          'administration' => null
	      );
		  }
		  $codes[$trace->code][$trace->date_delivery]['delivery'] = $trace;
		}
		
	  
	  // date_delivery_reception
		if ($trace->date_reception) {
		  if (!isset($codes[$trace->code][$trace->date_reception])) {
		    $codes[$trace->code][$trace->date_reception] = array(
          'reception' => null,
          'delivery' => null,
          'delivery_reception' => null,
					'administration' => null
	      );
		  }
		  $codes[$trace->code][$trace->date_reception]['delivery_reception'] = $trace;
		}
	}
}

foreach ($codes as &$_code) {
	ksort($_code);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('codes',    $codes);
$smarty->assign('code',     $code);
$smarty->assign('products', $products);

$smarty->display('vw_traceability.tpl');

?>
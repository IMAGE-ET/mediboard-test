<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CProductDeliveryTrace extends CMbObject {
  // DB Table key
  var $delivery_trace_id  = null;

  // DB Fields
  var $delivery_id    = null;
  var $date_delivery  = null;
  var $date_reception = null;
  var $code           = null;
  var $quantity       = null;

  // Object References
  //    Single
  var $_ref_delivery  = null;
  
  var $_date_min      = null;
  var $_date_max      = null;
  
  var $_deliver       = null;
  var $_undeliver     = null;
  var $_receive       = null;
  var $_unreceive     = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_delivery_trace';
    $spec->key   = 'delivery_trace_id';
    return $spec;
  }

  function getSpecs() {
    $specs = parent::getSpecs();
    $specs['delivery_id']    = 'notNull ref class|CProductDelivery';
    $specs['code']           = 'str maxLength|32';
    $specs['quantity']       = 'notNull num';
    $specs['date_delivery']  = 'dateTime';
    $specs['date_reception'] = 'dateTime';
    $specs['_date_min']      = 'notNull dateTime';
    $specs['_date_max']      = 'notNull dateTime moreThan|_date_min';
    return $specs;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_ref_delivery->updateFormFields();
    $this->_view = $this->_ref_delivery->_view;
  }
  
  function store() {
  	$this->completeField('delivery_id');
  	$this->completeField('quantity');
    $this->loadRefsFwd();
    $stock = $this->getStock();
    $stock->loadRefsFwd();
    
    if ($this->date_delivery) {
      if (!CAppUI::conf('dPstock CProductStockGroup infinite_quantity') && (($this->quantity == 0) || ($stock->quantity < $this->quantity))) {
        return 'Impossible de délivrer ce nombre de '.$stock->_ref_product->_unit_title;
      }
    }
    
    if (!CAppUI::conf('dPstock CProductStockGroup infinite_quantity')) {
	    // If we want to deliver, just provide a delivery date
	    if ($this->date_delivery) {
	      $stock->quantity -= $this->quantity;
	      if ($msg = $stock->store()) return $msg;
	    }
	    
	    // Un-deliver
	    else if ($this->_undeliver) {
	      $stock->quantity += $this->quantity;
	      $this->_undeliver = null;
	      
	      if ($msg = $stock->store()) return $msg;
	      return $this->delete();
	    }
    }
    
    // If we want to receive, just provide a reception date
    if ($this->date_reception) {
      $stock_service = new CProductStockService();
      $stock_service->product_id = $stock->product_id;
      $stock_service->service_id = $this->_ref_delivery->service_id;
      
      // If a stock already exist, its quantity is updated
      if ($stock_service->loadMatchingObject()) {
        $stock_service->quantity += $this->quantity;
      } 
      // if not, the stock is created
      else {
	      $stock_service->order_threshold_min = abs($this->quantity) + 1;
	      $stock_service->order_threshold_max = $stock_service->order_threshold_min * 2;
        $stock_service->quantity = $this->quantity;
      }

      if ($msg = $stock_service->store()) return $msg;
    }
    
    // Un-receive
    else if ($this->_unreceive) {
      $stock_service = new CProductStockService();
      $stock_service->product_id = $stock->product_id;
      $stock_service->service_id = $this->_ref_delivery->service_id;
      
      if ($stock_service->loadMatchingObject()) {
        $stock_service->quantity -= $this->quantity;
        if ($msg = $stock_service->store()) return $msg;
      }
      
      $this->_unreceive = null;
      $this->date_reception = '';
    }

    return parent::store();
  }
  
  function getStock() {
    $this->_ref_delivery->loadRefStock();
    return $this->_ref_delivery->_ref_stock;
  }
  
  function loadRefsFwd() {
    $this->_ref_delivery = new CProductDelivery();
    $this->_ref_delivery = $this->_ref_delivery->getCached($this->delivery_id); 
  }

  function getPerm($permType) {
    $stock = $this->getStock();
    if(!$this->_ref_service) {
      $this->loadRefsFwd();
    }
    if ($this->_ref_service) {
      return ($stock->getPerm($permType) && $this->_ref_service->getPerm($permType));
    } else {
      return ($stock->getPerm($permType));
    }
  }
}
?>
<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */

class CProductDelivery extends CMbObject {
  // DB Table key
  var $delivery_id  = null;

  // DB Fields
  var $stock_id       = null;
  var $date_dispensation = null;
  var $date_delivery  = null;
  var $date_reception = null;
  var $quantity       = null;
  var $code           = null; // Lot number, lapsing date
  var $service_id     = null;
  var $patient_id     = null;

  // Object References
  //    Single
  var $_ref_stock     = null;
  var $_ref_service   = null;
  
  var $_date_min      = null;
  var $_date_max      = null;
  
  var $_deliver       = null;
  var $_undeliver     = null;
  var $_receive       = null;
  var $_unreceive     = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_delivery';
    $spec->key   = 'delivery_id';
    return $spec;
  }

  function getSpecs() {
    $specs = parent::getSpecs();
    return array_merge($specs, array (
      'stock_id'          => 'notNull ref class|CProductStockGroup',
      'date_dispensation' => 'notNull dateTime',
      'date_delivery'     => 'dateTime',
      'date_reception'    => 'dateTime',
      'quantity'          => 'notNull num',
      'code'              => 'str maxLength|32',
      'service_id'        => 'notNull ref class|CService',
      'patient_id'        => 'ref class|CPatient',
      '_date_min'         => 'notNull dateTime',
      '_date_max'         => 'notNull dateTime',
    ));
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = $this->quantity.'x '.$this->_ref_stock->_view.($this->service_id?" pour le service '{$this->_ref_service->_view}'":'');
  }
  
  function store() {
    // If it's a new object, is is a dispensation
    if (!$this->_id) {
      $this->date_dispensation = mbDateTime();
    }
    
    // TODO: supprimer ce hack
    $code = $this->code;
    $this->load();
    $this->code = $code;
    if ($msg = $this->check()) return $msg;
    
    $this->loadRefsFwd();
    
    // If we want to deliver and if it hasn't been delivered yet
    if ($this->_deliver && !$this->date_delivery) {
      $this->_ref_stock->quantity -= $this->quantity;
      $this->_ref_stock->store();
      $this->_deliver = null;
      $this->date_delivery = mbDateTime();
    }
    
    // Un-deliver
    else if ($this->_undeliver) {
      $this->_ref_stock->quantity += $this->quantity;
      $this->_ref_stock->store();
      
      $this->_undeliver = null;
      $this->date_delivery = '';
    }
    
    // If we want to receive and if it hasn't been reveived yet
    if ($this->_receive && !$this->date_reception) {
      $stock_service = new CProductStockService();
      $stock_service->product_id = $this->_ref_stock->product_id;
      $stock_service->service_id = $this->service_id;
      
      if ($stock_service->loadMatchingObject()) {
        $stock_service->quantity += $this->quantity;
      } else if ($this->quantity > 0) {
        $stock_service->quantity = $this->quantity;
      }

      if ($msg = $stock_service->store()) {
        return $msg;
      }
      $this->_receive = null;
      $this->date_reception = mbDateTime();
    }
    else if ($this->_unreceive) {
      $stock_service = new CProductStockService();
      $stock_service->product_id = $this->_ref_stock->product_id;
      $stock_service->service_id = $this->service_id;
      
      if ($stock_service->loadMatchingObject()) {
        $stock_service->quantity -= $this->quantity;
      }
      
      if ($msg = $stock_service->store()) {
        return $msg;
      }
      $this->_unreceive = null;
      $this->date_reception = '';
    }


    return parent::store();
  }
  
  function check() {
  	$this->loadRefsFwd();
  	
  	mbTrace($this);
  	
  	if ($this->_deliver) {
    	if (($this->quantity == 0) || ($this->_ref_stock->quantity < $this->quantity)) {
        return 'impossible de délivrer ce nombre d\'articles';
      }
  	}
  	
  	return parent::check();
  }

  function loadRefsFwd() {
    $this->_ref_stock = new CProductStockGroup();
    $this->_ref_stock->load($this->stock_id);

    $this->_ref_service = new CService();
    $this->_ref_service->load($this->service_id);
  }

  function getPerm($permType) {
    if(!$this->_ref_stock || !$this->_ref_service) {
      $this->loadRefsFwd();
    }
    if ($this->_ref_service) {
      return ($this->_ref_stock->getPerm($permType) && $this->_ref_service->getPerm($permType));
    } else {
      return ($this->_ref_stock->getPerm($permType));
    }
  }
}
?>
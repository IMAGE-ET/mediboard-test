<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CProductDelivery extends CMbObject {
  // DB Table key
  var $delivery_id  = null;

  // DB Fields
  
  // Source
  var $stock_id       = null;
  var $stock_class    = null;
  
  // Target
  var $service_id     = null;
  var $patient_id     = null;
  //var $prescription_id = null;
  
  var $date_dispensation = null;
  var $date_delivery  = null;
  var $datetime_min  = null;
  var $datetime_max  = null;
  
  var $quantity       = null;
  var $order          = null;
  var $manual         = null;
  var $comments       = null;
  var $type           = null;

  // Object References
  //    Single
  /**
   * @var CProductStockGroup 
   */
  var $_ref_stock     = null;
  var $_ref_stock_service = null;
  /**
   * @var CService
   */
  var $_ref_service = null;
  var $_ref_patient = null;
  
  var $_ref_location_source = null;
  var $_ref_location_target = null;
  
  var $_ref_delivery_traces = null;
  
  var $_date_min      = null;
  var $_date_max      = null;
  var $_datetime_min      = null;
  var $_datetime_max      = null;
  var $_delivered     = null;
  var $_auto_deliver  = null;
  var $_make_delivery_trace = null;
  
  var $_products                  = null;
  var $_pn13_initiateur_group_id  = null; // group initiateur du message PN13
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_delivery';
    $spec->key   = 'delivery_id';
    //$spec->xor["service"] = array("service_id", "comments");
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    
    // Source
    $specs['stock_id']          = 'ref class|CProductStock meta|stock_class'; // can be null when the stock doesn't exist in the group
    $specs['stock_class']       = 'str notNull class show|0';
    
    // Target
    $specs['service_id']        = 'ref class|CService'; // >/dev/null
    $specs['patient_id']        = 'ref class|CPatient';
    //$specs['prescription_id']   = 'ref class|CPrescription';
    
    $specs['date_dispensation'] = 'dateTime notNull';
    $specs['date_delivery']     = 'dateTime';
    
    $specs['datetime_min']      = 'dateTime notNull';
    $specs['datetime_max']      = 'dateTime notNull moreEquals|datetime_min';
    
    $specs['quantity']          = 'num notNull';
    $specs['order']             = 'bool default|0';
    $specs['manual']            = 'bool default|0';
    $specs['comments']          = 'text';
    $specs['type']              = 'enum list|other|expired|breakage|loss|gift|discrepancy|notused|toomany';
    
    $specs['_date_min']         = 'date notNull';
    $specs['_date_max']         = 'date notNull moreEquals|_date_min';
    
    $specs['_datetime_min']         = 'dateTime notNull';
    $specs['_datetime_max']         = 'dateTime notNull moreEquals|_datetime_min';
    
    return $specs;
  }

  function updateFormFields() {
    parent::updateFormFields();
    
    $this->loadRefStock();
    $this->_view = $this->quantity.'x '.$this->_ref_stock->_view;
    
    if ($this->service_id) {
      $this->loadRefService();
      $this->_view .= ($this->service_id?" pour le service '{$this->_ref_service->_view}'":'');
    }
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['delivery_traces'] = 'CProductDeliveryTrace delivery_id';
    $backProps['echanges_phast']  = 'CPhastEchange object_id';
    return $backProps;
  }
  
  function countDelivered() {
    $this->loadRefsBack();
    $sum = 0;
    foreach ($this->_ref_delivery_traces as $trace) {
      if ($trace->date_delivery)
        $sum += $trace->quantity;
    }
    return $sum;
  }
  
  function isDelivered() {
    return $this->_delivered = ($this->countDelivered() >= $this->quantity);
  }
  
  function isReceived() {
    $this->loadRefsDeliveryTraces();
    
    $sum = 0;
    foreach ($this->_ref_delivery_traces as $trace) {
      if ($trace->date_reception) {
        $sum += $trace->quantity;
      }
    }
    
    return ($sum >= $this->quantity);
  }
  
  function loadRefsBack(){
    $this->loadRefsDeliveryTraces();
  }
  
  function loadRefsDeliveryTraces(){
    return $this->_ref_delivery_traces = $this->loadBackRefs('delivery_traces');
  }

  function loadRefTargetStock(){
    $this->loadRefStock();
    
    $stock_service = new CProductStockService();
    
    $this->completeField("service_id");
    
    if ($this->service_id) {
      $stock_service->product_id = $this->_ref_stock->product_id;
      $stock_service->object_id = $this->service_id;
      $stock_service->object_class = "CService"; // XXX
      $stock_service->loadMatchingObject();
    }
    
    return $this->_ref_stock_service = $stock_service;
  }
  
  function loadRefService(){
    return $this->_ref_service = $this->loadFwdRef("service_id", true);  
  }
  
  function loadRefPatient(){
    return $this->_ref_patient = $this->loadFwdRef("patient_id", true);  
  }

  function loadRefStock(){
    return $this->_ref_stock = $this->loadFwdRef("stock_id", true);
  }
  
  function loadRefsFwd() {
    $this->loadRefStock();
    $this->loadRefService();
    $this->loadRefPatient();
  }
  
  function updateDBFields(){
    parent::updateDBFields();
    
    $this->completeField("stock_class");
    if (!$this->stock_class) {
      $this->stock_class = "CProductStockGroup";
    }
  }
  
  function store(){
    $is_new = !$this->_id;
    
    if ($is_new) {
      if (!$this->datetime_min) {
        $this->datetime_min = $this->date_dispensation;
      }
      
      if (!$this->datetime_max) {
        $this->datetime_max = $this->date_dispensation;
      }
    }
    
    if ($msg = parent::store()){
      return $msg;
    }
    
    if (!$is_new) return;
    
    if ($this->manual) {
      $delivery_trace = new CProductDeliveryTrace;
      $delivery_trace->delivery_id = $this->_id;
      $delivery_trace->quantity = $this->quantity;
      $delivery_trace->date_delivery = $this->date_delivery ? $this->date_delivery : mbDateTime();
      $delivery_trace->date_reception = $delivery_trace->date_delivery;
      if ($msg = $delivery_trace->store()) {
        CAppUI::setMsg("La commande a été validée, mais elle n'a pas pu etre délivrée automatiquement pour la raison suivante: <br />$msg", UI_MSG_WARNING);
      }
      else {
        CAppUI::setMsg("CProductDeliveryTrace-msg-create");
      }
    }
    
    $this->loadRefStock();
    $this->_ref_stock->loadRefsFwd();
    if ($this->_auto_deliver || $this->_ref_stock->_ref_product->auto_dispensed) {
      $this->date_dispensation = mbDateTime();
      $this->order = 0;
      return parent::store();
    }
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
    return true;
  }
  
  function delete(){
    $this->completeField("manual");
    
    if ($this->manual) {
      $traces = $this->loadBackRefs("delivery_traces");
      foreach($traces as $_trace) {
        $_trace->delete();
      }
    }
    
    return parent::delete();
  }
}
?>
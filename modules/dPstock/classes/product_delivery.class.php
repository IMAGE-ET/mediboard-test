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
  var $stock_id       = null;
  var $date_dispensation = null;
  var $date_delivery  = null;
  var $quantity       = null;
  var $service_id     = null;
  var $patient_id     = null;
  var $order          = null;
  var $manual         = null;
  var $comments       = null;

  // Object References
  //    Single
  /** 
   * @var CProductStockGroup 
   */
  var $_ref_stock     = null;
  var $_ref_service   = null;
  
  var $_ref_delivery_traces = null;
  
  var $_date_min      = null;
  var $_date_max      = null;
  var $_delivered     = null;
  var $_auto_deliver  = null;
  var $_make_delivery_trace = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_delivery';
    $spec->key   = 'delivery_id';
    //$spec->xor["service"] = array("service_id", "comments");
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs['stock_id']          = 'ref class|CProductStockGroup'; // can be null when the stock doesn't exist in the group
    $specs['date_dispensation'] = 'dateTime notNull';
    $specs['date_delivery']     = 'dateTime';
    $specs['quantity']          = 'num notNull';
    $specs['service_id']        = 'ref class|CService';
    $specs['patient_id']        = 'ref class|CPatient';
    $specs['order']             = 'bool default|0';
    $specs['manual']            = 'bool default|0';
    $specs['comments']          = 'text';
    $specs['_date_min']         = 'date notNull';
    $specs['_date_max']         = 'date notNull moreEquals|_date_min';
    return $specs;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = $this->quantity.'x '.$this->_ref_stock->_view.($this->service_id?" pour le service '{$this->_ref_service->_view}'":'');
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['delivery_traces'] = 'CProductDeliveryTrace delivery_id';
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
    $this->loadRefsBack();
    $sum = 0;
    foreach ($this->_ref_delivery_traces as $trace) {
      if ($trace->date_reception)
        $sum += $trace->quantity;
    }
    return ($sum >= $this->quantity);
  }
  
  function loadRefsBack(){
    $this->_ref_delivery_traces = $this->loadBackRefs('delivery_traces');
  }

  function loadRefStock(){
    $this->_ref_stock = $this->loadFwdRef("stock_id", true);
  }
  
  function loadRefService(){
  	$this->_ref_service = $this->loadFwdRef("service_id", true);  
  }
  
  function loadRefPatient(){
    $this->_ref_patient = $this->loadFwdRef("patient_id", true);	
  }
  
  function loadRefsFwd() {
    $this->loadRefStock();
    $this->loadRefService();
    $this->loadRefPatient();
  }
  
  function store(){
    $is_new = !$this->_id;
    
    if ($msg = parent::store()){
      return $msg;
    }
    
    if (!$is_new) return;
    
    if ($this->manual) {
      $delivery_trace = new CProductDeliveryTrace;
      $delivery_trace->delivery_id = $this->_id;
      $delivery_trace->quantity = $this->quantity;
      $delivery_trace->date_delivery = mbDateTime();
      if ($msg = $delivery_trace->store()) {
        return "La commande a été validée, mais elle n'a pas pu etre dispensée automatiquement pour la raison suivante: <br />$msg";
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
}
?>
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
  var $quantity       = null;
  var $service_id     = null;
  var $patient_id     = null;

  // Object References
  //    Single
  var $_ref_stock     = null;
  var $_ref_service   = null;
  
  var $_ref_delivery_traces = null;
  
  var $_date_min      = null;
  var $_date_max      = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_delivery';
    $spec->key   = 'delivery_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs['stock_id']          = 'ref notNull class|CProductStockGroup';
    $specs['date_dispensation'] = 'dateTime notNull';
    $specs['quantity']          = 'num notNull';
    $specs['service_id']        = 'ref notNull class|CService';
    $specs['patient_id']        = 'ref class|CPatient';
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
  	return $this->countDelivered() >= $this->quantity;
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
    $this->_ref_stock = new CProductStockGroup();
    $this->_ref_stock = $this->_ref_stock->getCached($this->stock_id);
  }
  
  function loadRefService(){
  	$this->_ref_service = new CService();
    $this->_ref_service = $this->_ref_service->getCached($this->service_id);  
  }
  
  function loadRefPatient(){
    $this->_ref_patient = new CPatient();
    $this->_ref_patient = $this->_ref_patient->getCached($this->patient_id);	
  }
  
  function loadRefsFwd() {
    $this->loadRefStock();
    $this->loadRefService();
    $this->loadRefPatient();
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
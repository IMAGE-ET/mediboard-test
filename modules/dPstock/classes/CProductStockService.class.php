<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CProductStockService extends CProductStock {
  // DB Fields
  var $service_id = null;
  var $common     = null;

  // Object References
  //    Single
  /**
   * @var CService
   */
  var $_ref_service = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_stock_service';
    $spec->key   = 'stock_id';
    $spec->uniques["product"] = array("service_id", "product_id"/*, "location_id"*/);
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs['service_id'] = 'ref notNull class|CService';
    $specs['common'] = 'bool';
    return $specs;
  }
  
  /**
   * 
   * @param string $code
   * @param int $service_id [optional]
   * @return CProductStockService
   */
  static function getFromCode($code, $service_id = null) {
    $stock = new self();
    
    $where = array();
    $where['product.code'] = "= '$code'";
    if ($service_id) {
      $where['product_stock_service.service_id'] = "= $service_id";
    }
    $ljoin = array();
    $ljoin['product'] = 'product_stock_service.product_id = product.product_id';

    $stock->loadObject($where, null, null, $ljoin);
    return $stock;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "$this->_ref_product ($this->_ref_service)";
  }
  
  function loadRefService(){
    return $this->_ref_service = $this->loadFwdRef("service_id", true);
  }

  function loadRefsFwd(){
    parent::loadRefsFwd();
    $this->loadRefService();
  }
  
  function loadRelatedLocations(){
    $where = array(
      "object_class" => "= 'CService'",
      "object_id"    => "= '$this->service_id'",
    );
    
    $location = new CProductStockLocation;
    return $this->_ref_related_locations = $location->loadList($where, "name");
  }
  
  function check(){
    if ($msg = parent::check()) {
      return $msg;
    }
    
    if ($this->location_id) {
      $this->completeField("service_id");
      $location = $this->loadRefLocation();
      
      if ($location->object_class !== "CService" || $location->object_id != $this->service_id) {
        return "Le stock doit être associé à un emplacement du service '".$this->loadRefService()."'";
      }
    }
  }
  
  function loadRefHost(){
    return $this->loadRefService();
  }
  
  function setHost(CService $host){
    $this->_ref_service = $host;
    $this->service_id = $host->_id;
  }
}

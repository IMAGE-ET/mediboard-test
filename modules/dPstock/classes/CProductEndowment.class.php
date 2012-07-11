<?php /* $Id: product_stock_service.class.php 8121 2010-02-23 10:23:49Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 8121 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CProductEndowment extends CMbObject {
  var $endowment_id = null;
  
  var $name         = null;
  var $service_id   = null;
  var $_duplicate_to_service_id = null;

  // Object References
  var $_ref_service = null;
  var $_ref_endowment_items = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_endowment';
    $spec->key   = 'endowment_id';
    $spec->uniques["unique"] = array("name", "service_id");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["name"]       = "str notNull";
    $props["service_id"] = "ref notNull class|CService autocomplete|nom";
    $props["_duplicate_to_service_id"] = $props["service_id"];
    return $props;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["endowment_items"] = "CProductEndowmentItem endowment_id";
    return $backProps;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "$this->name";
    
    if ($this->_ref_service) {
      $this->_view .= " ({$this->_ref_service->_view})";
    }
  }

  function loadRefsFwd(){
    parent::loadRefsFwd();
    
    $this->loadRefService();
  }
  
  /**
   * @return CService
   */
  function loadRefService(){
    return $this->_ref_service = $this->loadFwdRef("service_id", true);
  }

  function loadRefsBack(){
    $this->loadRefsEndowmentItems();
  }

  function loadRefsEndowmentItems() {
    $ljoin = array(
      "product"                => "product.product_id = product_endowment_item.product_id",
      "product_stock_group"    => "product_stock_group.product_id = product.product_id",
      "product_stock_location" => "product_stock_location.stock_location_id = product_stock_group.location_id"
    );
    
    return $this->_ref_endowment_items = $this->loadBackRefs('endowment_items', "product_stock_location.position, product.name", null, null, $ljoin);
  }
  
  function getPerm($permType) {
    $this->loadRefsFwd();
    
    return parent::getPerm($permType) && $this->_ref_service->getPerm($permType);
  }
  
  function store(){
    if ($this->_id && $this->_duplicate_to_service_id) {
      $this->completeField("name");
      
      $dup = new self;
      $dup->service_id = $this->_duplicate_to_service_id;
      $dup->name = $this->name;
      if ($msg = $dup->store()) {
        return $msg;
      }
      
      $items = $this->loadRefsEndowmentItems();
      
      foreach($items as $_item) {
        if ($_item->cancelled) {
          continue;
        }
        
        $_dup_item = new CProductEndowmentItem;
        $_dup_item->product_id = $_item->product_id;
        $_dup_item->quantity   = $_item->quantity;
        $_dup_item->endowment_id = $dup->_id;
        $_dup_item->store();
      }
      
      $this->_duplicate_to_service_id = null;
      return;
    }
    
    return parent::store();
  }
}

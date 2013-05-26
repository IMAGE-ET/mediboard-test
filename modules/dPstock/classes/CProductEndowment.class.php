<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage stock
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Product Endowment
 */
class CProductEndowment extends CMbObject {
  public $endowment_id;
  
  public $name;
  public $service_id;
  public $_duplicate_to_service_id;

  /** @var CService */
  public $_ref_service;

  /** @var CProductEndowmentItem[] */
  public $_ref_endowment_items;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_endowment';
    $spec->key   = 'endowment_id';
    $spec->uniques["unique"] = array("name", "service_id");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["name"]       = "str notNull";
    $props["service_id"] = "ref notNull class|CService autocomplete|nom";
    $props["_duplicate_to_service_id"] = $props["service_id"];
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["endowment_items"] = "CProductEndowmentItem endowment_id";
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "$this->name";
    
    if ($this->_ref_service) {
      $this->_view .= " ({$this->_ref_service->_view})";
    }
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd(){
    parent::loadRefsFwd();
    
    $this->loadRefService();
  }
  
  /**
   * Load service
   *
   * @return CService
   */
  function loadRefService(){
    return $this->_ref_service = $this->loadFwdRef("service_id", true);
  }

  /**
   * @see parent::loadRefsBack()
   */
  function loadRefsBack(){
    $this->loadRefsEndowmentItems();
  }

  /**
   * Load items
   *
   * @return CProductEndowmentItem[]
   */
  function loadRefsEndowmentItems() {
    $ljoin = array(
      "product"                => "product.product_id = product_endowment_item.product_id",
      "product_stock_group"    => "product_stock_group.product_id = product.product_id",
      "product_stock_location" => "product_stock_location.stock_location_id = product_stock_group.location_id"
    );

    $order = "product_stock_location.position, product.name";
    
    return $this->_ref_endowment_items = $this->loadBackRefs('endowment_items', $order, null, null, $ljoin);
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    $this->loadRefsFwd();
    
    return parent::getPerm($permType) && $this->_ref_service->getPerm($permType);
  }

  /**
   * @see parent::store()
   */
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
      
      foreach ($items as $_item) {
        if ($_item->cancelled) {
          continue;
        }
        
        $_dup_item = new CProductEndowmentItem();
        $_dup_item->product_id = $_item->product_id;
        $_dup_item->quantity   = $_item->quantity;
        $_dup_item->endowment_id = $dup->_id;
        $_dup_item->store();
      }
      
      $this->_duplicate_to_service_id = null;

      return null;
    }
    
    return parent::store();
  }
}

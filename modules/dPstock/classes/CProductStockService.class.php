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
 * Service Product Stock
 */
class CProductStockService extends CProductStock /* extends CMbMetaObject */ {
  // DB Fields
  public $object_class;
  public $object_id;
  public $common;

  /** @var CService|CBlocOperatoire */
  public $_ref_object;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_stock_service';
    $spec->key   = 'stock_id';
    $spec->uniques["product"] = array("object_id", "object_class", "product_id"/*, "location_id"*/);
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs['object_class'] = 'enum notNull list|CService'; //|CBlocOperatoire';
    $specs['object_id']    = 'ref notNull class|CMbObject meta|object_class';
    $specs['common']       = 'bool';
    return $specs;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["administrations"] = "CAdministration stock_id";
    return $backProps;
  }

  /**
   * Set object_class and object_id
   *
   * @param CMbObject $object Object
   *
   * @return void
   */
  function setObject(CMbObject $object) {
    $this->_ref_object  = $object;
    $this->object_id    = $object->_id;
    $this->object_class = $object->_class;
  }

  /**
   * Load target object
   *
   * @param bool $cache Use object cache
   *
   * @return CService|CBlocOperatoire
   */
  function loadTargetObject($cache = true) {
    return $this->_ref_object = $this->loadFwdRef("object_id", $cache);
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd(){
    parent::loadRefsFwd();
    $this->loadTargetObject();
  }
  
  /**
   * Get Stock from product code and service ID
   *
   * @param string $code       Product code
   * @param int    $service_id Service ID
   *
   * @return CProductStockService
   */
  static function getFromCode($code, $service_id = null) {
    $stock = new self();
    
    $where = array();
    $where['product.code'] = "= '$code'";
    $where['product_stock_service.object_class'] = "= 'CService'"; // XXX
    
    if ($service_id) {
      $where['product_stock_service.object_id'] = "= '$service_id'";
    }
    
    $ljoin = array();
    $ljoin['product'] = 'product_stock_service.product_id = product.product_id';

    $stock->loadObject($where, null, null, $ljoin);
    return $stock;
  }

  /**
   * Get a stock from a product and a host
   *
   * @param CProduct  $product Product
   * @param CMbObject $host    Host
   *
   * @return CProductStockService
   */
  static function getFromProduct(CProduct $product, CMbObject $host) {
    $stock = new self;
    $stock->setObject($host);
    $stock->product_id = $product->_id;
    $stock->loadMatchingObject();
    return $stock;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "$this->_ref_product ($this->_ref_object)";
  }

  /**
   * Load locations
   *
   * @return CProductStockLocation[]
   */
  function loadRelatedLocations(){
    $where = array(
      "object_class" => "= '$this->object_class'",
      "object_id"    => "= '$this->object_id'",
    );
    
    $location = new CProductStockLocation;
    return $this->_ref_related_locations = $location->loadList($where, "name");
  }

  /**
   * @see parent::check()
   */
  function check(){
    if ($msg = parent::check()) {
      return $msg;
    }
    
    if ($this->location_id) {
      $this->completeField("object_id", "object_class");
      $location = $this->loadRefLocation();
      
      if (
          $location->object_class !== $this->object_class ||
          $location->object_id    !=  $this->object_id
      ) {
        return "Le stock doit être associé à un emplacement de '".$this->loadTargetObject()."'";
      }
    }

    return null;
  }

  /**
   * @see parent::loadRefHost()
   */
  function loadRefHost(){
    return $this->loadTargetObject();
  }

  /**
   * @see parent::setHost()
   */
  function setHost(CMbObject $host){
    $this->setObject($host);
  }
}

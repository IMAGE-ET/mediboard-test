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
 * Product Stock Location
 */
class CProductStockLocation extends CMbMetaObject {
  // DB Table key
  public $stock_location_id;

  // DB Fields
  public $name;
  public $desc;
  public $position;
  public $group_id;

  /** @var CProductStockGroup[] */
  public $_ref_group_stocks;
  
  /** @var CGroups */
  public $_ref_group;
  
  public $_before;
  public $_type;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_stock_location';
    $spec->key   = 'stock_location_id';
    $spec->uniques["name"] = array("name", "object_class", "object_id");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs['name'] = 'str notNull seekable';
    $specs['desc'] = 'text seekable';
    $specs['position'] = 'num min|1';
    $specs['group_id'] = 'ref notNull class|CGroups';
    $specs['object_class'] = 'enum notNull list|CGroups|CService|CBlocOperatoire';
    $specs['_before']  = 'ref class|CProductStockLocation autocomplete|name|true';
    $specs['_type']  = 'str';
    return $specs;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["group_stocks"]    = "CProductStockGroup location_id";
    $backProps["service_stocks"]  = "CProductStockService location_id";
    $backProps["delivery_traces"] = "CProductDeliveryTrace target_location_id";
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadTargetObject(false);
    
    $this->_shortview = ($this->position ? "[".str_pad($this->position, 3, "0", STR_PAD_LEFT)."] " : "") . $this->name;
    $this->_view = ($this->_ref_object ? "{$this->_ref_object->_view} - " : "") . $this->_shortview;
  }

  /**
   * @see parent::updatePlainFields()
   */
  function updatePlainFields() {
    parent::updatePlainFields();
    
    if ($this->_type) {
      list($this->object_class, $this->object_id) = explode("-", $this->_type);
      $this->_type = null;
    }
    
    if ($this->_before && $this->_before != $this->_id) {
      $next_object = new self;
      $next_object->load($this->_before);
      
      if ($next_object->_id) {
        $query = '';
        $table = $this->_spec->table;
        
        if ($this->position) {
          $query = "AND `$table`.`position` BETWEEN $next_object->position AND $this->position";
        }
        else if ($next_object->position) {
          $query = "AND `$table`.`position` >= $next_object->position";
        }
        
        $where = array(
          "`$table`.`position` IS NOT NULL $query",
          "`$table`.`object_class` = '$this->object_class'",
          "`$table`.`object_id` = '$this->object_id'"
        );
        
        $this->position = $next_object->position;

        /** @var self[] $next_objects */
        $next_objects = $this->loadList($where);
        foreach ($next_objects as &$object) {
          $object->position++;
          $object->store();
        }

        if (count($next_objects) == 0) {
          $next_object->position = 2;
          $next_object->store();
          $this->position = 1;
        }
      }

      $this->_before = null;
    }
    else if (!$this->_id && !$this->position) {
      $existing = $this->loadList(null, "position");
      if ($location = end($existing)) {
        $this->position = $location->position + 1;
      }
      else { 
        $this->position = 1;
      }
    }
  }

  /**
   * Get stock class from host class
   *
   * @param string $host_class Host class
   *
   * @return string
   */
  static function getStockClass($host_class) {
    switch ($host_class) {
      case "CGroups": 
        return "CProductStockGroup";
      default: 
      case "CBlocOperatoire":
      case "CService":
        return "CProductStockService";
    }
  }

  /**
   * Get stock type
   *
   * @return null|string
   */
  function getStockType(){
    if (!$this->_id) {
      return null;
    }
    
    $this->completeField("object_class");
    return self::getStockClass($this->object_class);
  }

  /**
   * Load stocks by type
   *
   * @return void
   */
  function loadRefsStocks(){
    $ljoin = array(
      "product" => "product_stock_group.product_id = product.product_id",
    );
    $this->loadBackRefs("group_stocks", "product.name", null, null, $ljoin);
    
    if (!empty($this->_back["group_stocks"])) {
      foreach ($this->_back["group_stocks"] as $_id => $_stock) {
        if ($_stock->loadRefProduct()->cancelled) {
          unset($this->_back["group_stocks"][$_id]);
        }
      }
    }
    
    $ljoin = array(
      "product" => "product_stock_service.product_id = product.product_id",
    );
    $this->loadBackRefs("service_stocks", "product.name", null, null, $ljoin);
    
    if (!empty($this->_back["service_stocks"])) {
      foreach ($this->_back["service_stocks"] as $_id => $_stock) {
        if ($_stock->loadRefProduct()->cancelled) {
          unset($this->_back["service_stocks"][$_id]);
        }
      }
    }
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd(){
    $this->_ref_group = $this->loadFwdRef("group_id", true);
  }
  
  /**
   * Returns the existing location for the product in the host,
   * if it doesn't exist, will return the first location found in the host
   * 
   * @param CGroups|CService|CBlocOperatoire|CMbObject $host    Stock location's host object
   * @param CProduct                                   $product Product
   * 
   * @return CProductStockLocation The location
   */
  static function getDefaultLocation(CMbObject $host, CProduct $product = null) {
    $stock_class = self::getStockClass($host->_class);

    /** @var CProductStock $stock */
    $stock = new $stock_class;
    $stock->setHost($host);
    $stock->product_id = $product->_id;
    $stock->loadMatchingObject();
    
    if (!$stock->_id || !$stock->location_id) {
      $ds = $host->_spec->ds;
      $where = array(
        "object_class" => $ds->prepare("=%", $host->_class),
        "object_id"    => $ds->prepare("=%", $host->_id),
      );
      
      // pas loadMatchingObject a cause du "position" pré-rempli :(
      $location = new CProductStockLocation();
      if (!$location->loadObject($where, "position")) {
        $location->name = "Lieu par défaut";
        $location->group_id = ($host instanceof CGroups ? $host->_id : $host->group_id);
        $location->setObject($host);
        $location->store();
      }
      
      return $location;
    }
    else {
      return $stock->loadRefLocation();
    }
  }

  /**
   * Find a stock from a product ID
   *
   * @param int $product_id Product ID
   *
   * @return CProductStock
   */
  function loadRefStock($product_id) {
    $class = $this->getStockType();

    /** @var CProductStock $stock */
    $stock = new $class;
    $stock->product_id = $product_id;
    
    switch ($this->object_class) {
      case "CGroups": 
        $stock->group_id = $this->object_id;
        break;
      default: 
        $stock->object_id    = $this->object_id;
        $stock->object_class = $this->object_class;
        break;
    }
    
    $stock->loadMatchingObject();
    return $stock;
  }

  /**
   * Get a group's stock locations
   *
   * @param int $group_id Group ID
   *
   * @return CStoredObject[]
   */
  static function getGroupStockLocations($group_id) {
    $where = "
      (product_stock_location.object_id = '$group_id' AND product_stock_location.object_class = 'CGroups') OR 
      (service.group_id = '$group_id' AND product_stock_location.object_class = 'CService') OR 
      (bloc_operatoire.group_id = '$group_id' AND product_stock_location.object_class = 'CBlocOperatoire')";
      
    $ljoin = array(
      "service" => "service.service_id = product_stock_location.object_id",
      "bloc_operatoire" => "bloc_operatoire.bloc_operatoire_id = product_stock_location.object_id",
    );
      
    $sl = new self;
    return $sl->loadList($where, null, null, null, $ljoin);
  }
}

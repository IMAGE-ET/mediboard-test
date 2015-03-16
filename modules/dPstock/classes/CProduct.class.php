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
 * Product
 */
class CProduct extends CMbObject {
  public $product_id;

  // DB Fields
  public $name;
  public $description;
  
  // codage
  public $code;
  public $code_canonical;
  public $scc_code; // in the barcodes (http://www.morovia.com/education/symbology/scc-14.asp)
  
  public $category_id;
  public $societe_id;
  public $quantity;
  public $item_title;
  public $unit_quantity;
  public $unit_title;
  public $packaging;
  public $renewable;
  public $cancelled;
  public $equivalence_id;
  public $auto_dispensed;
  
  // classif
  public $classe_comptable;
  public $cladimed;

  /** @var CProductCategory */
  public $_ref_category;

  /** @var CSociete */
  public $_ref_societe;

  /** @var CProductStockGroup[] */
  public $_ref_stocks_group;

  /** @var CProductStockService[] */
  public $_ref_stocks_service;

  /** @var CProductReference[] */
  public $_ref_references;

  /** @var CProductOrderItemReception[] */
  public $_ref_lots;

  /** @var CProductSelection[] */
  public $_ref_selections;
  
  // Undividable quantity
  public $_unit_quantity;
  public $_unit_title;
  public $_quantity; // The quantity view
  public $_consumption;
  public $_supply;
  public $_unique_usage;
  
  public $_in_order;
  public $_classe_atc;
  public $_create_stock_quantity;
  
  /** @var CProductStockGroup This group's stock id */
  public $_ref_stock_group;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product';
    $spec->key   = 'product_id';
    $spec->uniques["code"] = array("code");
    
    //if ($this->conf("allow_same_name")) {
      $spec->uniques["name"] = array("name");
    //}
    
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['references']     = 'CProductReference product_id';
    $backProps['stocks_group']   = 'CProductStockGroup product_id';
    $backProps['stocks_service'] = 'CProductStockService product_id';
    $backProps['lines_dmi']      = 'CPrescriptionLineDMI product_id';
    $backProps['selections']     = 'CProductSelectionItem product_id';
    $backProps['endowments']     = 'CProductEndowmentItem product_id';
    $backProps['dmis']           = 'CDMI product_id';
    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props['name']          = 'str notNull seekable show|0';
    $props['description']   = 'text seekable';
    
    // codage
    $props['code']          = 'str maxLength|32 seekable protected';
    $props['code_canonical']= 'str maxLength|32 seekable show|0';
    $props['scc_code']      = 'numchar length|10 seekable|equal protected'; // Manufacturer Code + Item Number
    
    $props['category_id']   = 'ref notNull class|CProductCategory autocomplete|name';
    $props['societe_id']    = 'ref class|CSociete seekable autocomplete|name';
    $props['quantity']      = 'num notNull min|0 show|0';
    $props['item_title']    = 'str autocomplete show|0';
    $props['unit_quantity'] = 'float min|0 show|0';
    $props['unit_title']    = 'str autocomplete show|0';
    $props['packaging']     = 'str autocomplete';
    $props['renewable']     = 'enum list|0|1|2';
    $props['cancelled']     = 'bool default|0 show|0';
    $props['equivalence_id'] = 'ref class|CProductEquivalence';
    $props['auto_dispensed'] = 'bool default|0';
    
    // classif
    $props['cladimed'] = 'str maxLength|7 autocomplete';
    $props['classe_comptable'] = 'str maxLength|9 autocomplete';
    
    $props['_unit_title']   = 'str';
    $props['_unique_usage'] = 'bool';
    $props['_unit_quantity']= 'float min|0';
    $props['_quantity']     = 'str show|1';
    $props['_consumption']  = 'num show|1';
    $props['_create_stock_quantity'] = 'num min|0';
    
    $props['_classe_atc']  = 'str';
    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->name;
    
    if ($this->unit_quantity !== null && $this->unit_quantity == round($this->unit_quantity)) {
      // float to int (the comma is deleted)
      $this->unit_quantity = round($this->unit_quantity);
    }
    if ($this->unit_quantity === 0) {
      $this->unit_quantity = '';
    }
    
    $this->_quantity = '';
    if ($this->item_title && $this->quantity) {
      $this->_quantity .= "$this->quantity $this->item_title";
    }
    
    if ($this->item_title && $this->quantity) {
      $this->_unit_quantity = ($this->quantity ? $this->quantity : 1);
      $this->_unit_title = $this->item_title;
    }
    else {
      $this->_unit_quantity = ($this->unit_quantity ? $this->unit_quantity : 1);
      $this->_unit_title = $this->unit_title;
    }
    
    $this->_unique_usage = ($this->unit_quantity < 2 && !$this->renewable);
  }

  /**
   * Load references
   *
   * @param bool $cache Use object cache
   *
   * @return CProductReference[]
   */
  function loadRefsReferences($cache = false) {
    if ($cache && !empty($this->_ref_references)) {
      return $this->_ref_references;
    }
    return $this->_ref_references = $this->loadBackRefs('references');
  }

  /**
   * @see parent::loadRefsBack()
   */
  function loadRefsBack() {
    $this->loadRefsReferences();
    $this->_ref_stocks_group = $this->loadBackRefs('stocks_group');
    
    $ljoin = array(
      'service' => "service.service_id = product_stock_service.object_id AND product_stock_service.object_class = 'CService'"
    );
    
    $this->_ref_stocks_service = $this->loadBackRefs('stocks_service', "service.nom", null, null, $ljoin);
    $this->_ref_selections     = $this->loadBackRefs('selections');
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    $this->loadRefCategory();
    $this->loadRefSociete();
  }

  /**
   * Load category
   *
   * @return CProductCategory
   */
  function loadRefCategory() {
    return $this->_ref_category = $this->loadFwdRef("category_id", true);
  }

  /**
   * Load manufacturer
   *
   * @return CSociete
   */
  function loadRefSociete() {
    return $this->_ref_societe = $this->loadFwdRef("societe_id" , true);
  }
  
  /**
   * Loads the stock associated to the current group
   *
   * @param bool $cache Use object cache
   *
   * @return CProductStockGroup
   */
  function loadRefStock($cache = true) {
    if ($this->_ref_stock_group && $cache) {
      return $this->_ref_stock_group;
    }
    
    // Coneserver le loadMatchingObject car group_id et product_id sont 
    // utilisés (au moins dans CProduitLivretTherapeutique::addToStocks)
    $this->completeField("product_id");
    $this->_ref_stock_group = new CProductStockGroup();
    $this->_ref_stock_group->group_id = CProductStockGroup::getHostGroup();
    $this->_ref_stock_group->product_id = $this->product_id;
    
    $this->_ref_stock_group->loadMatchingObject();
    return $this->_ref_stock_group;
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    if (!$this->_ref_category) {
      $this->loadRefsFwd();
    }
    return $this->_ref_category->getPerm($permType);
  }

  /**
   * Load lots
   *
   * @return CProductOrderItemReception[]
   */
  function loadRefsLots(){
    $ljoin = array(
      "product_order_item" => "product_order_item_reception.order_item_id = product_order_item.order_item_id",
      "product_reference"  => "product_order_item.reference_id = product_reference.reference_id",
      "product"            => "product_reference.product_id = product.product_id",
    );
    
    $where = array(
      "product.product_id" => " = '$this->_id'",
    );
    
    $lot = new CProductOrderItemReception;
    return $this->_ref_lots = $lot->loadList($where, "date DESC", null, null, $ljoin);
  }

  /**
   * @see parent::loadView()
   */
  function loadView(){
    parent::loadView();
    $this->getConsumption("-3 MONTHS");
  }

  /**
   * Computes this product's consumption between two dates
   *
   * @param string $since        [optional] Min date
   * @param string $date_max     [optional] Max date
   * @param int    $service_id   Service ID
   * @param bool   $include_loss Include "lost" products
   *
   * @return float
   */
  function getConsumption($since = "-1 MONTH", $date_max = null, $service_id = null, $include_loss = true){
    $this->loadRefStock(true);
    
    $where = array(
      "product_delivery.stock_class" => "= 'CProductStockGroup'",
      "product_delivery.stock_id" => "= '{$this->_ref_stock_group->_id}'",
      "product_delivery_trace.date_delivery > '".CMbDT::date($since)."'",
    );
    
    if ($date_max) {
      $where[] = "product_delivery_trace.date_delivery <= '".CMbDT::date($date_max)."'";
    }
    
    if ($service_id) {
      $where["product_delivery.service_id"] = "= '$service_id'";
    }
    else if ($include_loss) {
      $where["product_delivery.service_id"] = "IS NOT NULL";
    }
    
    $ljoin = array(
      "product_delivery" => "product_delivery.delivery_id = product_delivery_trace.delivery_id"
    );
    
    $sql = new CRequest();
    $sql->addTable("product_delivery_trace");
    $sql->addSelect("SUM(product_delivery_trace.quantity)");
    $sql->addLJoin($ljoin);
    $sql->addWhere($where);
    $total = $this->_spec->ds->loadResult($sql->makeSelect());
    
    return $this->_consumption = $total;
  }

  /**
   * Computes this product's consumption between two dates
   *
   * @param CProduct[] $products     Products list
   * @param string     $since        [optional] Start offset
   * @param string     $date_max     [optional] Max date
   *
   * @param CService[] $services     Services
   * @param bool       $include_loss Include lost items
   *
   * @return float[]
   */
  static function getConsumptionMultipleProducts(
      $products,
      $since = "-1 MONTH",
      $date_max = null,
      $services = null,
      $include_loss = true
  ) {
    $ds = CSQLDataSource::get("std");
    
    $where = array(
      "product_stock_group.product_id" => $ds->prepareIn(CMbArray::pluck($products, "_id")),
      "product_stock_group.group_id" => "= '".CProductStockGroup::getHostGroup()."'",
      "product_delivery.stock_class" => "= 'CProductStockGroup'",
      "product_delivery_trace.date_delivery > '".CMbDT::date($since)."'",
    );
    
    if ($date_max) {
      $where[] = "product_delivery_trace.date_delivery <= '".CMbDT::date($date_max)."'";
    }
    
    if (!empty($services)) {
      $where["product_delivery.service_id"] = $ds->prepareIn(CMbArray::pluck($services, "_id"));
    }
    else if ($include_loss) {
      $where["product_delivery.service_id"] = "IS NOT NULL";
    }
    
    $ljoin = array(
      "product_delivery" => "product_delivery.delivery_id = product_delivery_trace.delivery_id",
      "product_stock_group" => "product_delivery.stock_id = product_stock_group.stock_id",
    );
    
    $sql = new CRequest();
    $sql->addTable("product_delivery_trace");
    $sql->addSelect(array("product_stock_group.product_id", "SUM(product_delivery_trace.quantity) AS sum"));
    $sql->addLJoin($ljoin);
    $sql->addGroup("product_stock_group.product_id");
    $sql->addWhere($where);
    
    if (empty($services)) {
      $total = $ds->loadHashList($sql->makeSelect());
    }
    else {
      $sql->addGroup("product_delivery.service_id");
      $sql->addSelect(array("product_delivery.service_id"));
      $total = $ds->loadList($sql->makeSelect());
    }
    
    return $total;
  }
  
  /** 
   * Computes this product's supply between two dates
   * 
   * @param string $since    [optional]
   * @param string $date_max [optional]
   * 
   * @return float
   */
  function getSupply($since = "-1 MONTH", $date_max = null){
    $where = array(
      "product.product_id" => "= '{$this->_id}'",
      "product_order_item_reception.date > '".CMbDT::date($since)."'",
    );
    
    if ($date_max) {
      $where[] = "product_order_item_reception.date <= '".CMbDT::date($date_max)."'";
    }
    
    $ljoin = array(
      "product_order_item" => "product_order_item.order_item_id = product_order_item_reception.order_item_id",
      "product_reference" => "product_reference.reference_id = product_order_item.reference_id",
      "product" => "product.product_id = product_reference.product_id",
    );
    
    $sql = new CRequest();
    $sql->addTable("product_order_item_reception");
    $sql->addSelect("SUM(product_order_item_reception.quantity)");
    $sql->addLJoin($ljoin);
    $sql->addWhere($where);
    
    return $this->_supply = $this->_spec->ds->loadResult($sql->makeSelect());
  }

  /**
   * Get supply stats
   *
   * @param CProduct[] $products List of products
   * @param string     $since    Date start offset
   * @param string     $date_max Max date
   *
   * @return array
   */
  static function getSupplyMultiple($products, $since = "-1 MONTH", $date_max = null){
    $ds = CSQLDataSource::get("std");
    
    $where = array(
      "product.product_id" => $ds->prepareIn(CMbArray::pluck($products, "_id")),
      "product_order_item_reception.date > '".CMbDT::date($since)."'",
    );
    
    if ($date_max) {
      $where[] = "product_order_item_reception.date <= '".CMbDT::date($date_max)."'";
    }
    
    $ljoin = array(
      "product_order_item" => "product_order_item.order_item_id = product_order_item_reception.order_item_id",
      "product_reference" => "product_reference.reference_id = product_order_item.reference_id",
      "product" => "product.product_id = product_reference.product_id",
    );
    
    $sql = new CRequest();
    $sql->addTable("product_order_item_reception");
    $sql->addSelect("product.product_id, SUM(product_order_item_reception.quantity) AS sum");
    $sql->addLJoin($ljoin);
    $sql->addGroup("product.product_id");
    $sql->addWhere($where);
    
    return $ds->loadHashList($sql->makeSelect());
  }

  /**
   * Computes the weighted average price (PMP)
   *
   * @param string $since    [optional]
   * @param string $date_max [optional]
   * @param bool   $ttc      Include taxes
   *
   * @return float
   */
  function getWAP($since = "-1 MONTH", $date_max = null, $ttc = false){
    $qty = $this->getSupply($since, $date_max);
    
    if (!$qty) {
      return null;
    }
    
    $where = array(
      "product.product_id" => "= '{$this->_id}'",
      "product_order_item_reception.date > '".CMbDT::date($since)."'",
    );
    
    if ($date_max) {
      $where[] = "product_order_item_reception.date <= '".CMbDT::date($date_max)."'";
    }
    
    $ljoin = array(
      "product_order_item" => "product_order_item.order_item_id = product_order_item_reception.order_item_id",
      "product_reference" => "product_reference.reference_id = product_order_item.reference_id",
      "product" => "product.product_id = product_reference.product_id",
    );
    
    $sql = new CRequest();
    $sql->addTable("product_order_item_reception");

    $select = "SUM(product_order_item_reception.quantity * product_order_item.unit_price)";
    if ($ttc) {
      $ttc_select = "product_order_item.unit_price + (product_order_item.unit_price * (product_order_item.tva / 100))";
      $select = "SUM(product_order_item_reception.quantity * ($ttc_select))";
    }
    $sql->addSelect($select);
    $sql->addLJoin($ljoin);
    $sql->addWhere($where);
    
    $total = $this->_spec->ds->loadResult($sql->makeSelect());
    
    return $total / $qty;
  }

  /**
   * @see parent::store()
   */
  function store() {
    $this->completeField("code", 'quantity', 'unit_quantity');
    
    if (!$this->quantity) {
      $this->quantity = 1;
    }
    
    if ($this->unit_quantity == 0) {
      $this->unit_quantity = '';
    }
    
    if ($this->code !== null && (!$this->_id || $this->fieldModified("code"))) {
      $this->code_canonical = preg_replace("/[^0-9a-z]/i", "", $this->code);
    }
    
    $cc = trim($this->classe_comptable, "0\n\r\t ");
    if (preg_match('/^\d+$/', $cc)) {
      $this->classe_comptable = str_pad($cc, 9, "0", STR_PAD_RIGHT);
    }
    else {
      $this->classe_comptable = "";
    }
    
    if ($this->fieldModified("cancelled", 1)) {
      $references = $this->loadRefsReferences();
      foreach ($references as $_ref) {
        $_ref->cancelled = 1;
        $_ref->store();
      }
    }
    
    $create_stock_quantity = $this->_create_stock_quantity;
    
    if ($msg = parent::store()) {
      return $msg;
    }
    
    if ($create_stock_quantity) {
      $stock = $this->loadRefStock();
      $stock->quantity = $create_stock_quantity;
      $stock->order_threshold_min = $stock->quantity;
      
      $group = CGroups::loadCurrent();
      $stock->location_id = CProductStockLocation::getDefaultLocation($group, $this)->_id;
      
      if ($msg = $stock->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
      
      $this->_create_stock_quantity = null;
    }

    return null;
  }

  /**
   * Get or count items in pending orders
   *
   * @param bool $count Count instead of load
   *
   * @return CProductOrderItem[]|int[]
   */
  function getPendingOrderItems($count = true){
    $leftjoin = array();
    $leftjoin['product_order']      = 'product_order.order_id = product_order_item.order_id';
    $leftjoin['product_reference']  = 'product_reference.reference_id = product_order_item.reference_id';
    $leftjoin['product']            = 'product.product_id = product_reference.product_id';

    $where = array(
      "product.product_id"         => "= '$this->_id'",
      "product_order.cancelled"    => '= 0', // order not cancelled
      "product_order.deleted"      => '= 0', // order not deleted
      "product_order.date_ordered" => 'IS NOT NULL', // ordered
      "product_order.received"     => "= '0'", // ordered
      "product_order_item.renewal" => "= '1'", // renewal line
    );

    /** @var CProductOrderItem[] $list */

    $item = new CProductOrderItem();
    if ($count) {
      $list = $item->countList($where, null, $leftjoin);
    }
    else {
      $list = $item->loadList($where, "date_ordered ASC", null, "product_order_item.order_item_id", $leftjoin);
    }
    
    foreach ($list as $_id => $_item) {
      if ($_item->isReceived()) {
        unset($list[$_id]);
      }
    }
    
    $this->_in_order = $list;
    
    if ($list) {
      foreach ($this->_in_order as $_item) {
        $_item->loadOrder();
      }
    }
    
    return $this->_in_order;
  }

  /**
   * Fill a flow struct
   *
   * @param array      &$array   The flow struct to fill
   * @param CProduct[] $products Products
   * @param int        $n        N*$unit
   * @param string     $start    Start date
   * @param string     $unit     Time unit
   * @param CService[] $services Services
   *
   * @return void
   */
  private static function fillFlow(&$array, $products, $n, $start, $unit, $services) {
    foreach ($services as $_key => $_service) {
      $array["out"]["total"][$_key] = array(0, 0);
    }
    
    $d = &$array["out"];
    
    // Y init
    for ($i = 0; $i < 12; $i++) {
      $from = CMbDT::date("+$i $unit", $start);
      $d[$from] = array();
    }
    $d["total"] = array(
      "total" => array(0, 0)
    );
    
    for ($i = 0; $i < $n; $i++) {
      $from = CMbDT::date("+$i $unit", $start);
      $to = CMbDT::date("+1 $unit", $from);
      
      // X init
      foreach ($services as $_key => $_service) {
        $d[$from][$_key] = array(0, 0);
        if (!isset($d["total"][$_key])) {
          $d["total"][$_key] = array(0, 0);
        }
      }
      $d[$from]["total"] = array(0, 0);
      
      $all_counts = self::getConsumptionMultipleProducts($products, $from, $to, $services, false);
      
      $by_product = array();
      foreach ($all_counts as $_data) {
        $by_product[$_data["product_id"]][$_data["service_id"]] = $_data["sum"];
      }

      /** @var CProduct $_product */

      foreach ($products as $_product) {
        $counts = CValue::read($by_product, $_product->_id, array());

        $coeff = 1;
        $ref = reset($_product->loadRefsReferences(true));
        if ($ref) {
          $coeff = $ref->price;
        }
        
        foreach ($services as $_key => $_service) {
          $_count = CValue::read($counts, $_key, 0);
          $_price = $_count * $coeff;
          
          $d[$from][$_key][0] += $_count;
          $d[$from][$_key][1] += $_price;
          
          $d[$from]["total"][0] += $_count;
          $d[$from]["total"][1] += $_price;
          
          @$d["total"][$_key][0] += $_count;
          @$d["total"][$_key][1] += $_price;
          
          @$d["total"]["total"][0] += $_count;
          @$d["total"]["total"][1] += $_price;
        }
      }
    }
    
    $d = array_map_recursive(array("CProduct", "round2"), $d);
  
    // Put the total at the end
    $total = $d["total"];
    unset($d["total"]);
    $d["total"] = $total;
    
    $total = $d["total"]["total"];
    unset($d["total"]["total"]);
    $d["total"]["total"] = $total;
    
    $d = CMbArray::transpose($d);
  }

  /**
   * Round to 2 digits
   *
   * @param float $val Value to round
   *
   * @return float
   */
  static function round2($val) {
    return round($val, 2);
  }

  /**
   * Build stock flow graph
   *
   * @param array      $flow     A flow struct
   * @param string     $title    Graph title
   * @param CService[] $services Services
   *
   * @return array
   */
  static function getFlowGraph($flow, $title, $services) {
    $options = CFlotrGraph::merge(
      "lines", 
      array(
        "title" => $title,
        "legend" => array(
          "show" => true
        ),
        "xaxis" => array(
          "ticks" => array(),
        ),
        "yaxis" => array(
          "min" => 0,
          "title" => utf8_encode("Valeur (euro)") // FIXME le symbole ne euro passe pas
        ),
        "markers" => array(
          "show" => false
        )
      )
    );
    
    $graph = array(
      "data" => array(),
      "options" => $options,
    );
    
    foreach ($flow["out"] as $_service_id => $_data) {
      if ($_service_id == "total") {
        continue;
      }
      
      $data = array(
        "data" => array(),
        "label" => utf8_encode($services[$_service_id]->_view),
      );
      
      if (empty($graph["options"]["xaxis"]["ticks"])) {
        foreach ($_data as $_date => $_values) {
          if ($_date == "total") {
            continue;
          }
          $graph["options"]["xaxis"]["ticks"][] = array(count($graph["options"]["xaxis"]["ticks"]), $_date);
        }
      }
      
      foreach ($_data as $_date => $_values) {
        if ($_date == "total") {
          continue;
        }
        $data["data"][] = array(count($data["data"]), $_values[1]);
      }
      $graph["data"][] = $data;
    }
    
    return $graph;
  }

  /**
   * Compute stock balance
   *
   * @param CProduct[] $products Products
   * @param CService[] $services Services
   * @param int        $year     Year
   * @param int        $month    Month
   *
   * @return array
   */
  static function computeBalance(array $products, array $services, $year, $month = null){
    $flows = array();
    
    // YEAR //////////
    $year_flows = array(
      "in"  => array(),
      "out" => array(),
    );
    $start = CMbDT::date(null, "$year-01-01");
    self::fillFlow($year_flows, $products, 12, $start, "MONTH", $services);
    
    $flows["year"] = array(
      $year_flows, 
      "%b", 
      "Bilan annuel",
      "graph" => self::getFlowGraph($year_flows, "Bilan annuel", $services),
    ); 
    
    // MONTH //////////
    if ($month) {
      $month_flows = array(
        "in"  => array(),
        "out" => array(),
      );
      $start = CMbDT::date(null, "$year-$month-01");
      self::fillFlow($month_flows, $products, CMbDT::transform("+1 MONTH -1 DAY", $start, "%d"), $start, "DAY", $services);
      
      $flows["month"] = array(
        $month_flows, 
        "%d", 
        "Bilan mensuel",
        "graph" => self::getFlowGraph($month_flows, "Bilan mensuel", $services),
      );
    }
    
    // Balance des stocks ////////////////
    $balance = array(
      "in" => $flows["year"][0]["in"],
      "out" => array(),
      "diff" => array(),
    );
    
    $start = CMbDT::date(null, "$year-01-01");
    for ($i = 0; $i < 12; $i++) {
      $from = CMbDT::date("+$i MONTH", $start);
      $to = CMbDT::date("+1 MONTH", $from);
      
      $balance["in"][$from] = array(0, 0);
      $balance["out"][$from] = array(0, 0);

      $supply_multiple = self::getSupplyMultiple($products, $from, $to);
      $consum_multiple = self::getConsumptionMultipleProducts($products, $from, $to, null, false);

      /** @var CProduct $_product */
      foreach ($products as $_product) {
        $supply = CValue::read($supply_multiple, $_product->_id, 0);
        //$supply = $_product->getSupply($from, $to);
        
        $consum = CValue::read($consum_multiple, $_product->_id, 0);
        //$consum = $_product->getConsumption($from, $to, null, false);
        
        $coeff = 1;
        $ref = reset($_product->loadRefsReferences(true));
        if ($ref) {
          $coeff = $ref->price;
        }
        
        $balance["in"][$from][0]  += $supply;
        $balance["in"][$from][1]  += $supply * $coeff;
        
        $balance["out"][$from][0] += $consum;
        $balance["out"][$from][1] += $consum * $coeff;
      }
    }
    
    $cumul = 0;
    $cumul_price = 0;
    foreach ($balance["in"] as $_date => $_balance) {
      $diff =       $balance["in"][$_date][0] - $balance["out"][$_date][0];
      $diff_price = $balance["in"][$_date][1] - $balance["out"][$_date][1];
      
      $balance["diff"][$_date][0] = $diff+$cumul;
      $balance["diff"][$_date][1] = $diff_price+$cumul_price;
      
      $cumul += $diff;
      $cumul_price += $diff_price;
    }
    
    $balance = array_map_recursive(array("CProduct", "round2"), $balance);
    
    $options = CFlotrGraph::merge(
      "bars", 
      array(
        "title" => "Rotation des stocks",
        "legend" => array(
          "show" => true
        ),
        "xaxis" => array(
          "ticks" => array(),
        ),
        "yaxis" => array(
          "min" => null,
          "title" => utf8_encode("Valeur (euro)") // FIXME le symbole euro ne passe pas
        ),
        "y2axis" => array(
          "min" => null,
        )
      )
    );
    
    $graph = array(
      "data" => array(),
      "options" => $options,
    );
    
    $params = array(
      "in" =>   array("label" => "Entrée", "color" => "#4DA74D"),
      "out" =>  array("label" => "Sortie", "color" => "#CB4B4B"),
      "diff" => array("label" => "Cumul",  "color" => "#00A8F0"),
    );
    
    foreach ($balance as $_type => $_data) {
      $data = array(
        "data" => array(),
        "label" => utf8_encode($params[$_type]["label"]),
        "color" => $params[$_type]["color"],
      );
      
      if ($_type == "diff") {
        $data["lines"]["show"] = true;
        $data["bars"]["show"] = false;
        $data["points"]["show"] = true;
        $data["mouse"]["track"] = true;
        //$data["yaxis"] = 2;
      }
      
      if (empty($graph["options"]["xaxis"]["ticks"])) {
        foreach ($_data as $_date => $_values) {
          if ($_date == "total") {
            continue;
          }
          $graph["options"]["xaxis"]["ticks"][] = array(count($graph["options"]["xaxis"]["ticks"]), $_date);
        }
      }
      
      foreach ($_data as $_date => $_values) {
        if ($_date == "total") {
          continue;
        }
        $v = ($_type === "out" ? -$_values[1] : $_values[1]);
        $data["data"][] = array(count($data["data"]), $v);
      }
      $graph["data"][] = $data;
    }
    
    $balance["graph"] = $graph;
  
    return array(
      $flows, $balance, // required to use list()
      "flows" => $flows,
      "balance" => $balance,
    );
  }

  /**
   * Charge l'atc dans la pharmacie
   *
   * @return void
   */
  function loadMedicamentATC() {
    $med = CMedicamentProduit::get($this->code);
    $this->_classe_atc = $med->_ref_ATC_5_code;
  }

}

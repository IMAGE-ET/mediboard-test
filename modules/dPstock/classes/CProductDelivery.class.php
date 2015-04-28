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
 * Product Delivery
 */
class CProductDelivery extends CMbObject {
  public $delivery_id;

  // Source
  public $stock_id;
  public $stock_class;

  // Target
  public $service_id;
  public $patient_id;
  public $sejour_id;

  public $date_dispensation;
  public $date_delivery;
  public $datetime_min;
  public $datetime_max;

  public $quantity;
  public $endowment_quantity;
  public $endowment_item_id;
  public $order;
  public $manual;
  public $comments;
  public $comments_deliver;
  public $type;
  public $preparateur_id;

  /** @var CProductStockGroup */
  public $_ref_stock;

  /** @var CProductStockService */
  public $_ref_stock_service;

  /** @var CProductEndowmentItem */
  public $_ref_endowment_item;

  /** @var CService */
  public $_ref_service;

  /** @var CPatient */
  public $_ref_patient;

  /** @var CSejour */
  public $_ref_sejour;

  /** @var CProductStockLocation */
  public $_ref_location_source;

  /** @var CProductStockLocation */
  public $_ref_location_target;

  /** @var CProductDeliveryTrace[] */
  public $_ref_delivery_traces;
  public $_ref_prises_dispensation;
  public $_ref_prises_dispensation_med;

  /** @var CMediusers */
  public $_ref_preparateur;

  /** @var CMediusers */
  public $_ref_validateur;

  public $_date_min;
  public $_date_max;
  public $_datetime_min;
  public $_datetime_max;
  public $_delivered;
  public $_auto_deliver;
  public $_make_delivery_trace;
  public $_initial_quantity;
  public $_quantity_recond;
  public $_date_order;

  /** @var CProduct[] */
  public $_products;
  public $_prises;
  public $_pilulier;
  public $_code_cis;
  public $_code_ucd;
  public $_code_cip;
  public $_count_delivered;
  public $_validateur_id;

  public $_auto_trace;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_delivery';
    $spec->key   = 'delivery_id';
    //$spec->xor["service"] = array("service_id", "comments");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    // Source
    // can be null when the stock doesn't exist in the group
    $props['stock_id']          = 'ref class|CProductStock meta|stock_class';
    $props['stock_class']       = 'str notNull class show|0';

    // Target
    $props['service_id']        = 'ref class|CService'; // >/dev/null
    $props['patient_id']        = 'ref class|CPatient';
    $props['sejour_id']         = 'ref class|CSejour';

    $props['date_dispensation'] = 'dateTime notNull';
    $props['date_delivery']     = 'dateTime';

    $props['datetime_min']      = 'dateTime notNull';
    $props['datetime_max']      = 'dateTime notNull moreEquals|datetime_min';

    $type = (CProductStock::$allow_quantity_fractions ? "float" : "num");
    $props['quantity']          = "$type notNull";
    $props['endowment_quantity']= "$type";
    $props['_initial_quantity'] = "$type";
    $props['_quantity_recond']  = "float";

    $props['endowment_item_id'] = "ref class|CProductEndowmentItem";
    $props['order']             = 'bool default|0';
    $props['manual']            = 'bool default|0';
    $props['comments']          = 'text';
    $props['comments_deliver']  = 'text';
    $props['type']              = 'enum list|other|expired|breakage|loss|gift|discrepancy|notused|toomany';
    $props['preparateur_id']    = 'ref class|CMediusers';

    $props['_date_min']         = 'date notNull';
    $props['_date_max']         = 'date notNull moreEquals|_date_min';
    $props['_date_order']       = 'dateTime';

    $props['_datetime_min']     = 'dateTime notNull';
    $props['_datetime_max']     = 'dateTime notNull moreEquals|_datetime_min';

    $props['_validateur_id']    = 'ref class|CMediusers show|1';
    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    $this->loadRefStock();
    $this->_view = $this->quantity.'x '.$this->_ref_stock->_view;

    if ($this->service_id) {
      $this->loadRefService();
      $this->_view .= ($this->service_id?" pour le service '{$this->_ref_service->_view}'":'');
    }
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['delivery_traces'] = 'CProductDeliveryTrace delivery_id';
    $backProps['echanges_phast']  = 'CExchangePhast object_id';
    $backProps['prises_dispensation'] = "CPriseDispensation delivery_id";
    return $backProps;
  }

  /**
   * Count delivered items
   *
   * @return int
   */
  function countDelivered() {
    $this->loadRefsDeliveryTraces();
    $sum = 0;
    foreach ($this->_ref_delivery_traces as $trace) {
      if ($trace->date_delivery) {
        $sum += $trace->quantity;
      }
    }

    return $this->_count_delivered = $sum;
  }

  /**
   * Is it delivered ?
   *
   * @return bool
   */
  function isDelivered() {
    return $this->_delivered = ($this->countDelivered() >= $this->quantity);
  }

  /**
   * Is it received ?
   *
   * @return bool
   */
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

  /**
   * @see parent::loadRefsBack()
   */
  function loadRefsBack(){
    $this->loadRefsDeliveryTraces();
  }

  /**
   * Load traces
   *
   * @return CProductDeliveryTrace[]
   */
  function loadRefsDeliveryTraces(){
    return $this->_ref_delivery_traces = $this->loadBackRefs("delivery_traces");
  }

  /**
   * Load prises dispensation
   *
   * @deprecated
   *
   * @return CPriseDispensation[]
   */
  function loadRefsPrisesDispensation(){
    return $this->_ref_prises_dispensation = $this->loadBackRefs("prises_dispensation", "datetime ASC");
  }

  /**
   * Load preparateur
   *
   * @return CMediusers
   */
  function loadRefPreparateur() {
    $this->completeField("preparateur_id");

    if ($this->_id && !$this->preparateur_id) {
      $this->preparateur_id = $this->loadFirstLog()->loadRefUser()->_id;
      $this->rawStore();
    }

    /** @var CMediusers $preparateur */
    $preparateur = $this->loadFwdRef("preparateur_id");
    $preparateur->loadRefFunction();

    return $this->_ref_preparateur = $preparateur;
  }

  /**
   * Load validateur
   *
   * @return CMediusers|null
   */
  function loadRefValidateur() {
    $order_logs = $this->loadLogsForField("order");

    /** @var CUserLog $last_log */
    $last_log = end($order_logs);

    if (!$last_log || !$last_log->_id) {
      return null;
    }

    $this->_validateur_id = $last_log->user_id;

    /** @var CMediusers $validateur */
    $validateur = $this->loadFwdRef("_validateur_id");
    $validateur->loadRefFunction();

    return $this->_ref_validateur = $validateur;
  }

  /**
   * Load prises dispensation
   *
   * @param string $date_min Min date
   * @param string $date_max Max date
   *
   * @return CPriseDispensation[]
   */
  function loadRefsPrisesDispensationMed($date_min, $date_max){
    $where = array();
    $where["delivery_id"] = " = '$this->_id'";
    $where["object_class"] = " = 'CPrescriptionLineMedicament'";
    $where["datetime"] = " BETWEEN '$date_min' AND '$date_max'";

    $prise_dispensation = new CPriseDispensation();
    return $this->_ref_prises_dispensation_med = $prise_dispensation->loadList($where, "datetime ASC");
  }

  /**
   * Load target stock
   *
   * @return CProductStockService
   */
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

  /**
   * Load target service
   *
   * @return CService
   */
  function loadRefService(){
    return $this->_ref_service = $this->loadFwdRef("service_id", true);
  }

  /**
   * Load target patient
   *
   * @return CPatient
   */
  function loadRefPatient(){
    return $this->_ref_patient = $this->loadFwdRef("patient_id", true);
  }

  /**
   * Load target séjour
   *
   * @return CSejour
   */
  function loadRefSejour(){
    return $this->_ref_sejour = $this->loadFwdRef("sejour_id", true);
  }

  /**
   * Load endowment item on which it is based
   *
   * @return CProductEndowmentItem
   */
  function loadRefEndowmentItem(){
    return $this->_ref_endowment_item = $this->loadFwdRef("endowment_item_id", true);
  }

  /**
   * Load source stock
   *
   * @return CProductStock
   */
  function loadRefStock(){
    return $this->_ref_stock = $this->loadFwdRef("stock_id", true);
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    $this->loadRefStock();
    $this->loadRefService();
    $this->loadRefPatient();
  }

  /**
   * @see parent::updatePlainFields()
   */
  function updatePlainFields(){
    parent::updatePlainFields();

    $this->completeField("stock_class");
    if (!$this->stock_class) {
      $this->stock_class = "CProductStockGroup";
    }
  }

  /**
   * @see parent::loadView()
   */
  function loadView(){
    parent::loadView();

    $this->loadRefPreparateur();
    $this->loadRefValidateur();
  }

  /**
   * Get the original quantity
   *
   * @return float
   */
  function getInitialQuantity(){
    $logs = $this->loadLogsForField("quantity", true, null, true);
    $first = end($logs);

    if ($first && $first->_id) {
      $old = $first->getOldValues();
      $quantity = $old["quantity"];
    }
    else {
      $this->completeField("quantity");
      $quantity = $this->quantity;
    }

    return $this->_initial_quantity = $quantity;
  }

  /**
   * @see parent::store()
   */
  function store(){
    $is_new = !$this->_id;

    if ($is_new) {
      if (!$this->datetime_min) {
        $this->datetime_min = $this->date_dispensation;
      }

      if (!$this->datetime_max) {
        $this->datetime_max = $this->date_dispensation;
      }

      if (!$this->preparateur_id) {
        $this->preparateur_id = CMediusers::get()->_id;
      }
    }

    $order_to_0 = $this->fieldModified("order", "0");

    if ($msg = parent::store()) {
      return $msg;
    }

    if ($this->_prises) {
      $prises = json_decode(stripslashes($this->_prises), true);

      foreach ($prises as $_prise) {
        $prise_dispensation = new CPriseDispensation();
        $prise_dispensation->delivery_id   = $this->_id;
        $prise_dispensation->datetime      = $_prise["datetime"];
        $prise_dispensation->quantite_adm  = $_prise["quantite_adm"];
        $prise_dispensation->unite_adm     = utf8_decode($_prise["unite_adm"]);
        $prise_dispensation->quantite_disp = $_prise["quantite_disp"];
        $prise_dispensation->object_id     = $_prise["object_id"];
        $prise_dispensation->object_class  = $_prise["object_class"];
        $prise_dispensation->store();
      }

      $this->_prises = null;
    }

    // Sortie manuelle ou autotrace et passage de "commande" à "pas commande"
    if (
        $is_new && $this->manual ||
        ($is_new || $order_to_0) && $this->_auto_trace
    ) {
      $delivery_trace = new CProductDeliveryTrace;
      $delivery_trace->delivery_id = $this->_id;
      $delivery_trace->quantity = $this->quantity;
      $delivery_trace->date_delivery = $this->date_delivery ? $this->date_delivery : CMbDT::dateTime();
      $delivery_trace->_code_cis = $this->_code_cis;
      $delivery_trace->_code_cip = $this->_code_cip;

      $delivery_trace->_datetime_min = $this->datetime_min;

      if ($this->manual) {
        $delivery_trace->date_reception = $delivery_trace->date_delivery;
      }

      $product = $this->loadRefStock()->loadRefProduct();
      $location = CProductStockLocation::getDefaultLocation($this->loadRefService(), $product);

      $delivery_trace->target_location_id = $location->_id;

      if ($msg = $delivery_trace->store()) {
        CAppUI::setMsg(
          "La commande a été validée, mais n'a pas pu etre délivrée automatiquement pour la raison suivante: <br />$msg",
          UI_MSG_WARNING
        );
      }
      else {
        CAppUI::setMsg("CProductDeliveryTrace-msg-create");
      }
    }

    if (!$is_new) {
      return null;
    }

    $this->loadRefStock()->loadRefsFwd();
    if ($this->_auto_deliver || $this->_ref_stock->_ref_product->auto_dispensed || $this->_auto_trace) {
      $this->date_dispensation = CMbDT::dateTime();
      $this->order = 0;
      return parent::store();
    }

    return null;
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    if (!$this->_ref_stock || !$this->_ref_service) {
      $this->loadRefsFwd();
    }

    if ($this->_ref_service) {
      return $this->_ref_stock->getPerm($permType) && $this->_ref_service->getPerm($permType);
    }

    return $this->_ref_stock->getPerm($permType);
  }

  /**
   * @see parent::delete()
   */
  function delete(){
    $this->completeField("manual");

    //if ($this->manual) {
    /** @var CProductDeliveryTrace[] $traces */
    $traces = $this->loadBackRefs("delivery_traces");
    foreach ($traces as $_trace) {
      $_trace->_code_cis = $this->_code_cis;
      $_trace->_code_cip = $this->_code_cip;
      $_trace->delete();
    }
    //}

    return parent::delete();
  }
}

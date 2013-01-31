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

class CProductDelivery extends CMbObject {
  // DB Table key
  var $delivery_id        = null;

  // Source
  var $stock_id           = null;
  var $stock_class        = null;

  // Target
  var $service_id         = null;
  var $patient_id         = null;
  //var $prescription_id   = null;

  var $date_dispensation  = null;
  var $date_delivery      = null;
  var $datetime_min       = null;
  var $datetime_max       = null;

  var $quantity           = null;
  var $endowment_quantity = null;
  var $endowment_item_id  = null;
  var $order              = null;
  var $manual             = null;
  var $comments           = null;
  var $comments_deliver   = null;
  var $type               = null;

  // Object References
  /**
   * @var CProductStockGroup
   */
  var $_ref_stock           = null;
  var $_ref_stock_service   = null;
  var $_ref_endowment_item  = null;

  /**
   * @var CService
   */
  var $_ref_service         = null;
  var $_ref_patient         = null;

  var $_ref_location_source = null;
  var $_ref_location_target = null;

  var $_ref_delivery_traces = null;
  var $_ref_prises_dispensation = null;
  var $_ref_prises_dispensation_med = null;
  var $_ref_preparateur     = null;

  var $_date_min            = null;
  var $_date_max            = null;
  var $_datetime_min        = null;
  var $_datetime_max        = null;
  var $_delivered           = null;
  var $_auto_deliver        = null;
  var $_make_delivery_trace = null;
  var $_initial_quantity    = null;

  /**
   * @var CProduct[]
   */
  var $_products            = null;
  var $_prises              = null;
  var $_pilulier            = null;
  var $_code_cis            = null;
  var $_code_ucd            = null;
  var $_count_delivered     = null;

  var $_auto_trace;

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
    // can be null when the stock doesn't exist in the group
    $specs['stock_id']          = 'ref class|CProductStock meta|stock_class';
    $specs['stock_class']       = 'str notNull class show|0';

    // Target
    $specs['service_id']        = 'ref class|CService'; // >/dev/null
    $specs['patient_id']        = 'ref class|CPatient';
    //$specs['prescription_id']   = 'ref class|CPrescription';

    $specs['date_dispensation'] = 'dateTime notNull';
    $specs['date_delivery']     = 'dateTime';

    $specs['datetime_min']      = 'dateTime notNull';
    $specs['datetime_max']      = 'dateTime notNull moreEquals|datetime_min';

    $type = (CProductStock::$allow_quantity_fractions ? "float" : "num");
    $specs['quantity']          = "$type notNull";
    $specs['endowment_quantity']= "$type";
    $specs['_initial_quantity'] = "$type";

    $specs['endowment_item_id'] = "ref class|CProductEndowmentItem";
    $specs['order']             = 'bool default|0';
    $specs['manual']            = 'bool default|0';
    $specs['comments']          = 'text';
    $specs['comments_deliver']  = 'text';
    $specs['type']              = 'enum list|other|expired|breakage|loss|gift|discrepancy|notused|toomany';

    $specs['_date_min']         = 'date notNull';
    $specs['_date_max']         = 'date notNull moreEquals|_date_min';

    $specs['_datetime_min']     = 'dateTime notNull';
    $specs['_datetime_max']     = 'dateTime notNull moreEquals|_datetime_min';

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
    $backProps['echanges_phast']  = 'CExchangePhast object_id';
    $backProps['prises_dispensation'] = "CPriseDispensation delivery_id";
    return $backProps;
  }

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
    return $this->_ref_delivery_traces = $this->loadBackRefs("delivery_traces");
  }

  function loadRefsPrisesDispensation(){
    return $this->_ref_prises_dispensation = $this->loadBackRefs("prises_dispensation", "datetime ASC");
  }

  /**
   * @return CMediusers
   */
  function loadRefPreparateur() {
    return $this->_ref_preparateur = $this->loadFirstLog()->loadRefUser()->loadRefMediuser();
  }

  function loadRefsPrisesDispensationMed($date_min, $date_max){
    $where = array();
    $where["delivery_id"] = " = '$this->_id'";
    $where["object_class"] = " = 'CPrescriptionLineMedicament'";
    $where["datetime"] = " BETWEEN '$date_min' AND '$date_max'";

    $prise_dispensation = new CPriseDispensation();
    $this->_ref_prises_dispensation_med = $prise_dispensation->loadList($where, "datetime ASC");
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

  function loadRefEndowmentItem(){
    return $this->_ref_endowment_item = $this->loadFwdRef("endowment_item_id", true);
  }

  /**
   * @return CProductStock
   */
  function loadRefStock(){
    return $this->_ref_stock = $this->loadFwdRef("stock_id", true);
  }

  function loadRefsFwd() {
    $this->loadRefStock();
    $this->loadRefService();
    $this->loadRefPatient();
  }

  function updatePlainFields(){
    parent::updatePlainFields();

    $this->completeField("stock_class");
    if (!$this->stock_class) {
      $this->stock_class = "CProductStockGroup";
    }
  }

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

    if (!$is_new) {
      return;
    }

    if ($this->manual || $this->_auto_trace) {
      $delivery_trace = new CProductDeliveryTrace;
      $delivery_trace->delivery_id = $this->_id;
      $delivery_trace->quantity = $this->quantity;
      $delivery_trace->date_delivery = $this->date_delivery ? $this->date_delivery : mbDateTime();

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

    $this->loadRefStock()->loadRefsFwd();
    if ($this->_auto_deliver || $this->_ref_stock->_ref_product->auto_dispensed || $this->_auto_trace) {
      $this->date_dispensation = mbDateTime();
      $this->order = 0;
      return parent::store();
    }
  }

  function getPerm($permType) {
    if (!$this->_ref_stock || !$this->_ref_service) {
      $this->loadRefsFwd();
    }

    if ($this->_ref_service) {
      return $this->_ref_stock->getPerm($permType) && $this->_ref_service->getPerm($permType);
    }
    else {
      return $this->_ref_stock->getPerm($permType);
    }
    return true;
  }

  function delete(){
    $this->completeField("manual");

    if ($this->manual) {
      $traces = $this->loadBackRefs("delivery_traces");
      foreach ($traces as $_trace) {
        $_trace->delete();
      }
    }

    return parent::delete();
  }
}
?>
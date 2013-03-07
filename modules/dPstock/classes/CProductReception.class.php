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

class CProductReception extends CMbObject {
  // DB Table key
  public $reception_id;

  // DB Fields
  public $date;
  public $societe_id;
  public $group_id;
  public $reference;
  public $bill_number;
  public $bill_date;
  public $locked;

  /** @var CProductOrderItemReception[] */
  public $_ref_reception_items;

  /** @var int */
  public $_count_reception_items;
  public $_total;
  
  /** @var CSociete */
  public $_ref_societe;

  /** @var CGroups */
  public $_ref_group;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "product_reception";
    $spec->key   = "reception_id";
    $spec->uniques["reference"] = array("reference");
    return $spec;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["reception_items"] = "CProductOrderItemReception reception_id";
    return $backProps;
  }

  function getProps() {
    $props = parent::getProps();
    $props['date']       = 'dateTime seekable';
    $props['societe_id'] = 'ref class|CSociete seekable';
    $props['group_id']   = 'ref notNull class|CGroups show|0';
    $props['reference']  = 'str notNull seekable';
    $props['locked']     = 'bool notNull default|0';
    $props['bill_number']= 'str maxLength|64 protected seekable';
    $props['bill_date']  = 'date';
    $props['_total']     = 'currency';
    return $props;
  }
  
  private function getUniqueNumber() {
    $format = CAppUI::conf('dPstock CProductOrder order_number_format');
    
    if (strpos($format, '%id') === false) {
      $format .= '%id';
    }
    
    $format = str_replace('%id', str_pad($this->_id?$this->_id:0, 4, '0', STR_PAD_LEFT), $format);
    return CMbDT::transform(null, null, $format);
  }
  
  function findFromOrder($order_id, $locked = false) {
    $receptions_prob = array();
    $receptions = array();
    
    $order = new CProductOrder;
    $order->load($order_id);
    $order->loadBackRefs("order_items");
    
    foreach ($order->_back["order_items"] as $order_item) {
      $r = $order_item->loadBackRefs("receptions");
      
      foreach ($r as $_r) {
        if (!$_r->reception_id) {
          continue;
        }
        
        $_r->loadRefReception();
        if ($locked || $_r->_ref_reception->locked) {
          continue;
        }
        
        if (!isset($receptions_prob[$_r->reception_id])) {
          $receptions_prob[$_r->reception_id] = 0;
        }
        
        $receptions_prob[$_r->reception_id]++;
        $receptions[$_r->reception_id] = $_r->_ref_reception;
      }
    }
    
    if (!count($receptions_prob)) {
      return $receptions;
    }
    
    $reception_id = array_search(max($receptions_prob), $receptions_prob);
    if ($reception_id) {
      $this->load($reception_id);
    }
    
    return $receptions;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefSociete();
    $this->_view = $this->reference . ($this->societe_id ? " - {$this->_ref_societe->_view}" : "");
  }
  
  function updatePlainFields(){
    if (!$this->_id && $this->locked === null) {
      $this->locked = "0";
    }
    
    return parent::updatePlainFields();
  }
  
  function store () {
    if (!$this->_id && empty($this->reference)) {
      $this->reference = uniqid(rand());
      if ($msg = parent::store()) {
        return $msg;
      }
      $this->reference = $this->getUniqueNumber();
    }
    
    return parent::store();
  }

  function loadRefsBack(){
    $this->_ref_reception_items = $this->loadBackRefs('reception_items');
  }

  function updateTotal(){
    $this->loadRefsBack();
    $total = 0;
    foreach ($this->_ref_reception_items as $_item) {
      $total += $_item->computePrice();
    }
    $this->_total = $total;
  }

  // @todo: supprimer ceci
  function countReceptionItems(){
    $this->_count_reception_items = $this->countBackRefs('reception_items');
  }
  
  function loadRefSociete(){
    $this->_ref_societe = $this->loadFwdRef("societe_id", true);
  }
  
  function loadRefsFwd(){
    $this->loadRefSociete();
    $this->_ref_group = $this->loadFwdRef("group_id", true);
  }

  function getPerm($permType) {
    if (!$this->_ref_reception_items) {
      $this->loadRefsBack();
    }

    foreach ($this->_ref_reception_items as $item) {
      if (!$item->getPerm($permType)) {
        return false;
      }
    }
    return true;
  }
}

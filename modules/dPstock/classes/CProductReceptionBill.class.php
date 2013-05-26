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
 * Product Reception Bill
 */
class CProductReceptionBill extends CMbObject {
  // DB Table key
  public $bill_id;

  // DB Fields
  public $date;
  public $societe_id;
  public $group_id;
  public $reference;

  public $_total;

  /** @var CProductReceptionBillItem[] */
  public $_ref_bill_items;

  /** @var CProductReception */
  public $_ref_reception_item;

  /** @var CGroups */
  public $_ref_group;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "product_bill";
    $spec->key   = "bill_id";
    $spec->uniques["reference"] = array("reference");
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["bill_items"] = "CProductReceptionBillItem bill_id";
    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs['date']        = 'dateTime seekable';
    $specs['societe_id']  = 'ref notNull class|CSociete';
    $specs['group_id']    = 'ref notNull class|CGroups';
    $specs['reference']   = 'str notNull seekable';
    $specs['_total']      = 'currency';
    return $specs;
  }

  /**
   * Get a unique order number
   *
   * @return string
   */
  private function getUniqueNumber() {
    $format = CAppUI::conf('dPstock CProductOrder order_number_format');

    if (strpos($format, '%id') === false) {
      $format .= '%id';
    }

    $format = str_replace('%id', str_pad($this->_id?$this->_id:0, 4, '0', STR_PAD_LEFT), $format);
    return CMbDT::transform(null, null, $format);
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefSociete();
    $this->_view = $this->reference . ($this->societe_id ? " - $this->_ref_societe" : "");
  }

  /**
   * @see parent::store()
   */
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

  /**
   * @see parent::loadRefsBack()
   */
  function loadRefsBack(){
    $this->_ref_bill_items = $this->loadBackRefs('bill_items');
  }

  /**
   * Update total
   *
   * @return void
   */
  function updateTotal(){
    $this->loadRefsBack();
    $total = 0;
    foreach ($this->_ref_bill_items as $_item) {
      $_item->loadRefOrderItem();
      $total += $_item->_ref_order_item->_price;
    }
    $this->_total = $total;
  }

  /**
   * Load societe
   *
   * @return CSociete
   */
  function loadRefSociete(){
    return $this->_ref_societe = $this->loadFwdRef("societe_id", true);
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd(){
    $this->loadRefSociete();
    $this->_ref_group = $this->loadFwdRef("group_id", true);
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    if (!$this->_ref_bill_items) {
      $this->loadRefsBack();
    }

    foreach ($this->_ref_bill_items as $item) {
      if (!$item->getPerm($permType)) {
        return false;
      }
    }
    return true;
  }
}

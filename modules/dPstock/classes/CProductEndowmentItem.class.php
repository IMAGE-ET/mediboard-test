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
 * Product Endowment Item
 */
class CProductEndowmentItem extends CMbObject {
  public $endowment_item_id;
  
  public $quantity;
  public $endowment_id;
  public $product_id;
  public $cancelled;

  /** @var CProductEndowment */
  public $_ref_endowment;

  /** @var CProduct */
  public $_ref_product;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_endowment_item';
    $spec->key   = 'endowment_item_id';
    $spec->uniques["unique"] = array("endowment_id", "product_id");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs['quantity']     = 'num notNull min|0';
    $specs['endowment_id'] = 'ref notNull class|CProductEndowment autocomplete|name';
    $specs['product_id']   = 'ref notNull class|CProduct autocomplete|name dependsOn|cancelled seekable';
    $specs['cancelled']    = 'bool notNull default|0';
    return $specs;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["deliveries"] = "CProductDelivery endowment_item_id";
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = "$this->_ref_product x $this->quantity";
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd(){
    parent::loadRefsFwd();
    $this->_ref_endowment = $this->loadFwdRef("endowment_id", true);
    $this->_ref_product = $this->loadFwdRef("product_id", true);
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    $this->loadRefsFwd();

    return parent::getPerm($permType) && 
      $this->_ref_endowment->getPerm($permType) && 
      $this->_ref_product->getPerm($permType);
  }
}

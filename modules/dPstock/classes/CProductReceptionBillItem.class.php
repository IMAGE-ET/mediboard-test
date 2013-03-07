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

class CProductReceptionBillItem extends CMbObject {
  // DB Table key
  public $reception_bill_item_id;

  // DB Fields
  public $bill_id;
  public $reception_item_id;
  public $quantity;
  public $unit_price; // In the case the reference price changes

  /** @var CProductOrderItemReception */
  public $_ref_reception_item;

  /** @var CProductReceptionBill */
  public $_ref_bill;

  // Form fields
  public $_price;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_reception_bill_item';
    $spec->key   = 'reception_bill_item_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs['bill_id']           = 'ref class|CProductReceptionBill';
    $specs['reception_item_id'] = 'ref class|CProductOrderItemReception';
    $specs['quantity']          = 'num min|0';
    $specs['unit_price']        = 'currency precise';
    $specs['_price']            = 'currency';
    return $specs;
  }
}

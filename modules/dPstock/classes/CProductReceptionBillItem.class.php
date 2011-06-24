<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CProductReceptionBillItem extends CMbObject {
  // DB Table key
  var $reception_bill_item_id = null;

  // DB Fields
  var $bill_id            = null;
  var $reception_item_id  = null;
  var $quantity           = null;
  var $unit_price         = null; // In the case the reference price changes

  // Object References
  //    Single
  var $_ref_reception_item= null;
  var $_ref_bill          = null;

  // Form fields
  var $_price             = null;

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

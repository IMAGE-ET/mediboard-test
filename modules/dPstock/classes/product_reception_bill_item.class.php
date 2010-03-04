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
  var $bill_item_id       = null;

  // DB Fields
  var $reception_item_id  = null;
  var $bill_id            = null;
  var $quantity           = null;
  var $unit_price         = null; // In the case the reference price changes

  // Object References
  //    Single
  var $_ref_reference     = null;
  
  //    Multiple
  var $_ref_receptions    = null;

  // Form fields
  var $_price             = null;
  var $_cond_price        = null;
  var $_date_received     = null;
  var $_quantity_received = null;
  var $_quantity          = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_order_item';
    $spec->key   = 'order_item_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs['bill_id']           = 'ref class|CProductReceptionBill';
    $specs['reception_item_id'] = 'ref class|CProductOrderItemReception';
    $specs['quantity']          = 'num min|0';
    $specs['unit_price']        = 'currency precise';
    return $specs;
  }
}

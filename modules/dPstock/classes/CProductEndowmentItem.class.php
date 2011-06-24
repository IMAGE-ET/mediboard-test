<?php /* $Id: product_stock_service.class.php 8121 2010-02-23 10:23:49Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 8121 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CProductEndowmentItem extends CMbObject {
  var $endowment_item_id = null;
  
  var $quantity          = null;
  var $endowment_id      = null;
  var $product_id        = null;

  // Object References
  var $_ref_endowment    = null;
  var $_ref_product      = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_endowment_item';
    $spec->key   = 'endowment_item_id';
    $spec->uniques["unique"] = array("endowment_id", "product_id");
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs['quantity']     = 'num notNull min|0';
    $specs['endowment_id'] = 'ref notNull class|CProductEndowment autocomplete|name';
    $specs['product_id']   = 'ref notNull class|CProduct autocomplete|name dependsOn|cancelled seekable';
    return $specs;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = "$this->_ref_product x $this->quantity";
  }

  function loadRefsFwd(){
    parent::loadRefsFwd();
    $this->_ref_endowment = $this->loadFwdRef("endowment_id", true);
    $this->_ref_product = $this->loadFwdRef("product_id", true);
  }
  
  function getPerm($permType) {
    $this->loadRefsFwd();

    return parent::getPerm($permType) && 
      $this->_ref_endowment->getPerm($permType) && 
      $this->_ref_product->getPerm($permType);
  }
}
?>
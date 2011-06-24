<?php /* $Id: product_stock_service.class.php 8121 2010-02-23 10:23:49Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 8121 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CProductEndowment extends CMbObject {
  var $endowment_id = null;
  
  var $name         = null;
  var $service_id   = null;

  // Object References
  var $_ref_service = null;
  var $_ref_endowment_items = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_endowment';
    $spec->key   = 'endowment_id';
    $spec->uniques["unique"] = array("name", "service_id");
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs['name']       = 'str notNull';
    $specs['service_id'] = 'ref notNull class|CService autocomplete|nom';
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["endowment_items"] = "CProductEndowmentItem endowment_id";
    return $backProps;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "$this->name ($this->_ref_service)";
  }

  function loadRefsFwd(){
    parent::loadRefsFwd();
    $this->_ref_service = $this->loadFwdRef("service_id", true);
  }

  function loadRefsBack(){
    $ljoin = array(
      "product" => "product.product_id = product_endowment_item.product_id",
      "product_stock_group" => "product_stock_group.product_id = product.product_id",
      "product_stock_location" => "product_stock_location.stock_location_id = product_stock_group.location_id"
    );
    $this->_ref_endowment_items = $this->loadBackRefs('endowment_items', "product_stock_location.position, product.name", null, null, $ljoin);
  }
  
  function getPerm($permType) {
    $this->loadRefsFwd();
    
    return parent::getPerm($permType) && $this->_ref_service->getPerm($permType);
  }
}
?>
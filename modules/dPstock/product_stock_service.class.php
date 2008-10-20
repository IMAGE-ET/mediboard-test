<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CProductStockService extends CProductStock {
  // DB Fields
  var $service_id   = null;

  // Object References
  //    Single
  var $_ref_service = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_stock_service';
    $spec->key   = 'stock_id';
    return $spec;
  }

  function getSpecs() {
    $specs = parent::getSpecs();
    $specs['service_id'] = 'notNull ref class|CService';
    return $specs;
  }
  
  static function getFromCode($code, $service_id = null) {
    $stock = new CProductStockService();
    
    $where = array();
    $where['product.code'] = "= '$code'";
    if ($service_id) {
      $where['product_stock_service.service_id'] = "= $service_id";
    }
    $ljoin = array();
    $ljoin['product'] = 'product_stock_service.product_id = product.product_id';

    $stock->loadObject($where, null, null, $ljoin);
    return $stock;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->_ref_product->_view." ({$this->_ref_service->_view})";
  }

  function loadRefsFwd(){
    parent::loadRefsFwd();
    $this->_ref_service = new CService();
    $this->_ref_service = $this->_ref_service->getCached($this->service_id);
  }
  
  function getPerm($permType) {
    if(!$this->_ref_service) {
      $this->loadRefsFwd();
    }
    return parent::getPerm($permType) && $this->_ref_service->getPerm($permType);
  }
}
?>
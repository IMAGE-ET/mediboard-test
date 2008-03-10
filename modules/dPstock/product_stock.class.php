<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */

class CProductStock extends CMbObject {
  // DB Table key
  var $stock_id                 = null;

  // DB Fields
  var $product_id               = null;
  var $group_id                 = null;
  var $quantity                 = null;
  var $order_threshold_critical = null;
  var $order_threshold_min      = null;
  var $order_threshold_optimum  = null;
  var $order_threshold_max      = null;

  // Object References
  //    Single
  var $_ref_product             = null;
  var $_ref_group               = null;

  //    Multiple
  var $_ref_stock_outs          = null;

  // Form fields
  var $_rupture                 = null;

  function CProductStock() {
    $this->CMbObject('product_stock', 'stock_id');
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs['stock_outs'] = 'CProductStockOut stock_id';
    return $backRefs;
  }

  function getSpecs() {
    return array (
      'product_id'               => 'notNull ref class|CProduct',
      'group_id'                 => 'notNull ref class|CGroups',
      'quantity'                 => 'notNull num pos',
      'order_threshold_critical' => 'num pos',
      'order_threshold_min'      => 'notNull num pos moreEquals|order_threshold_critical',
      'order_threshold_optimum'  => 'num pos moreEquals|order_threshold_min',
      'order_threshold_max'      => 'notNull num pos moreEquals|order_threshold_optimum',
    );
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = $this->_ref_product->_view . " (x$this->quantity)";
    $this->_rupture = $this->quantity <= $this->order_threshold_min; // TODO: gestion des autres seuils
  }

  function loadRefsFwd(){
    $this->_ref_group = new CGroups;
    $this->_ref_group->load($this->group_id);

    $this->_ref_product = new CProduct;
    $this->_ref_product->load($this->product_id);
  }

  function loadRefsBack(){
    $this->_ref_stock_outs = $this->loadBackRefs('stock_outs');
  }
  
  function getPerm($permType) {
    if(!$this->_ref_group || !$this->_ref_product) {
      $this->loadRefsFwd();
    }
    return ($this->_ref_group->getPerm($permType) && $this->_ref_product->getPerm($permType));
  }

  function check() {
    if($this->product_id && $this->group_id) {
      $where['product_id'] = "= '$this->product_id'";
      $where['group_id']   = "= '$this->group_id'";
      
      $VerifDuplicateKey = new CProductStock();
      $ListVerifDuplicateKey = $VerifDuplicateKey->loadList($where);
      
      if(count($ListVerifDuplicateKey) != 0) {
        return 'Erreur : Le stock de ce produit existe déjà';
      } else {
        return null;
      }
    }
  }
}
?>
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
  
  // Stock percentages 
  var $_quantity                = null;
  var $_critical                = null;
  var $_min                     = null;
  var $_optmimum                = null;
  var $_max                     = null;
  // In which part of the graph the quantity is
  var $_zone                    = null;
  

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
      'quantity'                 => 'num pos notNull',
      'order_threshold_critical' => 'num pos',
      'order_threshold_min'      => 'num pos notNull moreEquals|order_threshold_critical',
      'order_threshold_optimum'  => 'num pos moreEquals|order_threshold_min',
      'order_threshold_max'      => 'num pos notNull moreEquals|order_threshold_optimum',
      '_quantity'                => 'pct',
      '_critical'                => 'pct',
      '_min'                     => 'pct',
      '_optimum'                 => 'pct',
      '_max'                     => 'pct',
      '_zone'                    => 'num',
    );
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = $this->_ref_product->_view . " (x$this->quantity)";
    
    $max = max(array($this->quantity, $this->order_threshold_max)) / 100;
    
    $this->_quantity = $this->quantity                 / $max;
    $this->_critical = $this->order_threshold_critical / $max;
    $this->_min      = $this->order_threshold_min      / $max - $this->_critical;
    $this->_optimum  = $this->order_threshold_optimum  / $max - $this->_critical - $this->_min;
    $this->_max      = $this->order_threshold_max      / $max - $this->_critical - $this->_min - $this->_optimum;
      
    if ($this->_quantity       <= $this->_critical) {
      $this->_zone = 0;
      
    } elseif ($this->_quantity <= $this->_min) {
      $this->_zone = 1;
      
    } elseif ($this->quantity  <= $this->_optimum) {
      $this->_zone = 2;
      
    } else {
      $this->_zone = 3;
    }
  }
  
  function updateDBFields() {
    /*if (!$this->order_threshold_critical) {
    	$this->order_threshold_critical = $this->order_threshold_min;
    }
    if (!$this->order_threshold_optimum) {
      $this->order_threshold_optimum = round(($this->order_threshold_min+$this->order_threshold_max)/2);
    }*/
    //mbTrace($this);
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
      $where['stock_id']   = " != '$this->stock_id'";
      
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
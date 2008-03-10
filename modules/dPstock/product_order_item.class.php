<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPtock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */

class CProductOrderItem extends CMbObject {
  // DB Table key
  var $order_item_id  = null;

  // DB Fields
  var $reference_id   = null;
  var $order_id       = null;
  var $quantity       = null;
  var $unit_price     = null; // In the case the reference price changes
  var $date_received  = null;

  // Object References
  //    Single
  var $_ref_order     = null;
  var $_ref_reference = null;

  // Form fields
  var $_price         = null;
  var $_received      = null;

  function CProductOrderItem() {
    $this->CMbObject('product_order_item', 'order_item_id');
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
    return array (
      'reference_id'  => 'notNull ref class|CProductReference',
      'order_id'      => 'notNull ref class|CProductOrder',
      'quantity'      => 'notNull num pos',
      'unit_price'    => 'currency',
      'date_received' => 'dateTime',
    );
  }

  function updateFormFields() {
    parent::updateFormFields();
    //$this->loadRefsFwd();
    $this->_ref_reference = new CProductReference();
    $this->_ref_reference->load($this->reference_id);
    $this->_view = $this->_ref_reference->_view;
    $this->_price = $this->unit_price * $this->quantity;
  }
  
  function updateDBFields() {
    parent::updateDBFields();
    //$this->date_received = mbDateTime();
  }

  function loadRefsFwd() {
    $this->_ref_reference = new CProductReference();
    $this->_ref_reference->load($this->reference_id);

    $this->_ref_order = new CProductOrder();
    $this->_ref_order->load($this->order_id);
  }

/*  function check() {
    if($this->order_id && $this->reference_id) {
      $where['order_id']     = "= '$this->order_id'";
      $where['reference_id'] = "= '$this->reference_id'";

      $VerifDuplicateKey = new CProductOrderItem();
      $ListVerifDuplicateKey = $VerifDuplicateKey->loadList($where);

      if(count($ListVerifDuplicateKey) != 0) {
        return 'Erreur : La référence produit existe déjà dans cette commande';
      } else {
        return null;
      }
    }
  }*/
}
?>
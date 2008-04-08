<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */

class CProductDelivery extends CMbObject {
  // DB Table key
  var $delivery_id  = null;

  // DB Fields
  var $product_id   = null;
  var $date         = null;
  var $target_class = null;
  var $target_id    = null;
  var $description  = null;
  var $code         = null;

  // Object References
  //    Single
  var $_ref_product = null;
  var $_ref_target  = null;
  
  var $_date_min    = null;
  var $_date_max    = null;

  function CProductDelivery() {
    $this->CMbObject('product_delivery', 'delivery_id');
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
    $specs = parent::getSpecs();
    return array_merge($specs, array (
      'product_id'   => 'notNull ref class|CProduct',
      'date'         => 'notNull dateTime',
      'target_class' => 'notNull str maxLength|25',
      'target_id'    => 'notNull ref class|CMbObject meta|target_class',
      'description'  => 'text',
      'code'         => 'str maxLength|32',
	    '_date_min'    => 'dateTime',
	    '_date_max'    => 'dateTime',
    ));
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_ref_product->updateFormFields();
    
    if ($this->_ref_target) {
      $this->_ref_target->updateFormFields();
    }
    $this->_view = $this->_ref_product->_view . (($this->_ref_target)?" (pour {$this->_ref_target->_view})":'');
  }

  function loadRefsFwd() {
    $this->_ref_product = new CProduct();
    $this->_ref_product->load($this->product_id);
    
    if ($this->target_class) {
	    $this->_ref_target = new $this->target_class;
	    $this->_ref_target->load($this->target_id);
    }
  }

  function getPerm($permType) {
    if(!$this->_ref_product || !$this->_ref_target) {
      $this->loadRefsFwd();
    }
    return ($this->_ref_product->getPerm($permType) && $this->_ref_target->getPerm($permType));
  }

  function check() {
  	$this->loadRefsFwd();
    if(!$this->_ref_target) {
      return 'Erreur : La cible n\'existe pas';
    } else {
      return null;
    }
  }
}

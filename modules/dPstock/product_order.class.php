<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */

class CProductOrder extends CMbObject {
  // DB Table key
  var $order_id         = null;

  // DB Fields
  var $name             = null;
  var $date_ordered     = null;
  var $date_received    = null;
  var $societe_id       = null;
  var $received         = null;
  var $locked           = null;
  var $order_number     = null;

  // Object References
  //    Multiple
  var $_ref_order_items = null;
  
  //    Single
  var $_ref_societe     = null;

  // Form fields
  var $_total           = null;
  var $_count_received  = null;

  function CProductOrder() {
    $this->CMbObject('product_order', 'order_id');
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs['order_items'] = 'CProductOrderItem order_id';
    return $backRefs;
  }

  function getSpecs() {
    return array (
      'name'            => 'str maxLength|64',
      'date_ordered'    => 'dateTime',
      'date_received'   => 'dateTime',
      'societe_id'      => 'notNull ref class|CSociete',
      'received'        => 'notNull bool',
      'locked'          => 'notNull bool',
      'order_number'    => 'str',
      '_total'          => 'currency',
      '_count_received' => 'num pos',
    );
  }

  function getSeeks() {
    return array (
      'name'         => 'like',
      'date_ordered' => 'like',
      'societe_id'   => 'like',
      'order_number' => 'like',
    );
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsBack();

    $this->_total = 0;
    $this->_count_received = 0;
    if ($this->_ref_order_items) {
      foreach ($this->_ref_order_items as $item) {
        $item->updateFormFields();
        $this->_total += $item->_price;
        if ($item->date_received) {
          $this->_count_received++;
        }
      }
    }
    
    $count = count($this->_ref_order_items);
    $this->_view = /*mbDateTime(null, $this->date_ordered).*/' ['.$count.' article'.(($count>1)?'s':'').', total = '.$this->_total.']';
  }
  
  function updateDBFields() {
    parent::updateDBFields();
    //$this->date_ordered = mbDateTime();
  }

  function loadRefsBack(){
    $this->_ref_order_items = $this->loadBackRefs('order_items');
  }
  
  function loadRefsFwd(){
    $this->_ref_societe = new CSociete;
    $this->_ref_societe->load($this->societe_id);
  }

  function getPerm($permType) {
    if(!$this->_ref_order_items) {
      $this->loadRefsFwd();
    }

    foreach ($this->_ref_order_items as $item) {
      if (!$perm->getPerm($permType)) {
        return false;
      }
    }
    return true;
  }
}
?>
<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */

class CSociete extends CMbObject {
  // DB Table key
  var $societe_id     = null;

  // DB Fields
  var $name            = null;
  var $address         = null;
  var $postal_code     = null;
  var $city            = null;
  var $phone           = null;
  var $email           = null;
  var $contact_name    = null;
  var $contact_surname = null;

  // Object References
  //     Multiple
  var $_ref_product_references = null;
  var $_ref_products   = null;

  function CSociete() {
    $this->CMbObject('societe', 'societe_id');
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs['product_references'] = 'CProductReference societe_id';
    $backRefs['products']           = 'CProduct societe_id';
    return $backRefs;
  }

  function getSpecs() {
    return array (
      'name'            => 'notNull str maxLength|50',
      'address'         => 'str',
      'postal_code'     => 'code insee',
      'city'            => 'str',
      'phone'           => 'str',
      'email'           => 'email',
      'contact_name'    => 'str maxLength|50',
      'contact_surname' => 'str maxLength|50',
    );
  }

  function getSeeks() {
    return array (
      'name'            => 'like',
      'city'            => 'like',
      'contact_name'    => 'like',
      'contact_surname' => 'like',
    );
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->name;
  }

  function loadRefsBack() {
    $where = array();
    $where['societe_id'] = "= '$this->societe_id'";
    
    $this->_ref_product_references = new CProductReference();
    $this->_ref_product_references = $this->_ref_product_references->loadList($where);
    
    $this->_ref_products = new CProduct();
    $this->_ref_products = $this->_ref_products->loadList($where);
  }

}
?>
<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage pharmacie
 *  @version $Revision: $
 *  @author Fabien Ménager
 */

class CDispensiation extends CMbObject {
  // DB Table key
  var $dispensiation_id     = null;

  // DB Fields
  var $product_id           = null;

  // Object References
  //    Single
  var $_ref_product         = null;

  //    Multiple

  // Filter Fields

  function Cdispensiation() {
    $this->CMbObject('dispensiation', 'dispensiation_id');
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs['product'] = 'CProduct product_id';
    return $backRefs;
  }

  function getSpecs() {
    $specs = parent::getSpecs();
    return array_merge($specs, array (
      'product_id'  => 'notNull ref class|CProduct',
    ));
  }

  function getSeeks() {
    return array (
    );
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->name;
  }

  function loadRefsBack() {
  }

  function loadRefsFwd() {
    $this->_ref_product = new CProduct;
    $this->_ref_product->load($this->product_id);
  }

  function getPerm($permType) {
    return true;
  }
}
?>
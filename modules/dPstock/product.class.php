<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPstock
 *  @version $Revision: $
 *  @author Fabien Ménager
 */

class CProduct extends CMbObject {
  // DB Table key
  var $product_id        = null;

  // DB Fields
  var $name              = null;
  var $description       = null;
  var $code              = null;
  var $category_id       = null;
  var $societe_id        = null;

  // Object References
  //    Single
  var $_ref_category     = null;
  var $_ref_societe      = null;

  //    Multiple
  var $_ref_stocks       = null;
  var $_ref_references   = null;

  // Filter Fields
  var $_date_min         = null;
  var $_date_max         = null;
  
  // This group's stock id
  var $_ref_stock_group  = null;

  function CProduct() {
    $this->CMbObject('product', 'product_id');
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs['references'] = 'CProductReference product_id';
    $backRefs['stocks']     = 'CProductStock product_id';
    return $backRefs;
  }

  function getSpecs() {
    return array (
      'name'        => 'notNull str maxLength|50',
      'description' => 'text',
      'code'        => 'str maxLength|32',
      'category_id' => 'notNull ref class|CProductCategory',
      'societe_id'  => 'notNull ref class|CSociete',
    );
  }

  function getSeeks() {
    return array (
      'name'            => 'like',
      'description'     => 'like',
    );
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->name;
  }

  function loadRefsBack() {
  	$this->_ref_references = $this->loadBackRefs('references');
    $this->_ref_stocks = $this->loadBackRefs('stocks');
  }

  function loadRefsFwd() {
    $this->_ref_category = new CProductCategory;
    $this->_ref_category->load($this->category_id);

    $this->_ref_societe = new CSociete;
    $this->_ref_societe->load($this->societe_id);
  }
  
  // Loads the stock associated to the current group
  function loadRefStock() {
  	global $g;
  	
    $this->_ref_stock_group = new CProductStock();
    $this->_ref_stock_group->group_id = $g;
    $this->_ref_stock_group->product_id = $this->product_id;
    $this->_ref_stock_group->loadMatchingObject();
  }

  function getPerm($permType) {
    if(!$this->_ref_category) {
      $this->loadRefsFwd();
    }
    return ($this->_ref_category->getPerm($permType));
  }
  
  function deliver($target_class, $target_id, $description = '') {
  	$delivery = new CProductDelivery();
  	if ($this->_id) {
	  	$delivery->product_id   = $this->_id;
	  	$delivery->date         = mbDateTime();
	  	$delivery->target_class = $target_class;
	  	$delivery->target_id    = $target_id;
	  	$delivery->description  = $description;
	  	$delivery->store();
	  	return true;
  	} else {
  		return false;
  	}
  }
}
?>
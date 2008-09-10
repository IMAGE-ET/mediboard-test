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
  var $_ref_stocks_group   = null;
  var $_ref_stocks_service = null;
  var $_ref_references   = null;
  
  // This group's stock id
  var $_ref_stock_group  = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product';
    $spec->key   = 'product_id';
    return $spec;
  }

  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs['references']     = 'CProductReference product_id';
    $backRefs['stocks_group']   = 'CProductStockGroup product_id';
    $backRefs['stocks_service'] = 'CProductStockService product_id';
    return $backRefs;
  }

  function getSpecs() {
    $specs = parent::getSpecs();
    $specs['name']        = 'notNull str maxLength|50';
    $specs['description'] = 'text';
    $specs['code']        = 'str maxLength|32';
    $specs['category_id'] = 'notNull ref class|CProductCategory';
    $specs['societe_id']  = 'ref class|CSociete';
    return $specs;
  }

  function getSeeks() {
    return array (
      'name'            => 'like',
      'description'     => 'like',
      'code'            => 'like',
    );
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = ($this->code ? "[$this->code] " : '') . $this->name;
  }

  function loadRefsBack() {
  	$this->_ref_references = $this->loadBackRefs('references');
    $this->_ref_stocks_group = $this->loadBackRefs('stocks_group');
    $this->_ref_stocks_service = $this->loadBackRefs('stocks_service');
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
  	
    $this->_ref_stock_group = new CProductStockGroup();
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
  
  function store() {
    if(!$this->_id) {
      $duplicate_code = new CProduct();
      $duplicate_code->code = $this->code;
      $duplicate_code->loadMatchingObject();
      if ($duplicate_code->_id) {
        $this->_id = $duplicate_code->_id;
      }
    }
    return parent::store();
  }
}
?>
<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage stock
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

class CProductCategory extends CMbObject {
  public $category_id;
  
  // DB fields
  public $name;
  
  public $_count_products;

  /** @var CProduct[] */
  public $_ref_products;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_category';
    $spec->key   = 'category_id';
    return $spec;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['products'] = 'CProduct category_id';
    return $backProps;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs['name'] = 'str notNull maxLength|50 seekable show|0';
    $specs['_count_products'] = 'num show|1';
    return $specs;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->name;
  }
  
  function loadView(){
    parent::loadView();
    
    $this->countProducts();
  }

  function loadRefsBack() {
    $this->_ref_products = $this->loadBackRefs('products');
  }
  
  function countProducts(){
    $this->_count_products = $this->countBackRefs("products");
  }
}

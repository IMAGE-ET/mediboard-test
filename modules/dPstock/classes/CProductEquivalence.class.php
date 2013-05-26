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

/**
 * Product Equivalence
 */
class CProductEquivalence extends CMbObject {
  public $equivalence_id;

  // DB Fields
  public $name;

  /** @var CProduct[] */
  public $_ref_products;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_equivalence';
    $spec->key   = 'equivalence_id';
    $spec->uniques["name"] = array("name");
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['products'] = 'CProduct equivalence_id';
    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs['name'] = 'str notNull seekable';
    return $specs;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->name;
  }

  /**
   * Load products
   *
   * @return CProduct[]
   */
  function loadRefsProducts(){
    return $this->_ref_products = $this->loadBackRefs('products', 'name');
  }

  /**
   * @see parent::loadRefsBack()
   */
  function loadRefsBack() {
    $this->loadRefsProducts();
  }
}

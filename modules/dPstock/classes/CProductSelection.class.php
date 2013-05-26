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
 * Product Selection
 */
class CProductSelection extends CMbObject {
  public $selection_id;

  // DB Fields
  public $name;

  /** @var CProductSelectionItem[] */
  public $_ref_items;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "product_selection";
    $spec->key   = "selection_id";
    $spec->uniques["name"] = array("name");
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["selection_items"] = "CProductSelectionItem selection_id";
    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["name"] = "str notNull seekable";
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
   * @see parent::loadRefsBack()
   */
  function loadRefsBack() {
    $this->loadRefsItems();
  }

  /**
   * @return CProductSelectionItem[]
   */
  function loadRefsItems() {
    $ljoin = array(
      "product" => "product.product_id = product_selection_item.product_id"
    );
    return $this->_ref_items = $this->loadBackRefs("selection_items", "product.name", null, null, $ljoin);
  }
}

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

class CProductSelectionItem extends CMbObject {
  // DB Table key
  public $selection_item_id;

  // DB Fields
  public $product_id;
  public $selection_id;

  /** @var CProduct */
  public $_ref_product;

  /** @var CProductSelection */
  public $_ref_selection;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_selection_item';
    $spec->key   = 'selection_item_id';
    $spec->uniques["selection"] = array("product_id", "selection_id");
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["product_id"]   = "ref notNull class|CProduct autocomplete|name dependsOn|cancelled";
    $specs["selection_id"] = "ref notNull class|CProductSelection";
    return $specs;
  }
  
  function loadRefsFwd(){
    $this->_ref_product = $this->loadFwdRef("product_id", true);
    $this->_ref_selection = $this->loadFwdRef("selection_id", true);
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = $this->_ref_product->_view;
  }
}

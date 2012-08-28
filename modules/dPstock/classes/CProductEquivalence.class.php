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

class CProductEquivalence extends CMbObject {
  // DB Table key
  var $equivalence_id = null;

  // DB Fields
  var $name           = null;

  // Object References
  //    Multiple
  var $_ref_products  = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_equivalence';
    $spec->key   = 'equivalence_id';
    $spec->uniques["name"] = array("name");
    return $spec;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['products'] = 'CProduct equivalence_id';
    return $backProps;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs['name'] = 'str notNull seekable';
    return $specs;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->name;
  }
  
  function loadRefsProducts(){
    return $this->_ref_products = $this->loadBackRefs('products', 'name');
  }

  function loadRefsBack() {
    $this->loadRefsProducts();
  }
}

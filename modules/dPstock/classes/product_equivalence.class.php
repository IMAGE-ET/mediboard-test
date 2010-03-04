<?php /* $Id: product.class.php 8121 2010-02-23 10:23:49Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 8121 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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

  function loadRefsBack() {
  	$this->_ref_products = $this->loadBackRefs('products');
  }
}

<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CProductDiscrepancy extends CMbMetaObject { // Ecart d'inventaire
  // DB Fields
  var $discrepancy_id = null;
  
  var $quantity       = null;
  var $date           = null;
  var $description    = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_discrepancy';
    $spec->key   = 'discrepancy_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs['quantity']     = 'num notNull';
    $specs['date']         = 'dateTime notNull';
    $specs['description']  = 'text';
    $specs['object_id']    = 'ref notNull class|CProductStock meta|object_class';
    return $specs;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = $this->_ref_object->_view." ({$this->quantity})";
  }
}
?>
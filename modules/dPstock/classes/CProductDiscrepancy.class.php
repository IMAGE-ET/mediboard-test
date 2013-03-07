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

class CProductDiscrepancy extends CMbMetaObject { // Ecart d'inventaire
  public $discrepancy_id;
  
  public $quantity;
  public $date;
  public $description;
  
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

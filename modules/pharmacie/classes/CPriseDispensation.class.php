<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPriseDispensation extends CMbMetaObject {
  // DB Table key
  var $prise_dispensation_id = null;

  // DB Fields
  var $delivery_id     = null;
  var $datetime        = null;
	
  var $quantite_adm    = null;
  var $unite_adm       = null;
  var $quantite_disp   = null;
	

  /**
   * @var CProductDelivery
   */
  var $_ref_delivery   = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prise_dispensation';
    $spec->key   = 'prise_dispensation_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs['delivery_id']   = 'ref notNull class|CProductDelivery';
    $specs['datetime']      = 'dateTime notNull';
		
    $specs['quantite_adm']  = 'float notNull';
    $specs['unite_adm']     = 'str notNull';
    $specs['quantite_disp'] = 'num notNull';
		
    $specs['object_class']  = 'enum notNull list|CPrescriptionLineMixItem|CPrescriptionLineMedicament';
    return $specs;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->getFormattedValue("datetime");
  }
  
  function loadRefDelivery($cache = true) {
    $this->_ref_delivery = $this->loadFwdRef("delivery_id", $cache);
  }
}

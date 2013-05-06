<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CElementPrescriptionToReeducation extends CMbObject {
  // DB Fields
  public $element_prescription_id;
  public $code;
  public $commentaire;
  
  public $_ref_element_prescription;
  
  function getProps() {
    $props = parent::getProps();
    $props["element_prescription_id"] = "ref notNull class|CElementPrescription";
    $props["code"]                    = "str notNull length|7";
    $props["commentaire"]             = "str";
    return $props;
  }
  
  function loadRefElementPrescription() {
    return $this->_ref_element_prescription = $this->loadFwdRef("element_prescription_id", true);
  }
    
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = "Code $this->code";
  }
}

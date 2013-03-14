<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CElementPrescriptionToReeducation extends CMbObject {
  // DB Fields
  var $element_prescription_id = null;
  var $code = null;
  var $commentaire = null;
  
  var $_ref_element_prescription = null;
  
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
  
  function loadView(){
    parent::loadView();
    $this->loadRefActiviteCsARR();
  }
}

?>
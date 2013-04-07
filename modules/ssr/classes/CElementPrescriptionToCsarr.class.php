<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CElementPrescriptionToCsarr extends CElementPrescriptionToReeducation {
  // DB Table key
  var $element_prescription_to_csarr_id = null;
    
  var $_ref_activite_csarr = null;
  var $_count_csarr_by_type = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'element_prescription_to_csarr';
    $spec->key   = 'element_prescription_to_csarr_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["code"] = "str notNull length|7";
    return $props;
  }
  
  function check(){
    // Verification du code Csarr saisi
    $code_csarr = CActiviteCsARR::get($this->code);
    if(!$code_csarr->code){
      return "Ce code n'est pas un code CsARR valide";
    }
    return parent::check();
  }
  
  function loadRefActiviteCsarr() {
    $activite = CActiviteCsARR::get($this->code);
    $activite->loadRefHierarchie();
    return $this->_ref_activite_csarr = $activite;
  }
  
  function loadView(){
    parent::loadView();
    $this->loadRefActiviteCsarr();
  }
}

?>
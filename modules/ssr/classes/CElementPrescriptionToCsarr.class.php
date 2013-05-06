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

class CElementPrescriptionToCsarr extends CElementPrescriptionToReeducation {
  // DB Table key
  public $element_prescription_to_csarr_id;
    
  public $_ref_activite_csarr;
  public $_count_csarr_by_type;
  
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
    if (!$code_csarr->code) {
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

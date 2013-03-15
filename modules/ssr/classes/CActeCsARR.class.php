<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CActeCsARR extends CActeSSR {
  // DB Table key
  var $acte_csarr_id = null;
    
  // References
  var $_ref_activite_csarr = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'acte_csarr';
    $spec->key   = 'acte_csarr_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["code"] = "str notNull length|7 show|0";
    return $props;
  }

  function loadRefActiviteCdARR() {
    $activite = CActiviteCsARR::get($this->code);
    return $this->_ref_activite_csarr = $activite;
  }
  
  function loadView(){
    parent::loadView();
    $this->loadRefActiviteCsARR();
  }
}

?>
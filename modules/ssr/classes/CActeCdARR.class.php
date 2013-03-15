<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CActeCdARR extends CActeSSR {
  // DB Table key
  var $acte_cdarr_id = null;
    
  // References
  var $_ref_activite_cdarr = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'acte_cdarr';
    $spec->key   = 'acte_cdarr_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["code"] = "str notNull length|4 show|0";
    return $props;
  }

  function loadRefActiviteCdARR() {
    $activite = CActiviteCdARR::get($this->code);
    $activite->loadRefTypeActivite();
    return $this->_ref_activite_cdarr = $activite;
  }
  
  function loadView(){
    parent::loadView();
    $this->loadRefActiviteCdARR();
  }
}

?>
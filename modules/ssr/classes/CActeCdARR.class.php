<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CActeCdARR extends CMbObject {
  // DB Table key
  var $acte_cdarr_id = null;
  
  // DB Fields
  var $evenement_ssr_id = null;
  var $administration_id = null;
  var $sejour_id         = null;
  var $code = null;
  
  // References
  var $_ref_activite_cdarr = null;
  var $_ref_administration = null;
  var $_ref_evenement_ssr  = null;
  var $_ref_sejour         = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table       = 'acte_cdarr';
    $spec->key         = 'acte_cdarr_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["evenement_ssr_id"]  = "ref class|CEvenementSSR cascade";
    $props["administration_id"] = "ref class|CAdministration cascade";
    $props["sejour_id"]         = "ref class|CSejour";
    $props["code"]              = "str notNull length|4 show|0";
    return $props;
  }

  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = $this->code;
  }
  
  function loadRefEvenementSSR(){
    $this->_ref_evenement_ssr = $this->loadFwdRef("evenement_ssr_id", true);
  }
  
  function loadRefAdministration(){
    $this->_ref_administration = $this->loadFwdRef("administration_id", true);
  }
  
  function loadRefSejour(){
    $this->_ref_sejour = $this->loadFwdRef("sejour_id", true);
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
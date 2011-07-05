<?php

/**
 * Interop Actor EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CInteropActor 
 * Interoperability Actor
 */
class CInteropActor extends CMbObject {
  // DB Fields
  var $nom         = null;
  var $libelle     = null;
  var $group_id    = null;
  var $actif       = null;
  
  // Form fields
  var $_reachable         = null;
  var $_parent_class_name = null;
  
  var $_tag_patient  = null;
  var $_tag_sejour   = null;
  var $_tag_mediuser = null;
  var $_tag_service  = null;
  
  // Forward references
  var $_ref_group             = null;
  var $_ref_exchanges_sources = null;
  var $_ref_last_message      = null;
  
  function getProps() {
    $props = parent::getProps();
    $props["nom"]        = "str notNull";
    $props["libelle"]    = "str";
    $props["group_id"]   = "ref notNull class|CGroups autocomplete|text";
    $props["actif"]      = "bool notNull";
    
    $props["_reachable"]         = "bool";
    $props["_parent_class_name"] = "str";
    $props["_ref_last_message"]  = "str";
    
    $props["_tag_patient"]            = "str";
    $props["_tag_sejour"]             = "str";
    $props["_tag_mediuser"]           = "str";
    $props["_tag_service"]            = "str";
    return $props;
  }

  function updateFormFields() {
    parent::updateFormFields();
        
    $this->_view = $this->libelle ? $this->libelle : $this->nom;
    $this->_type_echange = $this->_class_name;

    $this->_tag_patient  = CPatient::getTagIPP($this->group_id);  
    $this->_tag_sejour   = CSejour::getTagNumDossier($this->group_id);
    $this->_tag_mediuser = CMediusers::getTagMediusers($this->group_id);
    $this->_tag_service  = CService::getTagService($this->group_id);
  }

  function loadRefGroup() {
    return $this->_ref_group = $this->loadFwdRef("group_id", 1);
  }
  
  function loadRefUser() {}
  
  /**
   * Get exchanges sources
   * 
   * @return void
   */
  function loadRefsExchangesSources() {}

  /**
   * Sender is reachable ?
   * 
   * @return boolean reachable
   */
  function isReachable() {
    if (!$this->_ref_exchanges_sources) {
      $this->loadRefsExchangesSources();
    }
  }
  
  function lastMessage() {}
}

?>
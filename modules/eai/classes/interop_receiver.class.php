<?php

/**
 * Interop Receiver EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CInteropReceiver 
 * Interoperability Receiver
 */
class CInteropReceiver extends CMbObject {
  // DB Fields
  var $nom         = null;
  var $libelle     = null;
  var $group_id    = null;
  var $actif       = null;
  var $message     = null;
  
  // Forward references
  var $_ref_group             = null;
  var $_ref_exchanges_sources = null;
  
  // Form fields
  var $_tag_patient  = null;
  var $_tag_sejour   = null;
  var $_tag_mediuser = null;
  var $_tag_service  = null;
  var $_type_echange = null;
  var $_reachable    = null;
  var $_exchanges_sources_save = 0;
  
  function getSpec() {
    $spec = parent::getSpec();

    $spec->messages = array();
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["nom"]           = "str notNull";
    $props["libelle"]       = "str";
    $props["group_id"]      = "ref notNull class|CGroups autocomplete|text";
    $props["actif"]         = "bool notNull";
    $props["message"]       = "str";

    $props["_tag_patient"]            = "str";
    $props["_tag_sejour"]             = "str";
    $props["_tag_mediuser"]           = "str";
    $props["_tag_service"]            = "str";
    $props["_reachable"]              = "bool";
    $props["_exchanges_sources_save"] = "num";
    return $props;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    
    return $backProps;
  }
    
  function loadRefGroup() {
    return $this->_ref_group = $this->loadFwdRef("group_id", 1);
  }
  
  /**
   * Get exchanges sources
   * 
   * @return void
   */
  function loadRefsExchangesSources() {
  }
  
  function updateFormFields() {
    parent::updateFormFields();

    $this->_view = $this->libelle ? $this->libelle : $this->nom;
    $this->_type_echange = $this->_class_name;
  }
  
  /**
   * Get child receivers
   * 
   * @return array CInteropReceiver collection 
   */
  static function getChildReceivers() {    
    return CApp::getChildClasses("CInteropReceiver");
  }
  
  /**
   * Receiver is reachable ?
   * 
   * @return boolean reachable
   */
  function isReachable() {
    if (!$this->_ref_exchanges_sources) {
      $this->loadRefsExchangesSources();
    }
  }
}

?>

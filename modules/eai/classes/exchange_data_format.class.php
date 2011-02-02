<?php

/**
 * Echange Data Format EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CExchangeDataFormat
 * Echange Data Format
 */
CAppUI::requireSystemClass('mbMetaObject');

class CExchangeDataFormat extends CMbMetaObject {
  // DB Fields
  var $group_id                = null;
  var $date_production         = null;
  var $emetteur_id             = null;
  var $destinataire_id         = null;
  var $type                    = null;
  var $date_echange            = null;
  var $message_content_id      = null;
  var $acquittement_content_id = null;
  var $statut_acquittement     = null;
  var $message_valide          = null;
  var $acquittement_valide     = null;
  var $id_permanent            = null;
  var $object_id               = null;
  var $object_class            = null;
  
  // Filter fields
  var $_date_min          = null;
  var $_date_max          = null;
  
  // Form fields
  var $_self_emetteur     = null;
  var $_self_destinataire = null;
  var $_message           = null;
  var $_acquittement      = null;
  
  // Forward references
  var $_ref_group         = null;
  var $_ref_emetteur      = null;
  var $_ref_destinataire  = null;
  
  function getProps() {
    $props = parent::getProps();
    
    $props["date_production"]         = "dateTime notNull";
    $props["group_id"]                = "ref notNull class|CGroups";
    $props["type"]                    = "str";
    $props["date_echange"]            = "dateTime";
    $props["statut_acquittement"]     = "str show|0";
    $props["message_valide"]          = "bool show|0";
    $props["acquittement_valide"]     = "bool show|0";
    $props["id_permanent"]            = "str";
    $props["object_id"]               = "ref class|CMbObject meta|object_class unlink";
    
    $props["_self_emetteur"]          = "bool";
    $props["_self_destinataire"]      = "bool notNull";
    
    $props["_date_min"]               = "dateTime";
    $props["_date_max"]               = "dateTime";
    
    $props["_message"]                = "xml";
    $props["_acquittement"]           = "xml";
    
    return $props;
  }
  
  function loadRefGroups() {
    $this->_ref_group = new CGroups;
    $this->_ref_group->load($this->group_id);
  }
  
  function loadRefsDestinataireInterop() {
    $this->_ref_emetteur     = $this->loadFwdRef("emetteur_id");
    $this->_ref_destinataire = $this->loadFwdRef("destinataire_id");
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    // Chargement des tags
    $this->_tag_patient  = CPatient::getTagIPP($this->group_id);   
    $this->_tag_sejour   = CSejour::getTagNumDossier($this->group_id);
    $this->_tag_mediuser = CMediusers::getTagMediusers($this->group_id);
    $this->_tag_service  = CService::getTagService($this->group_id); 
    
    // Chargement des contents 
    $this->loadContent();
     
    $this->_self_emetteur     = $this->emetteur_id     === null;
    $this->_self_destinataire = $this->destinataire_id === null;
  }
}

?>
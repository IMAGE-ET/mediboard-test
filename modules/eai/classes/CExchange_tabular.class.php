<?php

/**
 * Echange Tabular EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CExchangeTabular
 * Echange Tabular
 */

CAppUI::requireModuleClass("eai", "CExchange_data_format");

class CExchangeTabular extends CExchangeDataFormat {
  // DB Fields
  var $version           = null;
  var $nom_fichier       = null;
  
  function getProps() {
    $props = parent::getProps();
        
    $props["version"]                 = "str";
    $props["nom_fichier"]             = "str";
    $props["message_content_id"]      = "ref class|CContentTabular show|0 cascade";
    $props["acquittement_content_id"] = "ref class|CContentTabular show|0 cascade";   
    
    $props["_message"]                = "str";
    $props["_acquittement"]           = "str";
    
    return $props;
  }
  
  function loadContent() {
    $content = new CContentTabular();
    $content->load($this->message_content_id);
    $this->_message = $content->content;
    
    $content = new CContentTabular();
    $content->load($this->acquittement_content_id);
    $this->_acquittement = $content->content;
  }
  
  function updatePlainFields() {
    if ($this->_message !== null) {
      $content = new CContentTabular();
      $content->load($this->message_content_id);
      $content->content = $this->_message;
      if ($msg = $content->store()) {
        return $msg;
      }
      if (!$this->message_content_id) {
        $this->message_content_id = $content->_id;
      }
    }
    
    if ($this->_acquittement !== null) {
      $content = new CContentTabular();
      $content->load($this->acquittement_content_id);
      $content->content = $this->_acquittement;
      if ($msg = $content->store()) {
        return $msg;
      }
      if (!$this->acquittement_content_id) {
        $this->acquittement_content_id = $content->_id;
      }
    }
  }
  
  function isWellForm() {}
  
  function understand($data, CInteropSender $actor = null) {
    return false;
  }
}

?>
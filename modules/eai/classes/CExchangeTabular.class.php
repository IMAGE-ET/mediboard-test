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

class CExchangeTabular extends CExchangeDataFormat {
  // DB Fields
  public $version;
  public $nom_fichier;
  public $identifiant_emetteur;

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
        
    $props["version"]                 = "str";
    $props["nom_fichier"]             = "str";
    $props["identifiant_emetteur"]    = "str";
    $props["message_content_id"]      = "ref class|CContentTabular show|0 cascade";
    $props["acquittement_content_id"] = "ref class|CContentTabular show|0 cascade";   
    
    $props["_message"]                = "str";
    $props["_acquittement"]           = "str";
    
    return $props;
  }

  /**
   * @see parent::loadContent()
   */
  function loadContent() {
    $this->_ref_message_content = $this->loadFwdRef("message_content_id", true);
    $this->_message = $this->_ref_message_content->content;

    $this->_ref_acquittement_content = $this->loadFwdRef("acquittement_content_id", true);
    $this->_acquittement = $this->_ref_acquittement_content->content;
  }

  /**
   * @see parent::updatePlainFields()
   */
  function updatePlainFields() {
    parent::updatePlainFields();

    if ($this->_message !== null) {
      /** @var CContentTabular $content */
      $content = $this->loadFwdRef("message_content_id", true);
      $content->content = $this->_message;
      if ($msg = $content->store()) {
        return;
      }
      if (!$this->message_content_id) {
        $this->message_content_id = $content->_id;
      }
    }
    
    if ($this->_acquittement !== null) {
      /** @var CContentTabular $content */
      $content = $this->loadFwdRef("acquittement_content_id", true);
      $content->content = $this->_acquittement;
      if ($msg = $content->store()) {
        return;
      }
      if (!$this->acquittement_content_id) {
        $this->acquittement_content_id = $content->_id;
      }
    }
  }

  /**
   * @see parent::isWellFormed()
   */
  function isWellFormed($data) {
  }

  /**
   * @see parent::understand()
   */
  function understand($data, CInteropActor $actor = null) {
  }

  /**
   * @see parent::getMessage()
   */
  function getMessage() {
  }

  /**
   * @see parent::getACK()
   */
  function getACK() {
  }
}


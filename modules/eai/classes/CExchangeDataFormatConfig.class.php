<?php

/**
 * Echange Data Format Config EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CExchangeDataFormatConfig
 * Echange Data Format Config
 */

class CExchangeDataFormatConfig extends CMbObjectConfig { 
  static $config_fields = array();
  
  // DB Fields
  // Sender
  public $sender_id;
  public $sender_class;
  
  // Form fields
  public $_config_fields;

  /**
   * References
   */

  /** @var CInteropSender */
  public $_ref_sender;

  /**
   * @see parent::getProps
   */
  function getProps() {
    $props = parent::getProps();
    
    $props["sender_id"]    = "ref class|CInteropSender meta|sender_class";
    $props["sender_class"] = "enum list|CSenderFTP|CSenderSOAP|CSenderMLLP|CSenderFileSystem show|0";
    
    return $props;
  }

  /**
   * Load interop sender
   *
   * @return CInteropSender
   */
  function loadRefSender(){
    return $this->_ref_sender = $this->loadFwdRef("sender_id", true);
  }

  /**
   * Get config fields
   *
   * @return array
   */
  function getConfigFields() {
    return $this->_config_fields = self::$config_fields;
  }

  /**
   * @see parent::store
   */
  function store() {
    $this->exportXML();

    return parent::store();
  }

  /**
   * Export XML
   *
   * @return CMbXMLDocument
   */
  function exportXML(){
    $doc = new CMbXMLDocument();
    $root = $doc->addElement($doc, $this->_class);
    
    foreach ($this->getConfigFields() as $field) {
      $node = $doc->addElement($root, "entry");
      $node->setAttribute("config", $field);
      $node->setAttribute("value", $this->$field);
    }
    
    return $doc;
  }

  /**
   * Import XML
   *
   * @return void
   */
  function importXML() {
  }
}


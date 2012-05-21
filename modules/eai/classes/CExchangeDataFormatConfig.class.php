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

class CExchangeDataFormatConfig extends CMbObject { 
  static $config_fields = array();
  
  // DB Fields
  // Sender
  var $sender_id      = null;
  var $sender_class   = null;
  
  // Form fields
  var $_config_fields = null;
  
  function getProps() {
    $props = parent::getProps();
    
    $props["sender_id"]    = "ref class|CInteropSender meta|sender_class";
    $props["sender_class"] = "enum list|CSenderFTP|CSenderSOAP|CSenderMLLP|CSenderFileSystem show|0";
    
    return $props;
  }
  
  function getConfigFields() {
    return $this->_config_fields = self::$config_fields;
  }
  
  function store(){
  	$this->exportXML();
  	return parent::store();
  }
  
  function exportXML(){
  	$doc = new CMbXMLDocument();
    $root = $doc->addElement($doc, $this->_class);
    
    foreach($this->getConfigFields() as $field) {
    	$node = $doc->addElement($root, "entry");
      $node->setAttribute("config", $field);
      $node->setAttribute("value", $this->$field);
    }
    
   return $doc;
  }
}

?>
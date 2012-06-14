<?php

/**
 * Echange Any EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CExchangeAny
 * Echange Tabular
 */

class CExchangeAny extends CExchangeDataFormat {
  static $messages = array(
    "None" => "CExchangeAny", 
  );
  
  static $evenements = array();
  
  // DB Table key
  var $echange_any_id     = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    $spec->table = 'echange_any';
    $spec->key   = 'echange_any_id';
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
        
    $props["message_content_id"]      = "ref class|CContentAny show|0 cascade";
    $props["acquittement_content_id"] = "ref class|CContentAny show|0 cascade";   
    
    $props["receiver_id"]             = "ref class|CInteropReceiver";
    $props["object_class"]            = "str class show|0";
    
    $props["_message"]                = "str";
    $props["_acquittement"]           = "str";
    
    return $props;
  }
  
  function loadContent() {
    $content = new CContentAny();
    $content->load($this->message_content_id);
    $this->_message = $content->content;
    
    $content = new CContentAny();
    $content->load($this->acquittement_content_id);
    $this->_acquittement = $content->content;
  }
  
  function guessDataType(){
    $data_types = array(
       "<?xml" => "xml", 
       "MSH|"  => "er7",
    );
    
    foreach ($data_types as $check => $spec) {
      if (strpos($this->_message, $check) === 0) {
        $this->_props["_message"] = $spec;
        $this->_specs["_message"] = CMbFieldSpecFact::getSpec($this, "_message", $spec);
      }
      
      if (strpos($this->_acquittement, $check) === 0) {
        $this->_props["_acquittement"] = $spec;
        $this->_specs["_acquittement"] = CMbFieldSpecFact::getSpec($this, "_acquittement", $spec);
      }
    }
  }
  
  function updatePlainFields() {
    if ($this->_message !== null) {
      $content = new CContentAny();
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
      $content = new CContentAny();
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
}

?>
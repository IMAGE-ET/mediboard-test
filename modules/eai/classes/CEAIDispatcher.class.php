<?php

/**
 * Dispatcher EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CEAIDispatcher
 * Dispatcher EAI
 */

class CEAIDispatcher {
  static $errors      = null;
  static $xml_error   = null;
    
  static function dispatch($data, CInteropSender $actor = null, $exchange_id = null, $to_treatment = true) {
    if ($actor && isset($actor->_configs["encoding"]) && $actor->_configs["encoding"] == "UTF-8") {
      $data = utf8_decode($data);
    }
    
    self::$errors = array();
    // Accepte t-on des utilisateurs acteurs non enregistrés ?
    if (!$actor) {
      CEAIDispatcher::$errors[] = CAppUI::tr("CEAIDispatcher-no_actor");
      return self::dispatchError($data, $actor);
    }
    
    foreach (CExchangeDataFormat::getAll() as $key => $_exchange_class) {  
      $understand = false;
      foreach (CApp::getChildClasses($_exchange_class, array(), true) as $under_key => $_data_format) {
        /**
         * @var CExchangeDataFormat
         */
        $data_format = new $_data_format;
        
        // Test si le message est compris
        $understand = $data_format->understand($data, $actor);    
        if ($understand) {
          break 2;
        }
      }
    }

    if (!$understand) {
      self::$errors[] = CAppUI::tr("CEAIDispatcher-no_understand");
      return self::dispatchError($data, $actor);
    }

    // est-ce que je comprend la famille de messages ?
    $supported = false;
    $family_message_class = (!$data_format->_family_message_class) ? get_class($data_format->_family_message) : $data_format->_family_message_class;    
    foreach ($data_format->getMessagesSupported($actor->_guid, false, null, true) as $_msg_supported_class => $_msg_supported) {
      if ($family_message_class == $_msg_supported_class) {
        $supported = true;
      }
    }

    if (!$supported) {
      self::$errors[] = CAppUI::tr("CEAIDispatcher-_family_message_no_supported_for_this_actor", $family_message_class);
      return self::dispatchError($data, $actor);
    }
    
    CAppUI::stepAjax("CEAIDispatcher-understand");
    
    $actor->_data_format        = $data_format;
    
    $data_format->sender_id     = $actor->_id;
    $data_format->sender_class  = $actor->_class;
    $data_format->group_id      = $actor->group_id;
    $data_format->_ref_sender   = $actor;
    $data_format->_message      = $data;
    $data_format->_exchange_id  = $exchange_id;
    $data_format->_to_treatment = $to_treatment;
        
    // Traitement par le handler du format
    try {
      return $data_format->handle();
    } 
    catch(CMbException $e) {
      self::$errors[] = $e->getMessage();
      return self::dispatchError($data, $actor);
    }
  }
  
  static function dispatchError($data, $actor = null) {
    foreach (self::$errors as $_error) {
      CAppUI::stepAjax($_error, UI_MSG_WARNING);
    }
    
    // Création d'un échange Any
    $exchange_any                  = new CExchangeAny();
    $exchange_any->date_production = mbDateTime();
    $exchange_any->sender_id       = $actor->_id;
    $exchange_any->sender_class    = $actor->_class;
    $exchange_any->group_id        = $actor->group_id ? $actor->group_id : CGroups::loadCurrent()->_id;
    $exchange_any->type            = "None";
    $exchange_any->_message        = $data;
    $exchange_any->store();
    
    // Création d'un message de retour
    $dom = new CMbXMLDocument();
    $mb_errors = $dom->addElement($dom, "MB_Dispatch_Errors");
    foreach (self::$errors as $_error) {
      $dom->addElement($mb_errors, "MB_Dispatch_Error", $_error);
    }
    self::$xml_error = $dom->saveXML();
    
    return false;
  }
  
  /*
   * Create ACK
   */
  static function createFileACK($msg, $sender) {
    if (!$sender->create_ack_file) {
      return;
    }
        
    $source       = reset($sender->_ref_exchanges_sources);
    $filename_ack = "ACK_".$source->_receive_filename;
    
    if ($source instanceof CSourceFTP) {
      $source->setData($msg);
      $source->send(null, $filename_ack);
    }
    elseif ($source instanceof CSourceFileSystem) {
      $source->setData($msg, false, "$filename_ack.$source->fileextension");
      $source->send();
    }
  }
}

?>
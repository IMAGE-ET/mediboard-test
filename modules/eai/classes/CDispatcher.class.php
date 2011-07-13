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
  static $errors    = null;
  static $xml_error = null;
    
  static function dispatch($data, CInteropSender $actor = null) {
    self::$errors = array();
    // Accepte t-on des utilisateurs acteurs non enregistrés ?
    if (!$actor) {
      CEAIDispatcher::$errors[] = CAppUI::tr("CEAIDispatcher-no_actor");
      return self::dispachError($data);
    }
    
    foreach (CExchangeDataFormat::getAll() as $key => $_exchange_class) {  
      $understand = false;
      foreach (CApp::getChildClasses($_exchange_class, array(), true) as $under_key => $_data_format) {
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
      return self::dispachError($data);
    }

    // est-ce que je comprend la famille de messages ?
    $supported = false;
    $family_message_class_name = get_class($data_format->_family_message);
    foreach ($data_format->getMessagesSupported($actor->_guid, false, null, true) as $_msg_supported_class => $_msg_supported) {
      if ($family_message_class_name == $_msg_supported_class) {
        $supported = true;
      }
    }

    if (!$supported) {
      self::$errors[] = CAppUI::tr("CEAIDispatcher-_family_message_no_supported_for_this_actor", $family_message_class_name);
      return self::dispachError($data);
    }
    
    CAppUI::stepAjax("CEAIDispatcher-understand");
    
    $data_format->sender_id     = $actor->_id;
    $data_format->sender_class  = $actor->_class_name;
    $data_format->group_id      = $actor->group_id;
    $data_format->_ref_sender   = $actor;
    $data_format->_message      = $data;

    // Traitement par le handler du format
    try {
      return $data_format->handle();    
    } catch(CMbException $e) {
      self::$errors[] = $e->getMessage();
      return self::dispachError($data);
    }
  }
  
  static function dispachError($data) {
    foreach (self::$errors as $_error) {
      CAppUI::stepAjax($_error, UI_MSG_WARNING);
    }
    
    // Création d'un échange Any
    $exchange_any                  = new CExchangeAny();
    $exchange_any->date_production = mbDateTime();
    $exchange_any->group_id        = CGroups::loadCurrent()->_id;
    $exchange_any->type            = "None";
    $exchange_any->_message        = $data;
    $exchange_any->store();
    
    // Création d'un message de retour
    $dom = new CMbXMLDocument();
    $mb_errors = $dom->addElement($dom, "MB_Dispach_Errors");
    foreach (self::$errors as $_error) {
      $dom->addElement($mb_errors, "MB_Dispach_Error", $_error);
    }
    self::$xml_error = $dom->saveXML();
    
    return false;
  }
}

?>
<?php

/**
 * SOAP Handler EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CEAISoapHandler
 * EAI SOAP Handler
 */

class CEAISoapHandler extends CSoapHandler {
  /**
   * Params specs
   * @var array
   */
  static $paramSpecs = array(
    "event" => array(
      "parameters" => array(
        "message" => "string"
      ),
      "return" => array()
    )
  );
  
  /**
   * Get parameters specifications
   * 
   * @return array
   */
  static function getParamSpecs() {
    return array_merge(parent::getParamSpecs(), self::$paramSpecs);
  }
  
  /**
   * Event method
   * 
   * @param string $message  Message
   * @param int    $actor_id Actor id
   * 
   * @return string ACK
   */ 
  function event($message, $actor_id = null) {
    $actor = null;
    
    $sender_soap = new CSenderSOAP();
    if ($actor_id) {
      $sender_soap->load($actor_id);
    }
    else {
      $sender_soap->user_id = CUser::get()->_id;
      $sender_soap->loadMatchingObject();
    }
    
    if ($sender_soap->_id) {
      $actor = $sender_soap;
    }

    // Dispatch EAI 
    if (!$acq = CEAIDispatcher::dispatch($message, $actor)) {
      return utf8_encode(CEAIDispatcher::$xml_error);
    }

    return $acq;
  }
}

?>
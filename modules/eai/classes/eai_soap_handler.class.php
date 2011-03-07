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

CAppUI::loadClass("CSoapHandler");

class CEAISoapHandler extends CSoapHandler {
  static $paramSpecs = array(
    "event" => array ( 
      "message" => "string")
  );
  
  function event($message) {
    // Dispatch EAI 
    CEAIDispatcher::dispatch($message);
  }
}

CEAISoapHandler::$paramSpecs += CSoapHandler::$paramSpecs;

?>
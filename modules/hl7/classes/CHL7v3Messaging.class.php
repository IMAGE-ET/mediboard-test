<?php

/**
 * Patient Administration - HL7v3
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v3Messaging
 * Patient Administration
 */
class CHL7v3Messaging {

  /** @var array */
  static $object_handlers = array(
  );


  /** @var array */
  static $versions   = array();


  /** @var array */
  static $evenements = array();


  /** @var array */
  var $_categories   = array();

  /**
   * Retrieve handlers list
   *
   * @return array Handlers list
   */
  static function getObjectHandlers() {
    return self::$object_handlers;
  }

  /**
   * Retrieve events list of data format
   *
   * @return array Events list
   */
  function getEvenements() {
    return self::$evenements;
  }

  /**
   * Retrieve transaction name
   *
   * @param string $code Event code
   *
   * @return string Transaction name
   */
  static function getTransaction($code) {
  }

  /**
   * Return data format object
   *
   * @param CExchangeDataFormat $exchange Instance of exchange
   *
   * @throws CMbException
   *
   * @return object An instance of data format
   */
  static function getEvent(CExchangeDataFormat $exchange) {
    switch ($exchange->type) {
      case "PRPA" :
        return CPRPAMessaging::getEvent($exchange);

      default :
        throw new CMbException("CHL7v3Messaging_event-unknown");
        break;
    }
  }
}
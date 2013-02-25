<?php

/**
 * IHE Classes
 *  
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CIHE 
 * IHE classes
 */
class CIHE {
  /**
   * @var array
   */
  static $object_handlers = array(
    "CSipObjectHandler"     => "CITI30DelegatedHandler",
    "CSmpObjectHandler"     => "CITI31DelegatedHandler",
    "CSaEventObjectHandler" => "CRAD48DelegatedHandler"
  );
  /**
   * @var array
   */
  static $versions   = array();
  /**
   * @var array
   */
  static $evenements = array();
  /**
   * @var array
   */
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
      case "PAM" :
        return CPAM::getEvent($exchange);
      case "PAM_FR" :
        return CPAM::getEvent($exchange);
      case "DEC" :
        return CDEC::getEvent($exchange);
      case "SWF" :
        return CSWF::getEvent($exchange);
      case "PDQ" :
        return CPDQ::getEvent($exchange);
      default :
        throw new CMbException("CIHE_event-unknown");
        break;
    }
  }
  
  /**
   * Return Patient Administration Management (PAM) transaction
   * 
   * @param string $code Event code
   * @param string $i18n Internationalization
   * 
   * @return object An instance of PAM transaction
   */
  static function getPAMTransaction($code, $i18n = null) {
    switch ($i18n) {
      case "FR" :
        return CPAMFR::getTransaction($code);
        break;
      default :
        return CPAM::getTransaction($code);
        break;
    }
  }
  
  /**
   * Return Device Enterprise Communication (DEC) transaction
   * 
   * @param string $code Event code
   * @param string $i18n Internationalization
   * 
   * @return object An instance of DEC transaction 
   */
  static function getDECTransaction($code, $i18n = null) {
    return CDEC::getTransaction($code);
  }
  
  /**
   * Return Scheduled Workflow (SWF) transaction
   * 
   * @param string $code Event code
   * @param string $i18n Internationalization
   * 
   * @return object An instance of DEC transaction 
   */
  static function getSWFTransaction($code, $i18n = null) {
    return CSWF::getTransaction($code);
  }

  /**
   * Return Patient Demographics Query (PDQ) transaction
   *
   * @param string $code Event code
   * @param string $i18n Internationalization
   *
   * @return object An instance of PDQ transaction
   */
  static function getPDQTransaction($code, $i18n = null) {
    return CPDQ::getTransaction($code);
  }
}
<?php

/**
 * Interop Norme EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CInteropNorm
 * Interoperability Norme
 */
abstract class CInteropNorm {
  /** @var array*/
  static $object_handlers = array();

  /** @var array  */
  static $versions = array ();

  /** @var array */
  static $evenements = array();

  /** @var array */
  public $_categories = array();

  /** @var string */
  public $domain;

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
   * Retrieve document elements
   *
   * @return array
   */
  function getDocumentElements() {
    return array();
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
  }

  /**
   * get tag
   *
   * @param String $group_id group id
   *
   * @return mixed|null
   */
  static function getTag($group_id = null) {
  }
}
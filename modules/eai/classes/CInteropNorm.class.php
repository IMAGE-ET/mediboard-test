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
  /** @var string */
  public $name;

  /** @var string */
  public $domain;

  /** @var string */
  public $type;

  /** @var array*/
  static $object_handlers = array();

  /** @var array  */
  static $versions = array ();

  /** @var array */
  static $evenements = array();

  /** @var array */
  public $_categories = array();

  /**
   * Construct
   *
   * @return CInteropNorm
   */
  function __construct() {
  }

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
   * Retrieve versions list of data format
   *
   * @return array Versions list
   */
  function getVersions() {
    return self::$versions;
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
   * Get tag
   *
   * @param String $group_id group id
   *
   * @return mixed|null
   */
  static function getTag($group_id = null) {
  }

  /**
   * Retrieve profil
   *
   * @return string
   */
  function getDomain() {
    return $this->domain ? $this->domain : "none";
  }

  /**
   * Retrieve type
   *
   * @return string
   */
  function getType() {
    return $this->type ? $this->type : "none";
  }

  /**
   * Retrieve events
   *
   * @return array Events list
   */
  function getEvents() {
    $events = $this->getEvenements();

    $temp = array();
    foreach ($this->_categories as $_transaction => $_events) {
      foreach ($_events as $_event_name) {
        if (array_key_exists($_event_name, $events)) {
          $temp[$_transaction][$_event_name] = $events[$_event_name];
        }
      }
    }

    if (empty($temp)) {
      $temp["none"] = $events;
    }

    return $temp;
  }

  /**
   * Get objects
   *
   * @return array CInteropNorm collection
   */
  static function getObjects() {
    $standards = array();
    foreach (CApp::getChildClasses("CInteropNorm", false) as $_interop_norm) {
      /** @var CInteropNorm $norm */
      $norm = new $_interop_norm;

      if (!$norm->name || !$norm->type) {
        continue;
      }

      $domain_name = $norm->getDomain();
      $type        = $norm->getType();
      $events      = $norm->getEvents();

      $standards[$norm->name][$domain_name][$type] = $events;
    }

    return $standards;
  }
}
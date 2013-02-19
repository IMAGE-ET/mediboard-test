<?php

/**
 * Patient Demographics Query - IHE
 *
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CPDQ
 * Patient Demographics Query
 */
class CPDQ extends CIHE {
  /**
   * @var array
   */
  static $versions = array (
    "2.1", "2.2", "2.3", "2.4", "2.5"
  );

  /**
   * @var array
   */
  static $transaction_iti21 = array(
    "Q22", "J01"
  );

  /**
   * @var array
   */
  static $transaction_iti22 = array(
    "ZV1"
  );

  /**
   * @var array
   */
  static $evenements = array(
    // ITI-21
    "Q22" => "CHL7EventQBPQ22",
    "J01" => "CHL7EventQCNJ01",

    // ITI-22
    "ZV1" => "CHL7EventQBPZV1",
  );

  /**
   * Construct
   *
   * @return CPDQ
   */
  function __construct() {
    $this->type = "PDQ";
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
   * Retrieve transaction name,
   *
   * @param string $code Event code
   *
   * @return string Transaction name
   */
  static function getTransaction($code) {
    if (in_array($code, self::$transaction_iti21)) {
      return "ITI21";
    }

    if (in_array($code, self::$transaction_iti22)) {
      return "ITI22";
    }
  }

  /**
   * Return data format object
   *
   * @param CExchangeDataFormat $exchange Instance of exchange
   *
   * @return object An instance of data format
   */
  static function getEvent(CExchangeDataFormat $exchange) {
    $code    = $exchange->code;
    $version = $exchange->version;

    foreach (CHL7::$versions as $_version => $_sub_versions) {
      if (in_array($version, $_sub_versions)) {
        if ($code == "Q22" || $code == "ZV1") {
          $classname = "CHL7{$_version}EventQBP$code";
        }
        if ($code == "J01") {
          $classname = "CHL7{$_version}EventQCN$code";
        }

        return new $classname;
      }
    }
  }
}
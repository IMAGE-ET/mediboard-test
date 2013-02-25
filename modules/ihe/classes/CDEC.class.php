<?php

/**
 * Device Enterprise Communication IHE
 *  
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CDEC 
 * Device Enterprise Communication
 */
class CDEC extends CIHE {
  /**
   * @var array
   */
  static $versions = array (
    "2.1", "2.2", "2.3", "2.4", "2.5", "2.6"
  );

  /**
   * @var array
   */
  static $transaction_pcdO1 = array(
    "R01"
  );

  /**
   * @var array
   */
  static $evenements = array(
    // PDC-01
    "R01" => "CHL7EventORUR01",
  );

  /**
   * Construct
   *
   * @return \CDEC
   */
  function __construct() {
    $this->type = "DEC";

    $this->_categories = array(
      "PDC-01" => self::$transaction_pcdO1
    );
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
    if (in_array($code, self::$transaction_pcdO1)) {
      return "PCD01";
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
        $classname = "CHL7{$_version}EventORU$code";
        return new $classname;
      }
    }
  }
}
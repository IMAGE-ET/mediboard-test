<?php

/**
 * Scheduled Workflow IHE
 *  
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CSWF 
 * Scheduled Workflow
 */
class CSWF extends CIHE {
  /**
   * @var array
   */
  static $versions = array (
    "2.3", "2.4", "2.5"  
  );

  /**
   * @var array
   */
  static $transaction_rad48 = array(
    "S12", "S13", "S14", "S15" 
  );

  /**
   * @var array
   */
  static $evenements = array(
    // SIU
    "S12" => "CHL7EventSIUS12",
    "S13" => "CHL7EventSIUS13",
    "S14" => "CHL7EventSIUS14",
    "S15" => "CHL7EventSIUS15",
  );
  
  function __construct() {
    $this->type = "SWF";
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
    if (in_array($code, self::$transaction_rad48)) {
      return "RAD48";
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
        $classname = "CHL7{$_version}EventSIU$code";
        return new $classname;
      }
    }
  }
}

?>
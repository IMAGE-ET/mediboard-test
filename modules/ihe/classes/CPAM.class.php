<?php

/**
 * Patient Administration Management IHE
 *  
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CPAM 
 * Patient Administration Management
 */
class CPAM extends CIHE {
  static $versions = array (
    "2.1", "2.2", "2.3", "2.4", "2.5"  
  );
  
  static $transaction_iti30 = array(
    "A28", "A31", "A40", "A47"
  );
  
  static $transaction_iti31 = array(
    "A01", "A02", "A03", "A04", "A05", "A06", "A07",
    "A08", "A11", "A12", "A13", "A14", "A16", "A25", 
    "A38", "A44", "A54", "A55", 
  );
  
  static $evenements = array(
    // ITI-30
    "A28" => "CHL7EventADTA28",
    "A31" => "CHL7EventADTA31",
    "A40" => "CHL7EventADTA40",
    "A47" => "CHL7EventADTA47",
    
    // ITI-31
    "A01" => "CHL7EventADTA01",
    "A02" => "CHL7EventADTA02",
    "A03" => "CHL7EventADTA03",
    "A04" => "CHL7EventADTA04",
    "A05" => "CHL7EventADTA05",
    "A06" => "CHL7EventADTA06",
    "A07" => "CHL7EventADTA07",
    "A08" => "CHL7EventADTA08",
    "A11" => "CHL7EventADTA11",
    "A12" => "CHL7EventADTA12",
    "A13" => "CHL7EventADTA13",
    "A14" => "CHL7EventADTA14",
    "A16" => "CHL7EventADTA16",
    "A25" => "CHL7EventADTA25",
    "A38" => "CHL7EventADTA38",
    "A44" => "CHL7EventADTA44",
    "A54" => "CHL7EventADTA54",
    "A55" => "CHL7EventADTA55",
  );

  function __construct() {
    $this->type = "PAM";
  }
  
  /**
   * Retrieve events list of data format
   * @return array Events list
   */
  function getEvenements() {
    return self::$evenements;
  }
  
  /**
   * Retrieve transaction name
   * @param $code Event code
   * @return string Transaction name
   */
  static function getTransaction($code) {
    if (in_array($code, self::$transaction_iti30)) {
      return "ITI30";
    }
    
    if (in_array($code, self::$transaction_iti31)) {
      return "ITI31";
    }
  }
  
  /**
   * Return data format object
   * @param exchange Instance of exchange
   * @return object An instance of data format
   */
  static function getEvent(CExchangeDataFormat $exchange) {
    $code    = $exchange->code;
    $version = $exchange->version;
    
    foreach (CHL7::$versions as $_version => $_sub_versions) {      
      if (in_array($version, $_sub_versions)) {
        $classname = "CHL7{$_version}EventADT$code";
        return new $classname;
      }
    }
  }
}

?>
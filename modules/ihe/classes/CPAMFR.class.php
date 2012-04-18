<?php

/**
 * Patient Administration Management - National extension France IHE
 *  
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CPAMFR 
 * Patient Administration Management - National extension France
 */
class CPAMFR extends CPAM {
  static $transaction_iti30 = array(
    "A28", "A31", "A40", "A47"
  );
  
  static $transaction_iti31 = array(
    "A01", "A02", "A03", "A04", "A05", "A06", "A07",
    "A11", "A12", "A13", "A14", "A16", "A25", "A38", 
    "A44", "A54", "A55", "Z80", "Z81", "Z84", "Z85",
    "Z99"
  );
  
  static $evenements = array(
    // ITI-30
    "A28" => "CHL7EventADTA28_FR",
    "A31" => "CHL7EventADTA31_FR",
    "A40" => "CHL7EventADTA40_FR",
    "A47" => "CHL7EventADTA47_FR",
    
    // ITI-31
    "A01" => "CHL7EventADTA01_FR",
    "A02" => "CHL7EventADTA02_FR",
    "A03" => "CHL7EventADTA03_FR",
    "A04" => "CHL7EventADTA04_FR",
    "A05" => "CHL7EventADTA05_FR",
    "A06" => "CHL7EventADTA06_FR",
    "A07" => "CHL7EventADTA07_FR",
    "A11" => "CHL7EventADTA11_FR",
    "A12" => "CHL7EventADTA12_FR",
    "A13" => "CHL7EventADTA13_FR",
    "A14" => "CHL7EventADTA14_FR",
    "A16" => "CHL7EventADTA16_FR",
    "A25" => "CHL7EventADTA25_FR",
    "A38" => "CHL7EventADTA38_FR",
    "A44" => "CHL7EventADTA44_FR",
    "A54" => "CHL7EventADTA54_FR",
    "A55" => "CHL7EventADTA55_FR",
    "Z80" => "CHL7EventADTZ80_FR",
    "Z81" => "CHL7EventADTZ81_FR",
    "Z84" => "CHL7EventADTZ84_FR",
    "Z85" => "CHL7EventADTZ85_FR",
    "Z99" => "CHL7EventADTZ99_FR",
  );
  
  function __construct() {
    $this->type = "PAM_FR";
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
        $classname = "CHL7{$_version}EventADT{$code}_FR";
        return new $classname;
      }
    }
  }
}

?>
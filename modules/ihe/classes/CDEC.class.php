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
 * PDevice Enterprise Communication
 */
class CDEC {
  static $versions = array (
    "2.1", "2.2", "2.3", "2.4", "2.5"  
  );
  
  static $transaction_pcdO1 = array(
    "R01"
  );
  
  static $evenements = array(
    // PDC-01
    "R01" => "CHL7EventORUR01",
  );
  
  function getEvenements() {
    return self::$evenements;
  }
  
  function __construct() {
    $this->type = "DEC";
  }
  
  static function getDECEvent($code, $version) {
    foreach (CHL7::$versions as $_version => $_sub_versions) {      
      if (in_array($version, $_sub_versions)) {
        $classname = "CHL7{$_version}EventORU$code";
        return new $classname;
      }
    }
  }
}

?>
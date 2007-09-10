<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: $
* @author Thomas Despoix
*/

class CInsee {
  // DB Table key
  static $ds = null;
  
  function connect() {
    if (!self::$ds) {
      self::$ds = CSQLDataSource::get("INSEE");
    }
  }
  
  /**
   * Informations sur un pays
   * @param string $pays nom du pays
   * @return string
   */
  function getPaysInfo($pays, $info) {
    self::connect();
    $query = "SELECT `$info` FROM pays" .
      "\nWHERE nom_fr = %";
    
    return self::$ds->loadResult(self::$ds->prepare($query, $pays));
  }
  
  /**
   * Code à 2 lettres d'un pays
   * @param string $pays nom du pays
   * @return string
   */
  function getPaysAlpha2($pays) {
    return self::getPaysInfo($pays, "alpha_2");
  }
}
?>
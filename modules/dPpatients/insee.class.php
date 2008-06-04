<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: $
* @author Thomas Despoix
*/

class CPaysInsee extends CMbObject {
  // DB Fields
  var $numerique = null;
  var $alpha_2 = null;
  var $alpha_3 = null;
  var $nom_fr = null;
  var $nom_ISO = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->dsn         = 'INSEE';
    $spec->incremented = false;
    $spec->table       = 'pays';
    $spec->key         = 'numerique';
    return $spec;
  }
  
  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["numerique"] = "numchar length|3";
    $specs["alpha_2"   ] = "str length|2";
    $specs["alpha_3"   ] = "str length|3";
    $specs["nom_fr"   ] = "str";
    $specs["nom_ISO"  ] = "str";
    return $specs;
  }
  
  /**
   * Informations sur un pays
   * @param string $nom nom du pays
   * @return string
   */
  function getPaysInfo($nom, $info) {
    self::connect();
    $query = "SELECT `$info` FROM pays" .
      "\nWHERE nom_fr = %";
    
    return self::$ds->loadResult(self::$ds->prepare($query, $nom));
  }
  
  /**
   * Trouve le code à 2 lettres d'un pays
   * @param string $pays nom du pays
   * @return string
   */
  function getPaysAlpha2ForNom($nom) {
    return self::getPaysInfo($nom, "alpha_2");
  }

  /**
   * Trouve le nom du pays pour un code à 2 lettres
   * @param string $pays nom du pays
   * @return string
   */
  function getPaysNomForAlpha2($pays) {
    return self::getPaysInfo($pays, "alpha_2");
  }
}
?>
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
    $specs["alpha_2"  ] = "str length|2";
    $specs["alpha_3"  ] = "str length|3";
    $specs["nom_fr"   ] = "str";
    $specs["nom_ISO"  ] = "str";
    return $specs;
  }
}
?>
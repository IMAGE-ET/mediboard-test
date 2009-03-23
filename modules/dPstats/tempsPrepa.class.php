<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPStats
 *	@version $Revision: $
 *  @author Sébastien Fillonneau
 */

/**
 * The CTempsPrepa class
 */
class CTempsPrepa extends CMbObject {
  // DB Table key
  var $temps_prepa_id = null;
  
  // DB Fields
  var $chir_id     = null;
  var $nb_prepa    = null;
  var $nb_plages   = null;
  var $duree_moy   = null;
  var $duree_ecart = null;
  
  // Object References
  var $_ref_praticien = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'temps_prepa';
    $spec->key   = 'temps_prepa_id';
    return $spec;
  }

  function getProps() {
  	$specs = parent::getProps();
    $specs["chir_id"]     = "ref class|CMediusers";
    $specs["nb_plages"]   = "num pos";
    $specs["nb_prepa"]    = "num pos";
    $specs["duree_moy"]   = "time";
    $specs["duree_ecart"] = "time";
    return $specs;
  }
  
  function loadRefsFwd(){ 
    $this->_ref_praticien = new CMediusers;
    $this->_ref_praticien->load($this->chir_id);
  }
}
?>
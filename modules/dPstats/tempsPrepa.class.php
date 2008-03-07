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

  
  function CTempsPrepa() {
    $this->CMbObject("temps_prepa", "temps_prepa_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "chir_id"        => "ref class|CMediusers",
      "nb_plages"      => "num pos",
      "nb_prepa"       => "num pos",
      "duree_moy"      => "time",
      "duree_ecart"    => "time"
    );
    return array_merge($specsParent, $specs);
  }  	
  
  function loadRefsFwd(){ 
    $this->_ref_praticien = new CMediusers;
    $this->_ref_praticien->load($this->chir_id);

  }
}
?>
<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
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
    
    $this->_props["temps_prepa_id"] = "ref";
    $this->_props["chir_id"]        = "ref";
    $this->_props["nb_plage"]       = "num|pos";
    $this->_props["nb_prepa"]       = "num|pos";
    $this->_props["duree_moy"]      = "time";
    $this->_props["duree_ecart"]    = "time";
  }	  	
  
  function loadRefsFwd(){ 
    $this->_ref_praticien = new CMediusers;
    $this->_ref_praticien->load($this->chir_id);

  }
}
?>
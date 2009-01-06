<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision: $
 *  @author Alexis Granger
 */

/**
 * The CFicheATC class
 */
class CFicheATC extends CMbObject {
  // DB Field
  var $fiche_ATC_id = null;
  
  var $code_ATC = null;
  var $libelle = null;
  var $description = null;
  
  var $_libelle_ATC = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'fiche_ATC';
    $spec->key   = 'fiche_ATC_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["code_ATC"]         = "notNull str length|3";
    $specs["libelle"]          = "str";
    $specs["description"]      = "html";
    return $specs;
  }

  function updateFormFields(){
  	parent::updateFormFields();
    $this->_view = "Fiche ATC: $this->code_ATC";
  }
  
  function getLibelleATC(){
    $classeATC = new CBcbClasseATC();
    $this->_libelle_ATC = $classeATC->getLibelle($this->code_ATC);
  }
}

?>
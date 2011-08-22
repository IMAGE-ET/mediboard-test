<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision$
* @author Romain Ollivier
*/

/**
 * The CGestionCab Class
 */
class CGestionCab extends CMbObject {
  // DB Table key
  var $gestioncab_id = null;

  // DB Fields
  var $function_id      = null;
  var $libelle          = null;
  var $date             = null;
  var $rubrique_id      = null;
  var $montant          = null;
  var $mode_paiement_id = null;
  var $num_facture      = null;
  var $rques            = null;
  
  //Filter Fields
  var $_date_min = null;
  var $_date_max = null;
  
  // Object References
  var $_ref_function      = null;
  var $_ref_rubrique      = null;
  var $_ref_mode_paiement = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'gestioncab';
    $spec->key   = 'gestioncab_id';
    return $spec;
  }

  function getProps() {
  	$specs = parent::getProps();
    $specs["function_id"]      = "ref notNull class|CFunctions";
    $specs["libelle"]          = "str notNull seekable";
    $specs["date"]             = "date notNull";
    $specs["rubrique_id"]      = "ref notNull class|CRubrique";
    $specs["montant"]          = "currency notNull min|0";
    $specs["mode_paiement_id"] = "ref notNull class|CModePaiement";
    $specs["num_facture"]      = "num notNull";
    $specs["rques"]            = "text";
    $specs["_date_min"] 		   = "date";
    $specs["_date_max"] 		   = "date moreThan|_date_min";
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "Fiche '".$this->libelle."'";
  }

  // Forward references
  function loadRefsFwd() {
    // fonction (cabinet)
    $this->_ref_function = new CFunctions();
    $this->_ref_function->load($this->function_id);

    // rubrique
    $this->_ref_rubrique = new CRubrique();
    $this->_ref_rubrique->load($this->rubrique_id);

    // mode de paiement
    $this->_ref_mode_paiement = new CModePaiement();
    $this->_ref_mode_paiement->load($this->mode_paiement_id);
  }
  
  function getPerm($permType) {
    if(!$this->_ref_function) {
      $this->loadRefsFwd();
    }
    return ($this->_ref_function->getPerm($permType));
  }
}

?>
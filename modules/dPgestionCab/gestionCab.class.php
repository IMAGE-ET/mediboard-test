<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision$
* @author Romain Ollivier
*/

require_once($AppUI->getSystemClass("mbobject"));

require_once($AppUI->getModuleClass("dPgestionCab", "modePaiement") );
require_once($AppUI->getModuleClass("dPgestionCab", "rubrique") );
require_once($AppUI->getModuleClass("mediusers"   , "functions") );

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

  // Object References
  var $_ref_function      = null;
  var $_ref_rubrique      = null;
  var $_ref_mode_paiement = null;

  function CGestionCab() {
    $this->CMbObject("gestioncab", "gestioncab_id");
    
    $this->_props["function_id"]      = "ref|notNull";
    $this->_props["libelle"]          = "str|notNull";
    $this->_props["date"]             = "date|notNull";
    $this->_props["rubrique_id"]      = "ref|notNull";
    $this->_props["montant"]          = "currency|min|0|notNull";
    $this->_props["mode_paiement_id"] = "ref|notNull";
    $this->_props["num_facture"]      = "num|notNull";
    $this->_props["rques"]            = "text";
    
    $this->_seek["libelle"] = "like";

    $this->buildEnums();
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
}

?>
<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
 *	@version $Revision: $
 *  @author Sébastien Fillonneau
 */

require_once( $AppUI->getSystemClass('mbobject'));

/**
 * The CReference class
 */
class CReference extends CMbObject {
  // DB Table key
  var $reference_id = null;
  
  // DB Fields
  var $materiel_id = null;
  var $fournisseur_id = null;
  var $quantite = null;
  var $prix = null;
  
  //
  var $_prix_unitaire = null;
  
  function CReference() {
    $this->CMbObject( 'fournisseur_ref', 'reference_id' );
  }	  	
  
  function updateFormFields() {
    $this->_prix_unitaire = $this->prix / $this->quantite;
  }
}
?>
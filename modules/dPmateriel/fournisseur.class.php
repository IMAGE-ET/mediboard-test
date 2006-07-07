<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
 *	@version $Revision: $
 *  @author Sébastien Fillonneau
 */

require_once( $AppUI->getSystemClass('mbobject'));

/**
 * The CFournisseur class
 */
class CFournisseur extends CMbObject {
  // DB Table key
  var $fournisseur_id = null;
  
  // DB Fields
  var $societe = null;
  var $adresse = null;
  var $adresse_suite = null;
  var $code_postal = null;
  var $ville = null;
  var $telephone = null;
  var $mail = null;
  
  function CFournisseur() {
    $this->CMbObject( 'fournisseur', 'fournisseur_id' );
  }	  	
}
?>
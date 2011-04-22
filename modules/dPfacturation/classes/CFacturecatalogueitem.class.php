<?php
/**
 * @package Mediboard
 * @subpackage dPfacturation
 * @version $Revision: 8133 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CFacturecatalogueitem extends CMbObject {
	
  // DB Table key
  var $facturecatalogueitem_id = null;
	
  // DB Fields
  var $libelle  		= null;
  var $prix_ht			= null;
  var $taxe					= null;
  var $type					= null;
  
  var $_ttc 				= null;

  
  
  //les deux fonctions getSpec et getProps vont servir à générer la table SQL representative de la classe
  
  //configure la classe (ou table SQL)
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'facturecatalogueitem';
    $spec->key   = 'facturecatalogueitem_id';
    return $spec;
  }
  
  //configure les champs de la classe (ou table SQL)
	function getProps() {
  	$specs = parent::getProps();
    $specs["libelle"]    = "text notNull";
    $specs["prix_ht"]    = "currency notNull";
    $specs["taxe"]       = "pct notNull";
    $specs["type"]			 = "enum list|produit|service";
    $specs["_ttc"]			 = "currency";
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->libelle;
    $this->_ttc += $this->prix_ht * ($this->taxe/100) + $this->prix_ht;
  }
	
}

?>
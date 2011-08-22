<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPfacturation
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CFactureItem extends CMbObject {
  // DB Table key
  var $factureitem_id = null;
  
  // DB Fields
  var $facture_id = null;
  var $libelle = null;
  var $prix_ht = null;
  var $taxe = null;
  var $facture_catalogue_item_id = null;
  var $reduction 		= null;
  
  // References
  var $_ref_facture = null;
  var $_ref_facture_catalogue_item = null;//
   
  var $_ttc = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'factureitem';
    $spec->key   = 'factureitem_id';
    return $spec;
  }

  function getProps() {
  	$specs = parent::getProps();
    $specs["facture_id"] = "ref notNull class|CFacture";
    $specs["libelle"]    = "text notNull";
    $specs["prix_ht"]    = "currency notNull";
    $specs["reduction"]	 = "currency";
    $specs["taxe"]       = "pct notNull";
    $specs["_ttc"]		   = "currency";
    $specs["facture_catalogue_item_id"]	= "ref class|CFacturecatalogueitem";
    return $specs;
  }
    
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->libelle;
    $prixReduit = $this->prix_ht - $this->reduction;
    $this->_ttc += $prixReduit * ($this->taxe/100) + $prixReduit;
  }
  
  function loadRefsFwd(){ 
  	$this->_ref_facture = new CFacture;
  	$this->_ref_facture->load($this->facture_id);
  }
}
?>
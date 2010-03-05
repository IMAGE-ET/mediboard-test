<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

require_once("bcbObject.class.php");

class CBcbEquivalent extends CBcbObject {
  
  // Object references
  var $_ref_produit = null;
  
  // Constructeur
  function CBcbEquivalent(){
    $this->distClass = "BCBEquivalents";
    parent::__construct();
  }

  // Fonction qui retourne les equivalent d'un produit
  function searchEquivalents($search){
    $this->distObj->Search($search);
    $equivalents = array();
    // Chargement des produits equivalents
    foreach($this->distObj->gTabEqui as $key => $equivalent){
      $produit = new CBcbProduit();
      $produit->load($equivalent->Code_CIP); 
      $equivalents[$equivalent->Code_CIP] = $produit;
    }
    return $equivalents;
  }
}

?>
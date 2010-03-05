<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

require_once("bcbObject.class.php");

class CBcbComposition extends CBcbObject {

  // Spéciale composition
  var $exprime_par      = null;
  var $real_exprime_par = null;
  var $excipients       = null;
  var $principes_actifs = null;

  
  // Object reference
  var $_ref_composants = null;
  var $_ref_produits = null;
  
  // Constructeur
  function CBcbComposition(){
    $this->distClass = "BCBComposition";
    parent::__construct();
  }
 
  // Chargement d'une composition a partir d'un code CIP
  function load($CIP){
    $this->distObj->Search($CIP);
    $this->exprime_par = $this->distObj->DataCompo->ExprimePar;
    $this->real_exprime_par = $this->distObj->DataCompo->RealExprimePar;
    $this->excipients = $this->distObj->DataCompo->Excipients;
    $this->principes_actifs = $this->distObj->DataCompo->PA;
  }
  
  
  function searchComposant($search){
    $this->distObj->SearchComposant($search, 1);
	  $this->_ref_composants = $this->distObj->TabComposant;
  }
  
  
  // livretTherapeutique = 1 permet une recherche dans le livret therapeutique
  function searchProduits($composant_id, $livretTherapeutique = null){
    if($livretTherapeutique){
      global $g;
      $this->distObj->LivretTherapeutique = $g;
    }
    $this->distObj->Produits($composant_id);
    $this->_ref_produits = $this->distObj->TabProduit;
  }
}

?>
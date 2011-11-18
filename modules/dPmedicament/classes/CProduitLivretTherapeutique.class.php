<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/*
 * Classe permettant de definir ou de redefinir les elements indispensables a la prescripion pour un produit
 */
class CProduitLivretTherapeutique extends CMbObject {
  // DB Table key
  var $produit_livret_id = null;
  var $group_id          = null;
  var $code_cip          = null;
	var $code_ucd          = null;
  var $code_cis          = null;
  
  var $prix_hopital      = null;
  var $prix_ville        = null;
  var $date_prix_hopital = null;
  var $date_prix_ville   = null;
  var $code_interne      = null;
  var $commentaire       = null;
  var $unite_prise       = null;
  
	var $_ref_produit = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'produit_livret_therapeutique';
    $spec->key   = 'produit_livret_id';
    $spec->uniques["code_cip"] = array("code_cip");
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["group_id"] = "ref notNull class|CGroups";
    $specs["code_cip"] = "numchar notNull length|7";
		$specs["code_ucd"] = "numchar length|7";
    $specs["code_cis"] = "numchar length|8";
    $specs["prix_hopital"] = "float";
    $specs["prix_ville"] = "float";
    $specs["date_prix_hopital"] = "date";
    $specs["date_prix_ville"] = "date";
    $specs["code_interne"] = "str";
    $specs["commentaire"] = "text";
    $specs["unite_prise"] = "str";
    return $specs;
	}
	
  function loadRefProduit(){
    $this->_ref_produit = CBcbProduit::get($this->code_cip);
  }
}
  
?>
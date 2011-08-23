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
class CProduitPrescription extends CMbObject {
  // DB Table key
  var $produit_prescription_id = null;
  var $code_cip                = null;
  var $code_ucd                = null;
  var $code_cis                = null;
  var $libelle                 = null;
  var $quantite                = null;
  var $unite_prise             = null;
  var $unite_dispensation      = null;
  var $nb_presentation         = null;
  var $voie                    = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'produit_prescription';
    $spec->key   = 'produit_prescription_id';
    $spec->uniques["libelle"] = array("libelle");
    $spec->uniques["code_cip"] = array("code_cip");
    $spec->uniques["code_ucd"] = array("code_ucd");
    $spec->uniques["code_cis"] = array("code_cis");
    $spec->xor["code"] = array("code_ucd", "code_cis", "code_cip");
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["code_cip"] = "numchar length|7";
    $specs["code_ucd"] = "numchar length|7";
    $specs["code_cis"] = "numchar length|8";
    $specs["libelle"] = "str notNull";
    $specs["quantite"] = "float notNull";
    $specs["unite_prise"] = "str notNull";
    $specs["unite_dispensation"] = "str notNull";
    $specs["nb_presentation"] = "num notNull";
    $specs["voie"] = "str";
    return $specs;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->libelle;
  }
	
	function store(){
		if($msg = parent::store()){
			return $msg;
		}
		$this->synchroStock();	
	}
	
	function synchroStock(){
		if(CModule::getActive("dPstock") && $this->code_cip){
			$product = new CProduct();
			$product->code = $this->code_cip;
			$product->loadMatchingObject();
			if($product->_id){
				// Synchro avec le produit de stock
				$product->quantity = $this->nb_presentation;
				$product->unit_quantity = $this->quantite;
				$product->unit_title = $this->unite_prise;
				$product->item_title = $this->unite_dispensation;
				if($msg = $product->store()){
					return $msg;
				}
			}
		}
	}
}
  
?>
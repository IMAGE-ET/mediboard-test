<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision: $
 * @author Alexis Granger
 */


class CActe extends CMbMetaObject {
  
  // DB fields
  var $montant_depassement = null;
  var $montant_base        = null;

  // Form fields
  var $_preserve_montant   = null; 
  var $_montant_facture    = null;
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_montant_facture = $this->montant_base + $this->montant_depassement;
  }
  
  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["object_class"]        = "notNull enum list|COperation|CSejour|CConsultation";
    $specs["montant_depassement"] = "currency";
    $specs["montant_base"]        = "currency";
    $specs["_montant_facture"]    = "currency";
    return $specs;
  }
  
  function checkCoded(){
    $object = new $this->object_class;
    $object->load($this->object_id);
    if($object->_coded == "1") {
      return "$object->_class_name déjà validée : Impossible de coter l\'acte";
    }    
  }

  function updateMontant(){
    if(!$this->_preserve_montant){
      $object = new $this->object_class;
      $object->load($this->object_id);
      // Permet de mettre a jour le montant dans le cas d'un consultation
      $object->doUpdateMontants();
    }
  }
  
  function store(){
    parent::store();
    $this->updateMontant();
  }
  
  function delete(){
    parent::delete();
    $this->updateMontant();
  }
}



?>
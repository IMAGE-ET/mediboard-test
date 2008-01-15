<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision$
 * @author Alexis Granger
 */


class CActe extends CMbMetaObject {
  
  // DB fields
  var $montant_depassement = null;
  var $montant_base        = null;

  // Form fields
  var $_preserve_montant   = null; 
  var $_montant_facture    = null;
  
  // Distant object
  var $_ref_sejour = null;
  var $_ref_patient = null;
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_montant_facture = $this->montant_base + $this->montant_depassement;
  }
  
  function loadRefSejour() {
    $this->loadTargetObject();
    $this->_ref_object->loadRefSejour();
    $this->_ref_sejour =& $this->_ref_object->_ref_sejour;
  }
  
  function loadRefPatient() {
    $this->loadTargetObject();
    $this->_ref_object->loadRefPatient();
    $this->_ref_patient =& $this->_ref_sejour->_ref_patient;
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
      return $object->doUpdateMontants();
    }
  }
  
  function store(){
    if ($msg = parent::store()){
      return $msg;
    }
    return $this->updateMontant();
  }
  
  function delete(){
    if ($msg = parent::delete()){
      return $msg;
    }
    return $this->updateMontant();
  }
}



?>
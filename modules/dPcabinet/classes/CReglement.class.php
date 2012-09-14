<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision$
 * @author Fabien Ménager
 */

class CReglement extends CMbMetaObject {
  // DB Table key
  var $reglement_id    = null;

  // DB References
  var $banque_id       = null;

  // DB fields
  var $date            = null;
  var $montant         = null;
  var $emetteur        = null;
  var $mode            = null;
  var $object_class    = null;
  var $object_id       = null;
  var $num_bvr         = null;
  
  // Fwd References
  var $_ref_consultation = null;
  var $_ref_banque       = null;
  var $_ref_facture      = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'reglement';
    $spec->key   = 'reglement_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs['object_class']    = 'enum notNull list|CConsultation|CFactureConsult show|0';
    $specs['banque_id']       = 'ref class|CBanque';
    $specs['date']            = 'dateTime notNull';
    $specs['montant']         = 'currency notNull';
    $specs['emetteur']        = 'enum list|patient|tiers';
    $specs['mode']            = 'enum notNull list|cheque|CB|especes|virement|BVR|autre default|cheque';
    $specs['num_bvr']         = 'str';
    return $specs;
  }
  
  function loadRefBanque() {
    $this->_ref_banque = $this->loadFwdRef("banque_id", 1);
  }
  
  function loadRefsFwd() {
    $this->loadTargetObject();
    $this->loadRefBanque();
  }
  
  function check () {
    if ($msg = parent::check()) {
      return $msg;
    }
    
    if (!$this->montant) {
      return 'Le montant du règlement ne doit pas être nul';
    }
    
    if (!$this->mode) {
      return 'Le mode de paiment ne doit pas être nul';
    }
    
    $this->loadRefsFwd();
    
    if ($this->object_class == "CConsultation" && !$this->_ref_object->valide) {
      return "Impossible d'enregistrer un règlement car le tarif de la consultation n'est pas validé";
    }
  }
  
  /**
   * Accesseur sur la 
   * Fait abstraction de l'ambivalence des consultations et des factures de consultation
   * Charge les actes des consultations en question
   * 
   * @return array Consultations concernées
   */
  function loadRefFacture() {
    $target = $this->loadTargetObject();
    
    if ($target instanceof CConsultation) {
      $target->loadRefsActes();
      $facture = new CFactureConsult();
      $facture->_view = sprintf("CO%08d", $target->_id);
      $facture->_ref_patient   = $target->loadRefPatient();
      $facture->_ref_praticien = $target->loadRefPraticien();
      $facture->_ref_consults  = array($target->_id => $target);
      $facture->updateMontants();
      return $this->_ref_facture = $facture;
    }
    
    if ($target instanceof CFactureConsult) {
      $target->loadRefsConsults();
      $target->loadRefPatient();
      $target->loadRefPraticien();
      return $this->_ref_facture = $target;
    }
  }
  
  /**
   * Acquite la facture automatiquement
   * @return Store-like message
   */
  function acquiteFacture() {
    $this->loadRefsFwd();
    
    // Cas de la consultation
    if ($this->object_class == "CConsultation"){
      $consult = $this->_ref_object;
      $consult->loadRefsReglements();
      
      // Acquitement patient
      if ($this->emetteur == "patient" && $consult->du_patient) {
        $consult->patient_date_reglement = $consult->_du_patient_restant <= 0 ? mbDate() : "";
      }
        
      // Acquitement tiers
      if ($this->emetteur == "tiers" && $consult->du_tiers) {
        $consult->tiers_date_reglement = $consult->_du_tiers_restant <= 0 ? mbDate() : "";
      }
      
      return $consult->store();
    }
    
    // Cas de la facture
    if ($this->object_class == "CFactureConsult"){
      $facture = $this->_ref_object;
      $facture->loadRefsReglements();
      
      // Acquitement patient
      if ($this->emetteur == "patient" && $facture->du_patient) {
        $facture->patient_date_reglement = $facture->_du_patient_restant <= 0 ? mbDate() : "";
      }
        
      // Acquitement tiers
      if ($this->emetteur == "tiers" && $facture->du_tiers) {
        $facture->tiers_date_reglement = $facture->_du_tiers_restant <= 0 ? mbDate() : "";
      }
      
      return $facture->store();
    }
  }
  
  function store() {
    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }
    
    return $this->acquiteFacture();
  }
  
  function delete() {
    // Preload consultation
    $this->load();
    $this->loadRefsFwd();
    
    // Standard delete
    if ($msg = parent::delete()) {
      return $msg;
    }

    return $this->acquiteFacture();
  }
  
  function getPerm($permType) {
    if(!$this->_ref_consultation) {
      $this->loadRefsFwd();
    }
    return $this->_ref_consultation->getPerm($permType);
  }
}

?>
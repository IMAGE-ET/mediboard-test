<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision: $
 * @author Fabien Ménager
 */

class CReglement extends CMbObject {
  // DB Table key
  var $reglement_id    = null;

  // DB References
  var $consultation_id = null;
  var $banque_id       = null;

  // DB fields
  var $date            = null;
  var $montant         = null;
  var $emetteur        = null;
  var $mode            = null;
  
  // Fwd References
  var $_ref_consultation = null;
  var $_ref_banque     = null;
  
  function CReglement() {
    $this->CMbObject('reglement', 'reglement_id');
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    $specs = parent::getSpecs();
    $specs['consultation_id'] = 'notNull ref class|CConsultation';
    $specs['banque_id']       = 'ref class|CBanque';
    $specs['date']            = 'notNull dateTime';
    $specs['montant']         = 'currency';
    $specs['emetteur']        = 'enum list|patient|tiers';
    $specs['mode']            = 'enum list|cheque|CB|especes|virement|autre default|cheque';
    return $specs;
  }
  
  function loadRefsFwd() {
    $this->_ref_consultation = new CConsultation();
    $this->_ref_consultation->load($this->consultation_id);
  }
  
  function loadRefsBack() {
    $this->_ref_banque = new CBanque();
    $this->_ref_banque->load($this->banque_id);
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
  }
  
  function store() {
    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }
    // Au cas où le reglement fait l'acquittement
    $this->loadRefsFwd();
    $this->_ref_consultation->updateDBFields();
    return $this->_ref_consultation->store();
  }
  
  function delete() {
    // Au cas où le reglement fait l'acquittement
    $this->load($this->_id);
    $this->loadRefsFwd();
    $consult = $this->_ref_consultation;
    // Standard delete
    if ($msg = parent::delete()) {
      return $msg;
    }
    $consult->updateDBFields();
    return $consult->store();
  }
  
  function getPerm($permType) {
    if(!$this->_ref_consultation) {
      $this->loadRefsFwd();
    }
    return $this->_ref_consultation->getPerm($permType);
  }
}

?>
<?php

/**
 * dPccam
 *
 * @category Ccam
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

class CActe extends CMbMetaObject {
  public $montant_depassement;
  public $montant_base;
  public $execution;
  
  // DB References
  public $executant_id;
  public $facturable;

  // Form fields
  public $_preserve_montant;
  public $_montant_facture;
  
  // Derived fields
  public $_full_code;
  
  // Behaviour fields
  public $_check_coded = true;
  public $_permissive;
  
  // Distant object
  /** @var  CSejour */
  public $_ref_sejour;

  /** @var  CPatient */
  public $_ref_patient;

  /** @var  CMediusers Probavle user*/
  public $_ref_praticien;

  /** @var  CMediusers Actual user*/
  public $_ref_executant;
  
  public $_list_executants;
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_montant_facture = $this->montant_base + $this->montant_depassement;
  }
  
  /**
   * @return CSejour
   */
  function loadRefSejour() {
    if (null == $object = $this->loadTargetObject()) {
      return null;
    }

    return $this->_ref_sejour = $object->loadRefSejour();
  }
  
  /**
   * @return CPatient
   */
  function loadRefPatient() {
    if (null == $object = $this->loadTargetObject()) {
      return null;
    }

    return $this->_ref_patient = $object->loadRefPatient();
  }
  
  /**
   * @return CMediusers
   */
  function loadRefPraticien() {
    if (null == $object = $this->loadTargetObject()) {
      return null;
    }
    
    return $this->_ref_praticien = $object->loadRefPraticien();
  }

  /**
   * @return CMediusers
   */
  function loadRefExecutant() {
    $this->_ref_executant = $this->loadFwdRef("executant_id", true);
    $this->_ref_executant->loadRefFunction();
    return $this->_ref_executant;
  }
  
  function loadListExecutants() {
    $user = CMediusers::get(); 
    $this->_list_executants = $user->loadProfessionnelDeSante(PERM_READ);

    // No executant guess for the existing acte
    if ($this->executant_id || $this->_id) {
      return null;
    }
    
    // User executant
    if (CAppUI::pref("user_executant")) {
      $this->executant_id = $user->_id;
      return null;
    }

    // Referring pratician executant
    $praticien = $this->loadRefPraticien();
    if ($praticien && $praticien->_id) {
      $this->executant_id = $praticien->_id;
      return;
    }
  }
  
  function getProps() {
    $props = parent::getProps();

    $props["object_id"]           = "ref notNull class|CCodable meta|object_class";
    $props["executant_id"]        = "ref notNull class|CMediusers";
    $props["montant_base"]        = "currency";
    $props["montant_depassement"] = "currency";
    $props["execution"]              = "dateTime notNull";
    $props["facturable"]          = "bool notNull default|1 show|0";

    $props["_montant_facture"]    = "currency";

    return $props;
  }
  
  /**
   * Check if linked object is already coded
   *
   * @return bool
   */
  function checkCoded() {
    if (!$this->_check_coded || $this->_forwardRefMerging) {
      return null;
    }
    
    $this->completeField("object_class");
    $this->completeField("object_id");
    $object = new $this->object_class;
    $object->load($this->object_id);
    if ($object->_coded == "1") {
      return CAppUI::tr($object->_class) ." déjà validée : Impossible de coter l\'acte";
    }
    return null;
  }

  /**
   * Tell wether acte is ready for precoding
   *
   * @return bool
   */
  function getPrecodeReady() {
    return false;
  }
  
  /**
   * Return a full serialised code
   *
   * @return string Serialised full code
   */
  function makeFullCode() {
    return $this->_full_code = "";
  }
    
  /**
   * Precode with a full serialised code for the act
   *
   * @param string $code Serialised full code
   *
   * @return void
   */
  function setFullCode($details) {
  }
  
  function updateMontant(){
    if ($this->_preserve_montant || $this->_forwardRefMerging) {
      return null;
    }

    /** @var CCodable $object */
    $object = new $this->object_class;
    $object->load($this->object_id);
    
    // Permet de mettre a jour le montant dans le cas d'une consultation
    return $object->doUpdateMontants();
  }

  function loadExecution() {
    $this->loadTargetObject();
    $this->_ref_object->getActeExecution();
    $this->execution = $this->_ref_object->_acte_execution;
  }

  function store(){
    if ($msg = parent::store()) {
      return $msg;
    }
    
    return $this->updateMontant();
  }
  
  function delete(){
    if ($msg = parent::delete()) {
      return $msg;
    }
    
    if (!$this->_purge) {
      return $this->updateMontant();
    }
    return null;
  }
}

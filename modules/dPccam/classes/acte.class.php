<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CActe extends CMbMetaObject {
  
  // DB fields
  var $montant_depassement = null;
  var $montant_base        = null;
  
  // DB References
  var $executant_id        = null;

  // Form fields
  var $_preserve_montant   = null; 
  var $_montant_facture    = null;
  
  // Behaviour fields
  var $_check_coded  = true;
  
  // Distant object
  var $_ref_sejour = null;
  var $_ref_patient = null;
  var $_ref_praticien = null; // Probable user
  var $_ref_executant = null; // Actual user
  
  var $_list_executants = null;
  
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
    $this->_ref_patient =& $this->_ref_object->_ref_patient;
  }
  
  function loadRefPraticien() {
    $this->loadTargetObject();
    $this->_ref_object->loadRefPraticien();
    $this->_ref_praticien =& $this->_ref_object->_ref_praticien;
  }
  
  function loadRefExecutant() {
    $this->_ref_executant = new CMediusers();
    $this->_ref_executant->load($this->executant_id);
    $this->_ref_executant->loadRefFunction();
  }
  
  function loadListExecutants($guess = true) {
    global $AppUI;
    
    $list_executants = new CMediusers;
    $this->_list_executants = $list_executants->loadProfessionnelDeSante(PERM_READ);

    // We guess who is the executant
    if ($guess && $this->executant_id == null && $this->_id == null) {
      if ($this->_ref_object && $this->loadRefPraticien() && $this->_ref_praticien->_id) {
        $this->executant_id = $this->_ref_praticien->_id;
        return;
      }
      else {
        $user = new CMediusers();
        $user->load($AppUI->user_id);
        if ($user->isPraticien() || $user->isInfirmiere()) {
          $this->executant_id = $user->_id;
          return;
        }
      }
    }
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["object_id"]           = "ref notNull class|CCodable meta|object_class";
    $specs["executant_id"]        = "ref notNull class|CMediusers";
    $specs["montant_base"]        = "currency";
    $specs["montant_depassement"] = "currency";
    $specs["_montant_facture"]    = "currency";
    return $specs;
  }
  
  function checkCoded() {
    if (!$this->_check_coded){
      return;
    }
    
    $this->completeField("object_class");
    $this->completeField("object_id");
    $object = new $this->object_class;
    $object->load($this->object_id);
    if ($object->_coded == "1") {
      return CAppUI::tr($object->_class_name) ." déjà validée : Impossible de coter l\'acte";
    }
  }

  /**
   * Tell wether acte is ready for prcoding
   * @return bool
   */
  function getPrecodeReady() {
    return false;
  }
  
  /**
   * Return a full serialised code for precoding
   * @return string Serialised full code
   */
  function makeFullCode() {
  }
  
  /**
   * Precode with a full serialised code for the act
   * @param string $code Serialised full code
   * @return void
   */
  function setFullCode($details) {
  }
  
  function updateMontant(){
    if (!$this->_preserve_montant){
      $object = new $this->object_class;
      $object->load($this->object_id);
      // Permet de mettre a jour le montant dans le cas d'une consultation
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
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
  var $facturable          = null;

  // Form fields
  var $_preserve_montant   = null; 
  var $_montant_facture    = null;
  
  // Derived fields
  var $_full_code = null;
  
  // Behaviour fields
  var $_check_coded  = true;
  var $_permissive   = null;
  
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
    $this->_ref_executant = $this->loadFwdRef("executant_id", true);
    $this->_ref_executant->loadRefFunction();
    
    return $this->_ref_executant;
  }
  
  function loadListExecutants($guess = true) {
    
    $list_executants = new CMediusers;
    $this->_list_executants = $list_executants->loadProfessionnelDeSante(PERM_READ);

    // We guess who is the executant
    if ($guess && $this->executant_id == null && $this->_id == null) {
      if ($this->_ref_object && $this->loadRefPraticien() && $this->_ref_praticien->_id) {
        $this->executant_id = $this->_ref_praticien->_id;
        return;
      }
      else {
        $user = CMediusers::get();
        if ($user->isPraticien() || $user->isInfirmiere()) {
          $this->executant_id = $user->_id;
          return;
        }
      }
    }
  }
  
  function getProps() {
    $props = parent::getProps();

    $props["object_id"]           = "ref notNull class|CCodable meta|object_class";
    $props["executant_id"]        = "ref notNull class|CMediusers";
    $props["montant_base"]        = "currency";
    $props["montant_depassement"] = "currency";
    $props["facturable"]          = "bool notNull default|1 show|0";

    $props["_montant_facture"]    = "currency";

    return $props;
  }
  
  /**
   * Check if linked object is already coded
   * @return bool
   */
  function checkCoded() {
    if (!$this->_check_coded || $this->_forwardRefMerging){
      return;
    }
    
    $this->completeField("object_class");
    $this->completeField("object_id");
    $object = new $this->object_class;
    $object->load($this->object_id);
    if ($object->_coded == "1") {
      return CAppUI::tr($object->_class) ." déjà validée : Impossible de coter l\'acte";
    }
  }

  /**
   * Tell wether acte is ready for precoding
   * @return bool
   */
  function getPrecodeReady() {
    return false;
  }
  
  /**
   * Return a full serialised code
   * @return string Serialised full code
   */
  function makeFullCode() {
    return $this->_full_code = "";
  }
    
  /**
   * Precode with a full serialised code for the act
   * @param string $code Serialised full code
   * @return void
   */
  function setFullCode($details) {
  }
  
  function updateMontant(){
    if ($this->_preserve_montant || $this->_forwardRefMerging) {
      return;
    }
    
    $object = new $this->object_class;
    $object->load($this->object_id);
    
    // Permet de mettre a jour le montant dans le cas d'une consultation
    return $object->doUpdateMontants();
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
  }
}

?>
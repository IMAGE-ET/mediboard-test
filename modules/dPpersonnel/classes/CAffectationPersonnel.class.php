<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPpersonnel
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

/**
 * Class CAffectationPersonnel
 */
class CAffectationPersonnel extends CMbMetaObject {
  // DB Table key
  public $affect_id;
  
  // DB references
  public $personnel_id;
  
  // DB fields
  public $realise;
  public $debut;
  public $fin;

  // Form fields
  public $_debut;
  public $_fin;
  
  // References
  public $_ref_personnel;
  public $_ref_object;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "affectation_personnel";
    $spec->key   = "affect_id";
    $spec->uniques["unique"] = array("personnel_id", "object_class", "object_id");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["personnel_id"] = "ref notNull class|CPersonnel";
    $props["realise"]  = "bool notNull";
    $props["debut"]    = "dateTime";
    $props["fin"]      = "dateTime moreThan|debut";

    $props["_debut"]   = "time";
    $props["_fin"]     = "time moreThan|_debut";
    
    return $props;
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefPersonnel();
  }

  /**
   * Load Personnel
   *
   * @return CPersonnel|null
   */
  function loadRefPersonnel() {
    return $this->_ref_personnel = $this->loadFwdRef("personnel_id");
  }
  
  function loadRefObject($cache = true) {
    return $this->_ref_object = $this->loadTargetObject($cache);
  }
   
  /**
   * Trouve les affectations avec cible et personnel identique
   *
   * @return $array Liste des siblings
   */
  function getSiblings() {
    // Version complete
    $clone = new CAffectationPersonnel();
    $clone->load($this->_id);
    $clone->extendsWith($this);
    
    // Filtre exact
    $sibling = new CAffectationPersonnel();
    $sibling->object_class = $clone->object_class;
    $sibling->object_id    = $clone->object_id;
    $sibling->personnel_id = $clone->personnel_id;
    
    // Chargement des siblings
    $siblings = $sibling->loadMatchingList();
    unset($siblings[$this->_id]);
    return $siblings;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefs();  
    if ($this->object_class == "CPlageOp") {
      $this->_debut = CMbDT::addDateTime($this->_ref_object->debut, $this->_ref_object->date);
      $this->_fin = CMbDT::addDateTime($this->_ref_object->fin, $this->_ref_object->date);
    }
    
    if ($this->object_class == "COperation" || $this->object_class == "CBloodSalvage" ) {
      if ($this->debut) {
        $this->_debut = CMbDT::time($this->debut);
      }
      if ($this->fin) {
        $this->_fin   = CMbDT::time($this->fin);
      }
    }
  }

  /**
   * @see parent::updatePlainFields()
   */
  function updatePlainFields(){
    if ($this->object_class == "COperation" || $this->object_class == "CBloodSalvage" ) {
      $this->loadRefObject();
      $this->_ref_object->loadRefPlageOp();
      
      if ($this->_debut =="current") {
        $this->_debut = CMbDT::time();
      }
      
      if ($this->_fin =="current") {
        $this->_fin = CMbDT::time();
      }

      if ($this->_debut !== null && $this->_debut != "") {
        $this->_debut = CMbDT::time($this->_debut);
        $this->debut = CMbDT::addDateTime($this->_debut, CMbDT::date($this->_ref_object->_datetime));
      }
      
      if ($this->_fin !== null && $this->_fin != "") {
        $this->_fin = CMbDT::time($this->_fin);
        $this->fin = CMbDT::addDateTime($this->_fin, CMbDT::date($this->_ref_object->_datetime));
      }
      
      // Suppression de la valeur
      if ($this->_debut === "") {
        $this->debut = "";
      }
      if ($this->_fin === "") {
        $this->fin = "";
      } 
      
      // Mise a jour du champ realise
      if ($this->debut !== null && $this->fin !== null) {
        $this->realise = 1;
      }
      
      if ($this->debut === "" || $this->fin === "") {
        $this->realise = 0;
      }
    }
  }
}

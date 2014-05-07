<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPpersonnel
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Class CAffectationPersonnel
 */
class CAffectationPersonnel extends CMbMetaObject {
  // DB Table key
  public $affect_id;
  
  // DB references
  public $personnel_id;
  public $parent_affectation_id;

  // DB fields
  public $realise;
  public $debut;
  public $fin;

  // Form fields
  public $_debut;
  public $_debut_dt;
  public $_fin;
  public $_fin_dt;
  
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
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["personnel_id"]          = "ref notNull class|CPersonnel";
    $props["parent_affectation_id"] = "ref class|CAffectationPersonnel";
    $props["realise"]               = "bool notNull";
    $props["debut"]                 = "dateTime";
    $props["fin"]                   = "dateTime moreThan|debut";
    $props["object_class"]          = "enum list|CBloodSalvage|COperation|CPlageOp";
    $props["_debut"]                = "time";
    $props['_debut_dt']             = 'dateTime';
    $props["_fin"]                  = "time moreThan|_debut";
    $props['_fin_dt']               = 'dateTime moreThan|_debut_dt';

    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["affectation_child"] = "CAffectationPersonnel parent_affectation_id";
    return $backProps;
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
    return $this->_ref_personnel = $this->loadFwdRef("personnel_id", true);
  }
  
  function loadRefObject($cache = true) {
    return $this->_ref_object = $this->loadTargetObject($cache);
  }
   
  /**
   * Trouve les affectations avec cible et personnel identique
   *
   * @return array Liste des siblings
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
   * @see parent::check()
   */
  function check() {
    $this->completeField("debut", "fin");
    if ($this->debut == null || $this->fin == null) {
      return parent::check();
    }

    $siblings = $this->getSiblings();

    if (count($siblings)) {
      foreach ($siblings as $_sibling) {
        if ($_sibling->debut == null || $_sibling->fin == null) {
          continue;
        }
        if (CMbRange::collides($this->debut, $this->fin, $_sibling->debut, $_sibling->fin) ||
            CMbRange::inside($this->debut, $this->fin, $_sibling->debut, $_sibling->fin)   ||
            CMbRange::inside($_sibling->debut, $_sibling->fin, $this->debut, $this->fin)) {
          return "Collision de personnel !";
        }
      }
    }

    return parent::check();
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefs();

    switch ($this->object_class) {
      case "CPlageOp":
        $this->_debut = CMbDT::addDateTime($this->_ref_object->debut, $this->_ref_object->date);
        $this->_debut_dt = CMbDT::addDateTime($this->_ref_object->debut, $this->_ref_object->date);
        $this->_fin = CMbDT::addDateTime($this->_ref_object->fin, $this->_ref_object->date);
        $this->_fin_dt = CMbDT::addDateTime($this->_ref_object->fin, $this->_ref_object->date);
        break;
      case "COperation":
      case "CBloodSalvage":
        if ($this->debut) {
          $this->_debut = CMbDT::time($this->debut);
          $this->_debut_dt = $this->debut;
        }
        if ($this->fin) {
          $this->_fin   = CMbDT::time($this->fin);
          $this->_fin_dt = $this->fin;
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
      
      if ($this->_debut == "current") {
        $this->_debut = CMbDT::time();
      }
      
      if ($this->_fin == "current") {
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

      if ($this->_debut_dt !== null && $this->_debut_dt != "") {
        $this->debut = $this->_debut_dt;
      }

      if ($this->_fin_dt !== null && $this->_fin_dt != "") {
        $this->fin = $this->_fin_dt;
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

<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CFicheAutonomie extends CMbObject {
  // DB Table key
  var $fiche_autonomie_id = null;
 
  // DB Fields
  var $sejour_id              = null;
  var $alimentation           = null;
  var $toilette               = null;
  var $habillage_haut         = null;
  var $habillage_bas          = null;
  var $utilisation_toilette   = null;
  var $transfert_lit          = null;
  var $locomotion             = null;
  var $locomotion_materiel    = null;
  var $escalier               = null;
  var $pansement              = null;
  var $escarre                = null;
  var $comprehension          = null;
  var $expression             = null;
  var $memoire                = null;
  var $resolution_pb          = null;
  var $etat_psychique         = null;
  var $devenir_envisage       = null;
  
  // Patient
  var $_patient_id    = null;
  
  // Sejour
  var $_group_id      = null;
  var $_praticien_id  = null;
  var $_duree_prevue  = null;
  var $_annule        = null;
  var $_entree        = null;
  var $_sortie        = null;
  
  // Object References
  var $_ref_sejour    = null;
    
  // Behaviour fields
  var $_bind_sejour   = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'fiche_autonomie';
    $spec->key   = 'fiche_autonomie_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["sejour_id"]      = "ref notNull class|CSejour cascade";
    $specs["alimentation"]   = "enum list|autonome|partielle|totale";
    $specs["toilette"]       = "enum list|autonome|partielle|totale";
    $specs["habillage_haut"] = "enum list|autonome|partielle|totale";
    $specs["habillage_bas"]  = "enum list|autonome|partielle|totale";
    $specs["transfert_lit"]  = "enum list|autonome|partielle|totale";
    $specs["locomotion"]     = "enum list|autonome|partielle|totale";
    $specs["escalier"]       = "enum list|autonome|partielle|totale";
    $specs["utilisation_toilette"]   = "enum list|sonde|couche|bassin|stomie";
    $specs["locomotion_materiel"]    = "enum list|canne|cadre|fauteuil";
    $specs["pansement"]              = "bool";
    $specs["escarre"]                = "bool";
    $specs["comprehension"]          = "enum list|intacte|alteree";
    $specs["expression"]             = "enum list|intacte|alteree";
    $specs["memoire"]                = "enum list|intacte|alteree";
    $specs["resolution_pb"]          = "enum list|intacte|alteree";
    $specs["etat_psychique"]         = "text";
    $specs["devenir_envisage"]       = "text";
        
    $specs["_patient_id"]   = "ref notNull class|CPatient";
    $specs["_praticien_id"] = "ref notNull class|CMediusers";
    $specs["_group_id"]     = "ref notNull class|CGroups";
    $specs["_entree"]       = "dateTime";
    $specs["_sortie"]       = "dateTime";
    $specs["_duree_prevue"] = "num";
    return $specs;
  }
  
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefSejour();
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    
    $sejour =& $this->_ref_sejour;

    $this->_praticien_id = $sejour->praticien_id;
    $this->_entree       = $sejour->_entree;
    $this->_annule       = $sejour->annule;
    $this->_sortie       = $sejour->_sortie;
    
    $patient =& $sejour->_ref_patient;
    
    $this->_patient_id = $patient->_id;
    $this->_view       = "Fiche d'autonomie du " . mbDateToLocale(mbDate($this->_entree)). " pour $patient->_view";
  }
  
  function loadRefSejour() {
    $this->_ref_sejour = new CSejour;
    $this->_ref_sejour->load($this->sejour_id);
    $this->_ref_sejour->loadRefsFwd();
  }
  
  function bindSejour() {
    if (!$this->_bind_sejour) {
      return;
    }
        
    $this->_bind_sejour = false;
    
    $this->loadRefsFwd();
    $sejour =& $this->_ref_sejour;
    $sejour->patient_id    = $this->_patient_id;
    $sejour->group_id      = $this->_group_id;
    $sejour->praticien_id  = $this->_praticien_id;
    $sejour->type          = "ssr";
    $sejour->entree_prevue = $this->_entree;
    $sejour->sortie_prevue = $this->_sortie;
    $sejour->annule        = $this->_annule;    

    // Le patient est souvent chargé à vide ce qui pose problème
    // dans le onAfterStore(). Ne pas supprimer.
    $sejour->_ref_patient = null;

    if ($msg = $sejour->store()) {
      return $msg;
    }
    
    // Affectation du sejour_id à la fiche d'autonomie
    $this->sejour_id = $sejour->_id;
  }
  
  function store() {
    // Bind Sejour
    if ($msg = $this->bindSejour()) {
      return $msg;
    }
        
    // Standard Store
    if ($msg = parent::store()){
      return $msg;
    }
    
    $this->_ref_sejour->onAfterStore();
  }
}

?>
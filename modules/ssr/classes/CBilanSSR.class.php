<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Bilan d'entrée SSR
 */
class CBilanSSR extends CMbObject {
  // DB Table key
  var $bilan_id = null;
  
  // DB Fields
  var $sejour_id     = null;
  var $technicien_id = null;
  var $entree        = null;
  var $sortie        = null;
  var $planification = null;
  var $brancardage   = null;
  
  var $hospit_de_jour = null;
  var $demi_journee_1 = null;
  var $demi_journee_2 = null;
  
  // Form fields
  var $_demi_journees = null;
  var $_premier_jour = null; 
  var $_dernier_jour = null; 
  var $_encours      = null; 
  
  // References
  var $_ref_technicien = null;

  // Distant Fields
  var $_kine_referent_id    = null;
  var $_kine_journee_id     = null;
  var $_prat_demandeur_id   = null;
  var $_sejour_demandeur_id = null;

  // Distant references
  var $_ref_kine_referent    = null;
  var $_ref_kine_journee     = null;
  var $_ref_prat_demandeur   = null;
  var $_ref_sejour_demandeur = null;
  
  /**
   * Surcharge de la spécification d'objet
   * @return CMbObjectSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "bilan_ssr";
    $spec->key   = "bilan_id";
    $spec->uniques["sejour_id"] = array("sejour_id");
    return $spec;
  }

  /**
   * Surcharge de spécifications de propriétés
   * @return array<string>
   */
  function getProps() {
    $props = parent::getProps();
    
    // DB Fields
    $props["sejour_id"    ] = "ref notNull class|CSejour show|0";
    $props["technicien_id"] = "ref class|CTechnicien";
    $props["entree"       ] = "text helped";
    $props["sortie"       ] = "text helped";
    $props["planification"] = "bool default|1";
    $props["brancardage"  ] = "bool default|0";
    
    $props["hospit_de_jour"] = "bool default|0";
    $props["demi_journee_1"] = "bool default|0";
    $props["demi_journee_2"] = "bool default|0";

    // Form fields
    $props["_demi_journees"] = "enum list|none|am|pm|all";
    $props["_premier_jour"] = "date";
    $props["_dernier_jour"] = "date";
        
    // Distant Fields
    $props["_kine_referent_id" ]   = "ref class|CMediusers";
    $props["_kine_journee_id"  ]   = "ref class|CMediusers";
    $props["_prat_demandeur_id"]   = "ref class|CMediusers";
    
    return $props;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    if ($this->hospit_de_jour) {
      static $demi_journees = array(
        "0" => array(
          "0" => "none",
          "1" => "pm",
        ),
        "1" => array(
          "0" => "am",
          "1" => "all",
        ),
      );
      $this->_demi_journees = $demi_journees[$this->demi_journee_1][$this->demi_journee_2];
    }
  }
  
  /**
   * @see parent::store() 
   */
  function store() {
    // Transférer les événéments de l'ancien référent vers le nouveau
  	if ($this->technicien_id && $this->fieldAltered("technicien_id")) {
      $technicien = $this->loadRefTechnicien();
      $old_technicien = new CTechnicien;
      $old_technicien->load($this->_old->technicien_id);
  	  $evenement = new CEvenementSSR();
      $evenement->therapeute_id = $old_technicien->kine_id;
      $evenement->sejour_id = $this->sejour_id;
      foreach ($evenement->loadMatchingList() as $_evenement) {
      	if (!$_evenement->_traite) {
          $_evenement->therapeute_id = $technicien->kine_id;
          $_evenement->store();
          CAppUI::setMsg("{$_evenement->_class}-msg-modify", UI_MSG_OK);
       	}
      }
    }
    
    if ($msg = parent::store()) {
      return $msg;
    }
  }

  /**
   * Chargement du séjour 
   * Calcul les premier et dernier jours ouvrés de rééducation 
   * @return CSejour sejour
   */
  function loadRefSejour() {
    $sejour = $this->loadFwdRef("sejour_id", true);
    
    // Premier et dernier jour ouvré (exclusion des week-end)
    $premier_jour = CMbDT::date($sejour->entree);
    $dernier_jour = CMbDT::date($sejour->sortie);
    if (!$this->hospit_de_jour) {
      $numero_premier_decalage = CMbDT::transform(null, $premier_jour, "%w");
      $numero_dernier_decalage = CMbDT::transform(null, $dernier_jour, "%w");
      $premier_jour = CMbDT::date(in_array($numero_premier_decalage, array(5, 6)) ? "next monday" : "+1 day", $premier_jour);
      $dernier_jour = CMbDT::date(in_array($numero_dernier_decalage, array(1, 7)) ? "last friday" : "-1 day", $dernier_jour);
    }
    $this->_premier_jour = $premier_jour;
    $this->_dernier_jour = $dernier_jour;
    
    return $this->_ref_sejour = $sejour;
  }
  
  /**
   * Chargement du technicien 
   * @return CTechnicien technicien
   */
  function loadRefTechnicien() {
    return $this->_ref_technicien = $this->loadFwdRef("technicien_id", true);
  }
  
  /**
   * Chargement du kiné référent
   * @return CMediusers Kiné référent
   */
  function loadRefKineReferent() {
    $this->loadRefTechnicien();
    $technicien =& $this->_ref_technicien;
    $technicien->loadRefKine();
    $this->_ref_kine_referent = $technicien->_ref_kine;
    $this->_kine_referent_id = $this->_ref_kine_referent->_id;
    return $this->_ref_kine_referent;
  }
  
  /**
   * Chargement du kiné référent et kiné journée pour une date donnée
   * @param date $date Date courante if null;
   * @return CMediusers Kiné journée
   */
  function loadRefKineJournee($date = null) {
    $this->loadRefKineReferent();
    $this->_ref_kine_journee = $this->_ref_kine_referent;

    // Recherche d'un remplacement
    $sejour = $this->loadRefSejour();
    foreach ($sejour->loadRefReplacements() as $_replacement) {
      if ($_replacement->_id) {
        $_replacement->loadRefConge();
        $conge = $_replacement->_ref_conge;
        if (CMbRange::in(CValue::first($date, CMbDT::date()), $conge->date_debut, $conge->date_fin)) {
          $_replacement->loadRefReplacer();
          $replacer =& $_replacement->_ref_replacer;
          $replacer->loadRefFunction();
          $this->_ref_kine_journee = $_replacement->_ref_replacer;
          break;
        }
      }
    }
    
    $this->_kine_journee_id = $this->_ref_kine_journee->_id;
    return $this->_ref_kine_journee;
  }

  /**
   * Chargement du séjour probablement demandeur du séjour SSR
   * (dont la sortie est proche de l'entree du séjour SSR)
   * @return CSejour
   */
  function loadRefSejourDemandeur() {
    // Effet de cache
    if ($this->_ref_sejour_demandeur) {
      return;
    }
    
    // Requête
    $sejour_ssr = $this->loadRefSejour();
    $tolerance = CAppUI::conf("ssr CBilanSSR tolerance_sejour_demandeur");
    $date_min = CMbDT::date("- $tolerance DAYS", $sejour_ssr->entree);
    $date_max = CMbDT::date("+ $tolerance DAYS", $sejour_ssr->entree);
    $where["sortie"]     = "BETWEEN '$date_min' AND '$date_max'";
    $where["patient_id"] = "= '$sejour_ssr->patient_id'";
    $where["annule"]     = " = '0'";
    $where["type"]       = " != 'ssr'";
    
    // Chargement
    $sejour = new CSejour;
    $sejour->loadObject($where);
    return $this->_ref_sejour_demandeur = $sejour;
  }

  /**
   * Chargement du praticien demandeur sur la base du séjour demandeur
   * @return CMediusers
   */
  function loadRefPraticienDemandeur() {
    $sejour = $this->loadRefSejourDemandeur();
    $praticien = $sejour->loadRefPraticien(1);
    
    $this->_prat_demandeur_id = $praticien->_id;
    return $this->_ref_prat_demandeur = $praticien;
  }


  /**
   * Load Sejour for technicien at a date
   * @return array<CSejour>
   **/ 
  static function loadSejoursSSRfor($technicien_id, $date, $show_cancelled_services = true) {
    $group = CGroups::loadCurrent();

    // Masquer les services inactifs
    if (!$show_cancelled_services) {
      $service = new CService;
      $service->group_id = $group->_id;
      $service->cancelled = "1";
      $services = $service->loadMatchingList();
      $where["service_id"] = CSQLDataSource::prepareNotIn(array_keys($services));
    }
    
    $where["type"] = "= 'ssr'";
    $where["group_id"] = "= '$group->_id'";
    $where["annule"] = "= '0'";
    $where["bilan_ssr.technicien_id"] = $technicien_id ? "= '$technicien_id'" : "IS NULL";
    $leftjoin["bilan_ssr"] = "bilan_ssr.sejour_id = sejour.sejour_id";
    return CSejour::loadListForDate($date, $where, "entree_reelle", null, null, $leftjoin);
  }
  
  /**
   * Calcul si la réeducation est en cours au jour donné au regard des jours ouvrés
   * @param $date Date de référence
   * @return bool
   */
  function getDateEnCours($date) {
    $this->loadRefSejour();
    return $this->_encours = CMbRange::in($date, $this->_premier_jour, $this->_dernier_jour);
  }
  
  /**
   * Calcul si la réeducation est en cours au jour donné au regard des jours ouvrés
   * @param $date_min Date minimale 
   * @param $date_max Date maximale
   * @return bool
   */
  function getDatesEnCours($date_min, $date_max) {
    $this->loadRefSejour();
    return $this->_encours = CMbRange::collides($date_min, $date_max, $this->_premier_jour, $this->_dernier_jour);
  }
  
  static function loadSejoursSurConges(CPlageConge $plage, $date_min, $date_max) {
    $group_id = CGroups::loadCurrent()->_id;
    
    $date_min = max($date_min, $plage->date_debut);
    $date_max = min($date_max, $plage->date_fin);
    $date_max = CMbDT::date("+1 DAY", $date_max);
    
    $sejour = new CSejour();
    $ljoin["bilan_ssr" ] = "bilan_ssr.sejour_id     = sejour.sejour_id";
    $ljoin["technicien"] = "bilan_ssr.technicien_id = technicien.technicien_id";
    
    $where = array();
    $where["type"] = "= 'ssr'";
    $where["group_id"] = "= '$group_id'";
    $where["sejour.annule"] = "!= '1'";
    $where["sejour.entree"] = "<= '$date_max'";
    $where["sejour.sortie"] = ">= '$date_min'";
    $where["technicien.kine_id"] = " = '$plage->user_id'";
    return $sejour->loadList($where, null, null, null, $ljoin);
  }
  
}


?>
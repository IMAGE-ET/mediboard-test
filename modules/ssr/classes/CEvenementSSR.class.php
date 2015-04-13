<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Elément central de la planification d'évenements (aka séances) SSR par un rééducateur
 * et concernant un séjour et une ligne de prescription de ce séjour, pour une date donnée
 */
class CEvenementSSR extends CMbObject {
  // DB Table key
  public $evenement_ssr_id;
  
  // DB Fields
  public $prescription_line_element_id;
  public $sejour_id;
  public $debut; // DateTime
  public $duree; // Durée en minutes
  public $therapeute_id;
  public $equipement_id;
  public $realise;
  public $annule;
  public $remarque;
  public $seance_collective_id; // Evenement lié a une seance collective
  public $type_seance;
  public $nb_patient_seance;

  // Seances collectives
  public $_ref_element_prescription;
  public $_ref_seance_collective;
  
  // Derived Fields
  /** @var bool */
  public $_traite;
  /** @var time */
  public $_heure_fin;
  /** @var time */
  public $_heure_deb;
  /** @var int */
  public $_count_actes;

  // Behaviour Fields
  public $_nb_decalage_min_debut;
  public $_nb_decalage_heure_debut;
  public $_nb_decalage_jour_debut;
  public $_nb_decalage_duree;

  // References
  /** @var  CEquipement */
  public $_ref_equipement;
  /** @var  CSejour */
  public $_ref_sejour;
  /** @var  CMediusers */
  public $_ref_therapeute;
  /** @var  CActeCdARR[] */
  public $_ref_actes_cdarr;
  /** @var  CActeCsARR[] */
  public $_ref_actes_csarr;
  /** @var  CActeSSR[] */
  public $_ref_actes;
  /** @var  CEvenementSSR[] */
  public $_ref_evenements_seance;
  /** @var  CPrescriptionLineElement */
  public $_ref_prescription_line_element;

  // Behaviour field
  public $_traitement;

  /**
   * @see parent::getSpecs()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table       = 'evenement_ssr';
    $spec->key         = 'evenement_ssr_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    $props["prescription_line_element_id"] = "ref class|CPrescriptionLineElement";
    $props["sejour_id"]     = "ref class|CSejour show|0";
    $props["debut"]         = "dateTime show|0";

    $props["_heure_deb"]    = "time show|1";
    $props["_heure_fin"]    = "time show|1";
    $props["duree"]         = "num min|0";

    $props["therapeute_id"] = "ref class|CMediusers";
    $props["equipement_id"] = "ref class|CEquipement";
    $props["realise"]       = "bool default|0";
    $props["annule"]        = "bool default|0";
    $props["remarque"]      = "str";
    $props["seance_collective_id"] = "ref class|CEvenementSSR";
    $props["type_seance"]   = "enum list|dediee|non_dediee|collective default|dediee";
    $props["nb_patient_seance"] = "num";

    $props["_traite"]       = "bool";
    $props["_nb_decalage_min_debut"]   = "num";
    $props["_nb_decalage_heure_debut"] = "num";
    $props["_nb_decalage_jour_debut"]  = "num";
    $props["_nb_decalage_duree"]   = "num";
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["actes_cdarr"] = "CActeCdARR evenement_ssr_id";
    $backProps["actes_csarr"] = "CActeCsARR evenement_ssr_id";
    $backProps["evenements_ssr"] = "CEvenementSSR seance_collective_id";
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_traite = $this->realise || $this->annule;
    $this->_heure_deb = CMbDT::time($this->debut);
    $this->_heure_fin = CMbDT::time("+ $this->duree MINUTES", $this->debut);
  }

  /**
   * @see parent::check()
   */
  function check(){
    if ($this->_forwardRefMerging) {
      return null;
    }
    
    // Vérouillage d'un événement traité
    $this->completeField("realise","annule", "nb_patient_seance");
    $this->_traite = $this->realise || $this->annule;
    if ($this->_traite && !$this->_traitement) {
      return "Evénément déjà traité (réalisé ou annulé)";
    }

    // Evénement dans les bornes du séjour
    $this->completeField("sejour_id", "debut");
    $sejour = $this->loadRefSejour();
    
    // Vérifier seulement les jours car les sorties peuvent être imprécises pour les hospit de jours
    if ($sejour->_id && $this->debut) {
      $date_debut = CMbDT::date($this->debut);
      $date_entree = CMbDT::date(CMbDT::date($sejour->entree));
      $date_sortie = CMbDT::date(CMbDT::date($sejour->sortie));
      if (!CMbRange::in($date_debut, $date_entree, $date_sortie)) {
        return "Evenement SSR en dehors des dates du séjour";
      }
    }
    
    // Cas de la réalisation des événements SSR
    $this->loadRefTherapeute();
    
    // Si le therapeute n'est pas defini, c'est 
    if ($this->therapeute_id) {
      $therapeute = $this->_ref_therapeute;
    }
    else {
      // Chargement du therapeute de la seance
      $evt_seance = new CEvenementSSR();
      $evt_seance->load($this->seance_collective_id);
      $evt_seance->loadRefTherapeute();
      $therapeute = $evt_seance->_ref_therapeute;
    }

    if ($this->fieldModified("realise")) {
      // Si le thérapeute n'a pas d'identifiant CdARR
      if (!$therapeute->code_intervenant_cdarr) {
        return CAppUI::tr("CMediusers-code_intervenant_cdarr-none");
      }
      $therapeute->loadRefIntervenantCdARR();
      $code_intervenant_cdarr = $therapeute->_ref_intervenant_cdarr->code;

      // Création du RHS au besoins
      $rhs = $this->getRHS();
      if (!$rhs->_id) {
        $rhs->store();
      }
      
      if ($rhs->facture == 1) {
        CAppUI::stepAjax(CAppUI::tr("CRHS.charged"), UI_MSG_WARNING);
      }
      $this->loadView();
      // Complétion de la ligne RHS
      foreach ($this->loadRefsActesCdARR() as $_acte_cdarr) {
        $ligne = new CLigneActivitesRHS();
        $ligne->rhs_id                 = $rhs->_id;
        $ligne->executant_id           = $therapeute->_id;
        $ligne->code_activite_cdarr    = $_acte_cdarr->code;
        $ligne->code_intervenant_cdarr = $code_intervenant_cdarr;
        $ligne->loadMatchingObject();
        $ligne->crementDay($this->debut, $this->realise ? "inc" : "dec");
        $ligne->auto = "1";
        $ligne->store();
      }

      foreach ($this->loadRefsActesCsARR() as $_acte_csarr) {
        $ligne = new CLigneActivitesRHS();
        $ligne->rhs_id                 = $rhs->_id;
        $ligne->executant_id           = $therapeute->_id;
        $ligne->code_activite_csarr    = $_acte_csarr->code;
        $ligne->code_intervenant_cdarr = $code_intervenant_cdarr;
        $ligne->modulateurs            = $_acte_csarr->modulateurs;
        $ligne->phases                 = $_acte_csarr->phases;
        $ligne->nb_patient_seance      = $this->nb_patient_seance;
        $ligne->loadMatchingObject();
        $ligne->crementDay($this->debut, $this->realise ? "inc" : "dec");
        $ligne->auto = "1";
        $ligne->store();
      }
    }

    return parent::check();
  }

  /**
   * @see parent::canDeleteEx()
   */
  function canDeleteEx() {
    if ($msg = parent::canDeleteEx()) {
      return $msg;
    }
    
    // Impossible de supprmier un événement réalisé
    $this->completeField("realise","annule");
    $this->_traite = $this->realise || $this->annule;
    if ($this->realise) {
      return "CEvenementSSR-msg-delete-failed-realise";
    }

    return null;
  }

  /**
   * @see parent::loadView()
   */
  function loadView() {
    parent::loadView();
    
    $sejour = $this->loadRefSejour();
    $patient = $sejour->loadRefPatient();
    
    if ($this->seance_collective_id) {
      $this->loadRefSeanceCollective();
      $this->debut = $this->_ref_seance_collective->debut;
      $this->duree = $this->_ref_seance_collective->duree;
    }

    $this->_view = "$patient->_view - ". CMbDT::dateToLocale(CMbDT::date($this->debut));
    
    $this->loadRefsActesCdARR();
    $this->loadRefsActesCsARR();
    
    if (!$this->sejour_id) {
      $this->loadRefsEvenementsSeance();
      foreach ($this->_ref_evenements_seance as $_evt_seance) {
        $_evt_seance->loadRefSejour()->loadRefPatient();
      }
    }
  }
  
  /**
   * Charge la ligne de prescription associée
   * 
   * @return CPrescriptionLineElement
   */
  function loadRefPrescriptionLineElement() {
    /** @var CPrescriptionLineElement $line */
    $line = $this->loadFwdRef("prescription_line_element_id", true);
    
    // Prescription may not be active
    if ($line) {
      $line->loadRefElement();
    }
    
    return $this->_ref_prescription_line_element = $line;
  }
  
  /**
   * Charge le séjour associé
   * 
   * @return CSejour
   */
  function loadRefSejour() {
    return $this->_ref_sejour = $this->loadFwdRef("sejour_id", true);
  }
  
  /**
   * Charge l'équipement associé
   * 
   * @return CEquipement
   */
  function loadRefEquipement() {
    return $this->_ref_equipement = $this->loadFwdRef("equipement_id", true);
  }

  /**
   * Charge le therapeute associé
   *
   * @return CMediusers
   */
  function loadRefTherapeute() {
    return $this->_ref_therapeute = $this->loadFwdRef("therapeute_id", true);
  }

  /**
   * Charge la séance parente, dans le cas des séances collectives
   *
   * @return CEvenementSSR
   */
  function loadRefSeanceCollective() {
    return $this->_ref_seance_collective = $this->loadFwdRef("seance_collective_id", true);
  }

  /**
   * Chage les actes CdARR
   *
   * @return CActeCdARR[]
   */
  function loadRefsActesCdARR(){
    return $this->_ref_actes_cdarr = $this->loadBackRefs("actes_cdarr");
  }

  /**
   * Charge les actes CsARR
   *
   * @return CActeCsARR[]
   */
  function loadRefsActesCsARR() {
    return $this->_ref_actes_csarr = $this->loadBackRefs("actes_csarr");
  }

  /**
   * Charge tous les actes
   *
   * @return CActeSSR[][] Actes classés par type
   */
  function loadRefsActes() {
    $actes_cdarr = $this->loadRefsActesCdARR();
    $actes_csarr = $this->loadRefsActesCsARR();
    return $this->_ref_actes = array(
      "cdarr" => $actes_cdarr,
      "csarr" => $actes_csarr,
    );
  }

  /**
   * Chargement les séances filles dans les cas des séances collectives
   * Une séance fille par séjour
   *
   * @return CEvenementSSR[]
   */
  function loadRefsEvenementsSeance(){
    return $this->_ref_evenements_seance = $this->loadBackRefs("evenements_ssr");
  }

  /**
   * Charge le RHS correspondant à l'évenement
   *
   * @return CRHS
   */
  function getRHS() {
    $rhs = new CRHS();
    $rhs->sejour_id = $this->sejour_id;
    $rhs->date_monday = CMbDT::date("last monday", CMbDT::date("+1 day", CMbDT::date($this->debut)));
    $rhs->loadMatchingObject();
    
    return $rhs;
  }

  /**
   * Donne le nombre de jours d'activités visibles pour le rééducateur dans la semaine demandée
   *
   * @param ref  $user_id Identifiant de rééducateur
   * @param date $date    Jour définissant la semaine englobante
   *
   * @return int 5, 6 ou 7 jours, suivant si les samedi et/ou dimanche sont ouvrés
   */
  static function getNbJoursPlanning($user_id, $date){
    $sunday = CMbDT::date("next sunday", CMbDT::date("- 1 DAY", $date));
    $saturday = CMbDT::date("-1 DAY", $sunday);
    
    $_evt = new CEvenementSSR();
    $where = array();
    $where["debut"] = "BETWEEN '$sunday 00:00:00' AND '$sunday 23:59:59'";
    $where["therapeute_id"] = " = '$user_id'";
    $count_event_sunday = $_evt->countList($where);
    
    $nb_days = 7;
    
    // Si aucun evenement le dimanche
    if (!$count_event_sunday) {
      $nb_days = 6;
      $where["debut"] = "BETWEEN '$saturday 00:00:00' AND '$saturday 23:59:59'";
      $count_event_saturday= $_evt->countList($where);  
      // Aucun evenement le samedi et aucun le dimanche
      if (!$count_event_saturday) {
        $nb_days = 5;
      }
    }
    return $nb_days;
  }
  
  /**
   * Find all therapeutes for a patient 
   * 
   * @param ref $patient_id  Patient
   * @param ref $function_id May restrict to a function
   * 
   * @return CMediusers[]
   */
  static function getAllTherapeutes($patient_id, $function_id = null) {
    // Filter on patient
    $join["sejour"] = "sejour.sejour_id = evenement_ssr.sejour_id";
    $where["patient_id"] =  "= '$patient_id'";
    
    // Filter on function
    if ($function_id) {
      $join["users_mediboard"] = "users_mediboard.user_id = evenement_ssr.therapeute_id";
      $where["function_id"] = "= '$function_id'";
    }
    
    // Load grouped
    $group = "therapeute_id";
    $evenement = new self;
    $evenements = $evenement->loadList($where, null, null, $group, $join);
    
    // Load therapeutes
    $therapeutes = CMbObject::massLoadFwdRef($evenements, "therapeute_id");
    foreach ($therapeutes as $_therapeute) {
      $_therapeute->loadRefFunction();
    }
    
    return $therapeutes;
  }

  /**
   * Find all therapeutes having planned events 
   * 
   * @param date $min Minimal date to start from
   * @param date $max Maximal date to stop to
   * 
   * @return array[CMediusers]
   */
  static function getActiveTherapeutes($min, $max) {
    $max = CMbDT::date("+1 DAY", $max);
    $query = "SELECT DISTINCT therapeute_id FROM `evenement_ssr` 
      WHERE debut BETWEEN '$min' AND '$max'";
    $that = new self;
    $ds = $that->_spec->ds;
    $therapeute_ids = $ds->loadColumn($query);
    
    $therapeute = new CMediusers();
    return $therapeute->loadAll($therapeute_ids);
  }
  
}

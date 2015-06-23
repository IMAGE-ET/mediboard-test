<?php

/**
 * $Id: $
 *
 * @category Maternité
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */

/**
 * Gestion des grossesses d'une parturiente
 */

class CGrossesse extends CMbObject {
  // DB Table key
  public $grossesse_id;
  
  // DB References
  public $parturiente_id;
  public $group_id;

  // DB Fields
  public $terme_prevu;
  public $active;
  public $multiple;
  public $allaitement_maternel;
  public $date_dernieres_regles;
  public $lieu_accouchement;
  public $fausse_couche;
  public $rques;

  // Timings de l'accouchement, date+heure pour permettre les accouchements sur plusieurs jours (pas comme dans COperation)
  public $datetime_debut_travail;
  public $datetime_accouchement;

  /** @var CPatient */
  public $_ref_parturiente;

  /** @var CNaissance[] */
  public $_ref_naissances;

  /** @var CSejour[] */
  public $_ref_sejours = array();
  public $_nb_ref_sejours;

  /** @var CSejour|null */
  public $_ref_sejour;

  public $_ref_last_operation;

  /** @var CConsultation[] */
  public $_ref_consultations = array();
  public $_nb_ref_consultations;

  /** @var CConsultation */
  public $_ref_consultations_anesth = array();
  public $_ref_last_consult_anesth;

  /** @var  CAllaitement[] */
  public $_ref_allaitements;

  /** @var  CAllaitement */
  public $_ref_last_allaitement;

  // Form fields
  public $_praticiens;
  public $_date_fecondation;
  public $_semaine_grossesse;
  public $_terme_vs_operation;
  public $_operation_id;
  public $_allaitement_en_cours;
  public $_last_consult_id;
  public $_days_relative_acc;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'grossesse';
    $spec->key   = 'grossesse_id';

    $spec->events = array(
      "suivi" => array(
        "reference1" => array("CConsultation", "_last_consult_id"),
        "reference2" => array("CPatient", "parturiente_id"),
      ),
    );
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["parturiente_id"] = "ref notNull class|CPatient";
    $specs["group_id"]       = "ref class|CGroups";
    $specs["terme_prevu"]    = "date notNull";
    $specs["active"]         = "bool default|1";
    $specs["multiple"]       = "bool default|0";
    $specs["allaitement_maternel"] = "bool default|0";

    if (CAppUI::conf("maternite CGrossesse date_regles_obligatoire")) {
      $specs["date_dernieres_regles"] = "date notNull";
    }
    else {
      $specs["date_dernieres_regles"] = "date";
    }
    $specs["lieu_accouchement"] = "enum list|sur_site|exte default|sur_site";
    $specs["fausse_couche"]     = "enum list|inf_15|sup_15";
    $specs["rques"]             = "text helped";

    $specs["datetime_debut_travail"] = "dateTime";
    $specs["datetime_accouchement"]  = "dateTime";

    $specs["_last_consult_id"]  = "ref class|CConsultation";
    return $specs;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["naissances"] = "CNaissance grossesse_id";
    $backProps["consultations"] = "CConsultation grossesse_id";
    $backProps["sejours"] = "CSejour grossesse_id";
    $backProps["allaitements"] = "CAllaitement grossesse_id";
    return $backProps;
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    $this->loadRefParturiente();
  }

  /**
   * Chargement de la parturiente
   *
   * @return CPatient
   */
  function loadRefParturiente() {
    return $this->_ref_parturiente = $this->loadFwdRef("parturiente_id", true);
  }

  /**
   * Chargement des naissances associées à la grossesse
   *
   * @return CNaissance[]
   */
  function loadRefsNaissances() {
    return $this->_ref_naissances = $this->loadBackRefs("naissances");
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefParturiente();
    $this->_view = "Terme du " . CMbDT::dateToLocale($this->terme_prevu);
    // Nombre de semaines (aménorrhée = 41, grossesse = 39)
    $this->_date_fecondation = CMbDT::date("-41 weeks", $this->terme_prevu);
    $this->_semaine_grossesse = ceil(CMbDT::daysRelative($this->_date_fecondation, CMbDT::date()) / 7);
  }

  /**
   * Chargement des séjours associés à la grossesse
   *
   * @return CSejour[]
   */
  function loadRefsSejours() {
    return $this->_ref_sejours = $this->loadBackRefs("sejours" ,"entree_prevue DESC");
  }

  /**
   * sejour count for this grossesse
   *
   * @return int
   */
  function countRefSejours() {
    return $this->_nb_ref_sejours = $this->countBackRefs("sejours");
  }

  /**
   * Chargement des consultations associées à la grossesse
   *
   * @return CConsultation[]
   */
  function loadRefsConsultations($with_anesth = false) {
    if (!$this->_ref_consultations) {
      $this->_ref_consultations = $this->loadBackRefs("consultations", "date DESC, heure DESC", null, null, array('plageconsult' => 'plageconsult.plageconsult_id = consultation.plageconsult_id'));
    }

    if ($with_anesth) {
      /** @var CConsultation $_consultation */
      foreach ($this->_ref_consultations as $_consultation) {
        $consult_anesth = $_consultation->loadRefConsultAnesth();
        if ($consult_anesth->_id) {
          $this->_ref_consultations_anesth[$consult_anesth->_id] = $consult_anesth;
        }
      }
    }

    return $this->_ref_consultations;
  }

  /**
   * Chargement de la dernière consultation d'anesthésie pour une grossesse
   *
   * @return CConsultation
   */
  function loadLastConsultAnesth() {
    $consultations = $this->loadRefsConsultations();
    foreach ($consultations as $_consultation) {
      $consult_anesth = $_consultation->loadRefConsultAnesth();
      if ($consult_anesth->_id) {
        return $this->_ref_last_consult_anesth = $_consultation;
      }
    }
    return $this->_ref_last_consult_anesth = new CConsultation();
  }

  function loadRefLastOperation() {
    $sejour = $this->_ref_sejour;
    if ($sejour && $sejour->_id) {
      $ops = $sejour->loadRefsOperations(null, 'date DESC');
      if (count($ops)) {
        $this->_ref_last_operation = reset($ops);
      }
    }
    return $this->_ref_last_operation;
  }

  /**
   * @see parent::loadView()
   */
  function loadView() {
    parent::loadView();

    $naissances = $this->loadRefsNaissances();
    $sejours = CMbObject::massLoadFwdRef($naissances, "sejour_enfant_id");
    CMbObject::massLoadFwdRef($sejours, "patient_id");
    
    foreach ($naissances as $_naissance) {
      $_naissance->loadRefSejourEnfant()->loadRefPatient();
    }

    $this->loadLastAllaitement();
  }

  /**
   * @see parent::loadComplete()
   */
  function loadComplete(){
    parent::loadComplete();

    $this->loadLastConsult();
  }

  function getDateAccouchement() {
    if ($this->datetime_accouchement) {
      return $this->_days_relative_acc = abs(CMbDT::daysRelative(CMbDT::date($this->datetime_accouchement), CMbDT::date()));
    }

    if (count($this->_ref_naissances)) {
      /** @var CNaissance $first_naissance */
      $first_naissance = reset($this->_ref_naissances);
      if ($first_naissance->_day_relative !== null) {
        return $this->_days_relative_acc = $first_naissance->_day_relative;
      }
    }

    //@TODO : A revoir !
    if ($this->_ref_last_operation && $this->_ref_last_operation->_id) {
      return $this->_days_relative_acc = abs(CMbDT::daysRelative($this->_ref_last_operation->date, CMbDT::date()));
    }

    return null;
  }

  /**
   * Load last consult
   *
   * @return CConsultation|null
   */
  function loadLastConsult(){
    $consultations = $this->loadRefsConsultations();
    $last_consult = end($consultations);

    $this->_last_consult_id = null;

    if ($last_consult && $last_consult->_id) {
      $this->_last_consult_id = $last_consult->_id;
    }

    return $last_consult;
  }

  /**
   * @see parent::delete()
   */
  function delete() {
    $consults = $this->loadRefsConsultations();
    $sejours  = $this->loadRefsSejours();
    
    if ($msg = parent::delete()) {
      return $msg;
    }
    
    $msg = "";
    
    foreach ($consults as $_consult) {
      $_consult->grossesse_id = "";
      if ($_msg = $_consult->store()) {
        $msg .= "\n $_msg";
      }
    }
    
    
    foreach ($sejours as $_sejour) {
      $_sejour->grossesse_id = "";
      if ($_msg = $_sejour->store()) {
        $msg .= "\n $_msg";
      }
    }
    
    if ($msg) {
      return $msg;
    }

    return null;
  }

  /**
   * @see parent::store()
   */
  function store() {
    if (!$this->_id) {
      $this->group_id = CGroups::loadCurrent()->_id;
    }

    return parent::store();
  }

  function loadRefsAllaitement() {
    return $this->_ref_allaitements = $this->loadBackRefs("allaitements");
  }

  function loadLastAllaitement() {
    return $this->_ref_last_allaitement = $this->loadLastBackRef("allaitements", "date_debut DESC");
  }
}

<?php 
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPplanningOp
 * @author     Thomas Despoix <dev@openxtrem.com>
 * @version    $Revision$
 */

/**
 * Classe CSejour.
 *
 * @abstract Gère les séjours en établissement
 */
class CSejour extends CFacturable implements IPatientRelated {

  //static lists
  static $types = array("comp", "ambu",  "exte", "seances", "ssr", "psy" ,"urg", "consult");
  static $fields_etiq = array(
    "NDOS", "NRA", "DATE ENT", "HEURE ENT", "DATE SORTIE", "HEURE SORTIE",
    "PRAT RESPONSABLE", "CODE BARRE NDOS", "CHAMBRE COURANTE"
  );

  // DB Table key
  public $sejour_id;

  // Clôture des actes
  public $cloture_activite_1;
  public $cloture_activite_4;

  // DB Réference
  public $patient_id;
  public $praticien_id;
  public $group_id;
  public $grossesse_id;

  public $uf_hebergement_id; // UF de responsabilité d'hébergement
  public $uf_medicale_id; // UF de responsabilité médicale
  public $uf_soins_id; // UF de responsabilité de soins

  public $etablissement_entree_id;
  public $etablissement_sortie_id;
  public $service_entree_id; // Service d' entrée de mutation
  public $service_sortie_id; // Service de sortie de mutation

  // DB Fields
  public $type;
  public $charge_id;
  public $modalite;
  public $annule;
  public $recuse;
  public $chambre_seule;
  public $reanimation;
  public $UHCD;
  public $service_id;

  public $entree_prevue;
  public $sortie_prevue;
  public $entree_reelle;
  public $sortie_reelle;
  public $entree;
  public $sortie;

  public $entree_preparee;
  public $sortie_preparee;
  public $entree_modifiee;
  public $sortie_modifiee;

  public $DP;
  public $DR;
  public $pathologie;
  public $septique;
  public $convalescence;

  public $provenance;
  public $destination;
  public $transport; /* @todo Passer en $transport_entree */
  public $transport_sortie;
  public $rques_transport_sortie;

  public $rques;
  public $ATNC;
  public $consult_accomp;
  public $hormone_croissance;
  public $lit_accompagnant;
  public $isolement;
  public $isolement_date;
  public $isolement_fin;
  public $raison_medicale;
  public $television;
  public $repas_diabete;
  public $repas_sans_sel;
  public $repas_sans_residu;
  public $repas_sans_porc;

  public $mode_entree;
  public $mode_entree_id;
  public $mode_sortie;
  public $mode_sortie_id;

  public $confirme;
  public $prestation_id;
  public $facturable;
  public $adresse_par_prat_id;
  public $libelle;
  public $forfait_se;
  public $forfait_sd;
  public $commentaires_sortie;
  public $discipline_id;
  public $ald;
  public $type_pec;
  
  public $date_accident;
  public $nature_accident;

  // Form Fields
  public $_entree;
  public $_sortie;
  public $_duree_prevue;
  public $_duree_reelle;
  public $_duree;
  public $_date_entree_prevue;
  public $_date_sortie_prevue;
  public $_time_entree_prevue;
  public $_time_sortie_prevue;
  public $_hour_entree_prevue;
  public $_hour_sortie_prevue;
  public $_min_entree_prevue;
  public $_min_sortie_prevue;
  public $_guess_NDA;
  public $_at_midnight;
  public $_couvert_cmu;
  public $_couvert_ald;
  public $_curr_op_id;
  public $_curr_op_date;
  public $_protocole_prescription_anesth_id;
  public $_protocole_prescription_chir_id;
  public $_adresse_par;
  public $_adresse_par_prat;
  public $_etat;
  public $_entree_relative;
  public $_sortie_relative;
  public $_not_collides       = array ("urg", "consult", "seances", "exte"); // Séjour dont on ne test pas la collision
  public $_is_proche;
  public $_motif_complet;
  public $_grossesse;
  public $_nb_printers;
  public $_sejours_enfants_ids = array();
  public $_date_deces;
  public $_envoi_mail;
  public $_naissance;
  public $_isolement_date;
  public $_count_modeles_etiq;
  public $_count_tasks;
  public $_count_pending_tasks;
  public $_collisions = array();
  public $_rques_sejour;

  // Behaviour fields
  public $_check_bounds  = true;
  public $_en_mutation;
  public $_unique_lit_id;
  public $_no_synchro;
  public $_generate_NDA            = true;
  public $_skip_date_consistencies = false; // On ne check pas la cohérence des dates des consults/intervs

  // EAI Fields
  public $_eai_initiateur_group_id; // group initiateur du message EAI

  //Fields for bill
  public $_assurance_maladie;
  public $_rques_assurance_maladie;
  public $_assurance_accident;
  public $_rques_assurance_accident;
  public $_type_sejour;
  public $_statut_pro;
  public $_dialyse;
  public $_cession_creance;
  
  // Object References
  /** @var CPatient Patient */
  public $_ref_patient; // Declared in CCodable

  /** @var CMediusers */
  public $_ref_praticien;

  /** @var COperation[] */
  public $_ref_operations;

  /** @var COperation */
  public $_ref_last_operation;
  /** @var  CAffectation[] */
  public $_ref_affectations;
  /** @var  CAffectation */
  public $_ref_first_affectation;
  /** @var  CAffectation */
  public $_ref_last_affectation;
  /** @var  CAffectation */
  public $_ref_curr_affectation;
  public $_ref_GHM = array();
  public $_ref_group;
  public $_ref_etablissement_transfert;
  public $_ref_etablissement_provenance;
  public $_ref_service_mutation;
  public $_ref_dossier_medical;
  public $_ref_rpu;
  /** @var CBilanSSR */
  public $_ref_bilan_ssr;
  /** @var CFicheAutonomie */
  public $_ref_fiche_autonomie;
  /** @var CConsultAnesth */
  public $_ref_consult_anesth;
  /** @var CConsultation[] */
  public $_ref_consultations;
  public $_ref_consult_atu;
  public $_ref_prescriptions;
  public $_ref_last_prescription;
  public $_ref_NDA;
  public $_ref_NPA;
  public $_ref_NRA;
  public $_ref_prescripteurs;
  public $_ref_adresse_par_prat;
  /** @var CPrescription */
  public $_ref_prescription_sejour;
  public $_ref_replacements;
  public $_ref_replacement;
  public $_ref_tasks;
  public $_ref_tasks_not_created;
  public $_ref_transmissions;
  public $_ref_observations;
  public $_ref_hl7_movement;
  public $_ref_hl7_affectation;
  public $_ref_grossesse;
  public $_ref_curr_operation;
  public $_ref_curr_operations;
  public $_ref_exams_igs;
  public $_ref_charge_price_indicator; // Type d'activité
  public $_ref_movements;
  public $_ref_mode_entree;
  public $_ref_mode_sortie;
  public $_ref_factures;
  public $_ref_last_facture;
  public $_ref_prestation;

  // External objects
  public $_ext_diagnostic_principal;
  public $_ext_diagnostic_relie;
  public $_ref_echange_hprim;

  // Distant fields
  public $_dates_operations;
  public $_dates_consultations;
  public $_codes_ccam_operations;
  public $_NDA; // Numéro Dossier Administratif
  public $_NDA_view; // Vue du NDA
  public $_NPA; // Numéro Pré-Admission
  public $_list_constantes_medicales;
  public $_cancel_alerts;
  public $_ref_suivi_medical;
  public $_diagnostics_associes;
  public $_ref_prestations;
  public $_liaisons_for_prestation;
  public $_first_liaison_for_prestation;

  // Filter Fields
  public $_date_min;
  public $_date_max;
  public $_date_entree;
  public $_date_sortie;
  public $_horodatage;
  public $_admission;
  public $_service;
  public $_type_admission;
  public $_specialite;
  public $_date_min_stat;
  public $_date_max_stat;
  public $_filter_type;
  public $_ccam_libelle;
  public $_coordonnees;

  // Object tool field
  public $_modifier_sortie;
  public $_modifier_entree;

  function CSejour() {
    parent::__construct();
    $this->_locked = CAppUI::conf("dPplanningOp CSejour locked");
  }

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'sejour';
    $spec->key   = 'sejour_id';
    $spec->measureable = true;
    $spec->events = array(
      "suivi_clinique" => array(
        "reference1" => array("CMediusers", "praticien_id"),
        "reference2" => array("CPatient",   "patient_id"),
      ),
    );
    return $spec;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["affectations"]          = "CAffectation sejour_id";
    $backProps["bilan_ssr"]             = "CBilanSSR sejour_id";
    $backProps["consultations_anesths"] = "CConsultAnesth sejour_id";
    $backProps["consultations"]         = "CConsultation sejour_id";
    $backProps["fiche_autonomie"]       = "CFicheAutonomie sejour_id";
    $backProps["GHM"]                   = "CGHM sejour_id";
    $backProps["hprim21_sejours"]       = "CHprim21Sejour sejour_id";
    $backProps["observations"]          = "CObservationMedicale sejour_id";
    $backProps["operations"]            = "COperation sejour_id";
    $backProps["prescriptions"]         = "CPrescription object_id";
    $backProps["rpu"]                   = "CRPU sejour_id";
    $backProps["rpu_mute"]              = "CRPU mutation_sejour_id";
    $backProps["transmissions"]         = "CTransmissionMedicale sejour_id";
    $backProps["dossier_medical"]       = "CDossierMedical object_id";
    $backProps["ghm"]                   = "CGHM sejour_id";
    $backProps["planifications"]        = "CPlanificationSysteme sejour_id";
    $backProps["rhss"]                  = "CRHS sejour_id";
    $backProps["evenements_ssr"]        = "CEvenementSSR sejour_id";
    $backProps["replacements"]          = "CReplacement sejour_id";
    $backProps["echanges_hprim"]        = "CEchangeHprim object_id";
    $backProps["echanges_hprim21"]      = "CEchangeHprim21 object_id";
    $backProps["echanges_ihe"]          = "CExchangeIHE object_id";
    $backProps["tasks"]                 = "CSejourTask sejour_id";
    $backProps["sejour_brancard"]       = "CBrancardage sejour_id";
    $backProps["naissance"]             = "CNaissance sejour_enfant_id";
    $backProps["naissances"]            = "CNaissance sejour_maman_id";
    $backProps["movements"]             = "CMovement sejour_id";
    $backProps["items_liaisons"]        = "CItemLiaison sejour_id";
    $backProps["exams_igs"]             = "CExamIgs sejour_id";
    $backProps["ufs"]                   = "CAffectationUniteFonctionnelle object_id";
    $backProps["actes_cdarr"]           = "CActeCdARR sejour_id";
    $backProps["actes_csarr"]           = "CActeCsARR sejour_id";
    $backProps["poses_disp_vasc"]       = "CPoseDispositifVasculaire sejour_id";
    $backProps["deliveries"]            = "CProductDelivery sejour_id";
    $backProps["stock_sejour"]          = "CStockSejour sejour_id";
    $backProps["refus_dispensation"]    = "CRefusDispensation sejour_id";
    return $backProps;
  }

  function getProps() {
    $props = parent::getProps();
    $props["patient_id"]               = "ref notNull class|CPatient seekable";
    $props["praticien_id"]             = "ref notNull class|CMediusers seekable";
    $props["group_id"]                 = "ref notNull class|CGroups";
    $props["grossesse_id"]             = "ref class|CGrossesse unlink";
    $props["uf_hebergement_id"]        = "ref class|CUniteFonctionnelle seekable";
    $props["uf_medicale_id"]           = "ref class|CUniteFonctionnelle seekable";
    $props["uf_soins_id"]              = "ref class|CUniteFonctionnelle seekable";
    $props["type"]                     = "enum notNull list|".implode("|", self::$types)." default|ambu";
    $props["charge_id"]                = "ref class|CChargePriceIndicator autocomplete|libelle show|0";
    $props["modalite"]                 = "enum notNull list|office|libre|tiers default|libre show|0";
    $props["annule"]                   = "bool show|0";
    $props["recuse"]                   = "enum list|-1|0|1 default|0 show|0";
    $props["chambre_seule"]            = "bool notNull show|0 default|".(CGroups::loadCurrent()->chambre_particuliere ? 1 : 0);
    $props["reanimation"]              = "bool default|0";
    $props["UHCD"]                     = "bool default|0";
    $props["service_id"]               = "ref".(CAppUI::conf("dPplanningOp CSejour service_id_notNull") == 1 ? ' notNull' : '')." class|CService seekable";
    $props["entree_prevue"]            = "dateTime notNull show|0";
    $props["sortie_prevue"]            = "dateTime notNull moreEquals|entree_prevue show|0";
    $props["entree_reelle"]            = "dateTime show|0";
    $props["sortie_reelle"]            = "dateTime moreEquals|entree_reelle show|0";
    $props["entree"]                   = "dateTime derived show|0";
    $props["sortie"]                   = "dateTime moreEquals|entree derived show|0";
    $props["entree_preparee"]          = "bool";
    $props["sortie_preparee"]          = "bool";
    $props["entree_modifiee"]          = "bool";
    $props["sortie_modifiee"]          = "bool";
    $props["DP"]                       = "code cim10 show|0";
    $props["DR"]                       = "code cim10 show|0";
    $props["pathologie"]               = "str length|3 show|0";
    $props["septique"]                 = "bool show|0";
    $props["convalescence"]            = "text helped";
    $props["rques"]                    = "text helped";
    $props["ATNC"]                     = "bool show|0";
    $props["consult_accomp"]           = "enum list|oui|non|nc default|nc";
    $props["hormone_croissance"]       = "bool";
    $props["lit_accompagnant"]         = "bool";
    $props["isolement"]                = "bool";
    $props["isolement_date"]           = "dateTime";
    $props["isolement_fin"]            = "dateTime";
    $props["raison_medicale"]          = "text helped";
    $props["television"]               = "bool";

    $props["repas_diabete"]            = "bool";
    $props["repas_sans_sel"]           = "bool";
    $props["repas_sans_residu"]        = "bool";
    $props["repas_sans_porc"]          = "bool";

    $props["mode_entree"]              = "enum list|8|7|6";
    $props["mode_entree_id"]           = "ref class|CModeEntreeSejour autocomplete|libelle|true";
    $props["mode_sortie"]              = "enum list|normal|transfert|mutation|deces";
    $props["mode_sortie_id"]           = "ref class|CModeSortieSejour autocomplete|libelle|true";

    $props["confirme"]                 = "bool";
    $props["prestation_id"]            = "ref class|CPrestation";
    $props["facturable"]               = "bool notNull default|1 show|0";
    $props["etablissement_sortie_id"]  = "ref class|CEtabExterne autocomplete|nom";
    $props["etablissement_entree_id"]  = "ref class|CEtabExterne autocomplete|nom";
    $props["service_entree_id"]        = "ref class|CService autocomplete|nom dependsOn|group_id|cancelled";
    $props["service_sortie_id"]        = "ref class|CService autocomplete|nom dependsOn|group_id|cancelled";
    $props["adresse_par_prat_id"]      = "ref class|CMedecin";
    $props["libelle"]                  = "str seekable autocomplete dependsOn|praticien_id";
    $props["facture"]                  = "bool default|0";
    $props["forfait_se"]               = "bool default|0";
    $props["forfait_sd"]               = "bool default|0";
    $props["commentaires_sortie"]      = "text helped";
    $props["discipline_id"]            = "ref class|CDisciplineTarifaire autocomplete|description show|0";
    $props["ald"]                      = "bool default|0";

    $props["provenance"]               = "enum list|1|2|3|4|5|6|7|8";
    $props["destination"]              = "enum list|1|2|3|4|6|7";
    $props["transport"]                = "enum list|perso|perso_taxi|ambu|ambu_vsl|vsab|smur|heli|fo";
    $props["transport_sortie"]         = "enum list|perso|perso_taxi|ambu|ambu_vsl|vsab|smur|heli|fo";
    $props["rques_transport_sortie"]   = "text";
    $props["type_pec"]                 = "enum list|M|C|O";

    $props["date_accident"]            = "date";
    $props["nature_accident"]          = "enum list|P|T|D|S|J|C|L|B|U";

    // Clôture des actes
    $props["cloture_activite_1"]    = "bool default|0";
    $props["cloture_activite_4"]    = "bool default|0";

    $props["_rques_assurance_maladie"]  = "text helped";
    $props["_rques_assurance_accident"] = "text helped";
    $props["_assurance_maladie"]        = "ref class|CCorrespondantPatient";
    $props["_assurance_accident"]       = "ref class|CCorrespondantPatient";
    $props["_type_sejour"]              = "enum list|maladie|accident default|maladie";
    $props["_dialyse"]                  = "bool default|0";
    $props["_cession_creance"]          = "bool default|0";
    $props["_statut_pro"]               = "enum list|chomeur|etudiant|non_travailleur|independant|invalide|militaire|retraite|salarie_fr|salarie_sw|sans_emploi";
    
    $props["_time_entree_prevue"] = "time";
    $props["_time_sortie_prevue"] = "time";

    $props["_entree"]           = "dateTime show";
    $props["_sortie"]           = "dateTime show";
    $props["_date_entree"]      = "date";
    $props["_date_sortie"]      = "date";
    $props["_date_min"]         = "dateTime";
    $props["_date_max"]         = "dateTime moreEquals|_date_min";
    $props["_horodatage"]       = "enum list|entree_prevue|entree_reelle|sortie_prevue|sortie_reelle";
    $props["_admission"]        = "text";
    $props["_service"]          = "text";
    $props["_type_admission"]   = "enum notNull list|ambucomp|comp|ambu|exte|seances|ssr|psy|urg|consult default|ambu";
    $props["_specialite"]       = "text";
    $props["_date_min_stat"]    = "date";
    $props["_date_max_stat"]    = "date moreEquals|_date_min_stat";
    $props["_filter_type"]      = "enum list|comp|ambu|exte|seances|ssr|psy|urg|consult";
    $props["_NDA"]              = "str show|1";
    $props["_ccam_libelle"]     = "bool default|0";
    $props["_coordonnees"]      = "bool default|0";
    $props["_adresse_par"]      = "bool";
    $props["_adresse_par_prat"] = "str";
    $props["_etat"]             = "enum list|preadmission|encours|cloture";

    $props["_duree_prevue"]                     = "num";
    $props["_duree_reelle"]                     = "num";
    $props["_duree"]                            = "num";
    $props["_date_entree_prevue"]               = "date";
    $props["_date_sortie_prevue"]               = "date moreEquals|_date_entree_prevue";
    $props["_protocole_prescription_anesth_id"] = "str";
    $props["_protocole_prescription_chir_id"]   = "str";
    $props["_motif_complet"]                    = "str";
    $props["_unique_lit_id"]    = "ref class|CLit";
    $props["_date_deces"]       = "date progressive";
    $props["_isolement_date"]   = "dateTime";
    return $props;
  }

  function loadRelPatient() {
    return $this->loadRefPatient();
  }

  function check() {
    // Has to be done first to check and repair fields before further checking
    if ($msg = parent::check()) {
      return $msg;
    }

    $pathos = new CDiscipline();

    // Test de la pathologies
    if ($this->pathologie != null && (!in_array($this->pathologie, $pathos->_specs["categorie"]->_list))) {
      return "Pathologie non disponible";
    }

    // Test de coherence de date avec les interventions
    if ($this->_check_bounds) {
      $this->completeField("entree_prevue");
      $this->completeField("sortie_prevue");
      $entree = $this->entree_prevue;
      $sortie = $this->sortie_prevue;

      if ($entree !== null && $sortie !== null  && !$this->_skip_date_consistencies) {
        $entree = CMbDT::date($entree);
        $sortie = CMbDT::date($sortie);
        $this->makeDatesOperations();
        if (!$this->entree_reelle) {
          foreach ($this->_dates_operations as $operation_id => $date_operation) {
            if ($this->_curr_op_id == $operation_id) {
              $date_operation = $this->_curr_op_date;
            }

            if (!CMbRange::in($date_operation, $entree, $sortie)) {
               return "Intervention du '$date_operation' en dehors des nouvelles dates du séjour du '$entree' au '$sortie'";
            }
          }
        }


        if (!$this->entree_reelle && $this->type == "consult") {
          $this->makeDatesConsultations();
          foreach ($this->_dates_consultations as $consultation_id => $date_consultation) {
            if (!CMbRange::in($date_consultation, $entree, $sortie)) {
              return "Consultations en dehors des nouvelles dates du séjour.";
            }
          }
        }
      }

      $this->completeField("entree_reelle", "annule");
      if ($this->fieldModified("annule", "1")) {
        $max_cancel_time = CAppUI::conf("dPplanningOp CSejour max_cancel_time");
        if ((CMbDT::dateTime("+ $max_cancel_time HOUR", $this->entree_reelle) < CMbDT::dateTime())) {
           return "Impossible d'annuler un dossier ayant une entree réelle depuis plus de $max_cancel_time heures.<br />";
        }
      }

      if (!$this->_merging && !$this->_forwardRefMerging) {
        foreach ($this->getCollisions() as $collision) {
          return "Collision avec le séjour du '$collision->entree' au '$collision->sortie'";
        }
      }
    }
  }

  /**
   * Cherche les différentes collisions au séjour courant
   *
   * @return array|CSejour
   */
  function getCollisions() {
    $collisions = array();

    // Ne concerne pas les annulés
    $this->completeField("annule", "type", "group_id", "patient_id");
    if ($this->annule || in_array($this->type, $this->_not_collides)) {
      return $collisions;
    }

    // Données incomplètes
    if (!$this->entree || !$this->sortie) {
      return $collisions;
    }

    // Test de colision avec un autre sejour
    $patient = new CPatient;
    $patient->load($this->patient_id);
    if (!$patient->_id) {
      return $collisions;
    }

    // Chargement des autres séjours
    $where["annule"] = " = '0'";
    $where["group_id"] = " = '".$this->group_id."'";
    foreach ($this->_not_collides as $_type_not_collides) {
      $where[] = "type != '$_type_not_collides'";
    }

    $patient->loadRefsSejours($where);
    $sejours = $patient->_ref_sejours;

    // Collision sur chacun des autres séjours
    foreach ($sejours as $sejour) {
      if ($sejour->_id != $this->_id && $this->collides($sejour)) {
        $collisions[$sejour->_id] = $sejour;
      }
    }

    return $this->_collisions = $collisions;
  }

  /**
   * Cherche des séjours les dates d'entrée ou sortie sont proches,
   * pour le même patient dans le même établissement
   *
   * @param int  $tolerance Tolérance en heures
   * @param bool $use_type  Matche sur le type de séjour aussi
   *
   * @return CSejour[]
   */
  function getSiblings($tolerance = 1, $use_type = false) {
    $sejour = new CSejour;
    $sejour->patient_id = $this->patient_id;
    $sejour->group_id   = $this->group_id;

    // Si on veut rechercher pour un type de séjour donné
    if ($use_type) {
      $sejour->type   = $this->type;
    }

    $siblings = $sejour->loadMatchingList();

    $this->updateFormFields();

    // Entree et sortie ne sont pas forcément stored
    $entree = $this->entree_reelle ? $this->entree_reelle : $this->entree_prevue;
    $sortie = $this->sortie_reelle ? $this->sortie_reelle : $this->sortie_prevue;

    foreach ($siblings as $_sibling) {
      if ($_sibling->_id == $this->_id) {
        unset($siblings[$_sibling->_id]);
        continue;
      }

      $entree_relative = abs(CMbDT::hoursRelative($entree, $_sibling->entree));
      $sortie_relative = abs(CMbDT::hoursRelative($sortie, $_sibling->sortie));
      if ($entree_relative > $tolerance && $sortie_relative > $tolerance) {
        unset($siblings[$_sibling->_id]);
      }
    }

    return $siblings;
  }

  /**
   * Check is the object collide another
   *
   * @param CSejour $sejour                 Sejour
   * @param bool    $collides_update_sejour Launch updateFormFields
   *
   * @return boolean
   */
  function collides(CSejour $sejour, $collides_update_sejour = true) {

    if ($this->_id && $sejour->_id && $this->_id == $sejour->_id) {
      return false;
    }

    if ($this->annule || $sejour->annule) {
      return false;
    }

    if (in_array($this->type, $this->_not_collides) || in_array($sejour->type, $this->_not_collides)) {
      return false;
    }

    if ($this->group_id != $sejour->group_id) {
      return false;
    }

    if ($collides_update_sejour) {
      $this->updateFormFields();
    }

    switch ($this->conf("check_collisions")) {
      case "no":
        return;
      case "date":
        $lower1 = CMbDT::date($this->entree);
        $upper1 = CMbDT::date($this->sortie);
        $lower2 = CMbDT::date($sejour->entree);
        $upper2 = CMbDT::date($sejour->sortie);
        break;
      case "datetime":
        $lower1 = $this->entree;
        $upper1 = $this->sortie;
        $lower2 = $sejour->entree;
        $upper2 = $sejour->sortie;
        break;
    }

    return CMbRange::collides($lower1, $upper1, $lower2, $upper2, false);
  }

  function applyProtocolesPrescription($operation_id = null) {
    if (!$this->_protocole_prescription_chir_id) {
      return;
    }

    // Application du protocole de prescription
    $prescription = new CPrescription;
    $prescription->object_class = $this->_class;
    $prescription->object_id = $this->_id;
    $prescription->type = "sejour";
    if ($msg = $prescription->store()) {
      return $msg;
    }

    /*
    if ($this->_protocole_prescription_anesth_id) {
      $prescription->applyPackOrProtocole($this->_protocole_prescription_anesth_id, $this->praticien_id, CMbDT::date(), null, $operation_id);
    }
    */
    if ($this->_protocole_prescription_chir_id) {
      $prescription->_dhe_mode = true;
      $prescription->applyPackOrProtocole($this->_protocole_prescription_chir_id, $this->praticien_id, CMbDT::date(), null, $operation_id, null);
    }
  }

  /**
   * check for a sectorisation rules to find a service_id
   * if 0 rules, no work
   * if 1 rule, => service_id of the rule
   * if more than rules are found, if they all lead to the same service, we use this service
   *
   * @return bool
   */
  function getServiceFromSectorisationRules() {
    if (!CAppUI::conf("dPplanningOp CRegleSectorisation use_sectorisation") || $this->service_id) {
      return false;
    }
    $this->completeField("type", "praticien_id", "entree", "sortie", "group_id", "type_pec");
    $this->updatePlainFields(); // make sure entree & sortie well defined
    $praticien = $this->loadRefPraticien();

    $where = array();
    $where["type_admission"] = " = '$this->type' OR `type_admission` IS NULL";
    $where["praticien_id"] = " = '$this->praticien_id' OR `praticien_id` IS NULL";
    $where["function_id"] = " = '$praticien->function_id' OR `function_id` IS NULL";
    $where["date_min"] = " <= '$this->entree' OR `date_min` IS NULL";
    $where["date_max"] = " >= '$this->entree' OR `date_max` IS NULL";
    $where["group_id"] = " = '$this->group_id'";

    if ($this->type_pec) {
      $where["type_pec"] = " = '$this->type_pec' OR `type_pec` IS NULL";
    }

    $duree = CMbDT::daysRelative($this->entree, $this->sortie);
    $where["duree_min"] = " <= '$duree' OR `duree_min` IS NULL";
    $where["duree_max"] = " >= '$duree' OR `duree_max` IS NULL";

    $regle = new CRegleSectorisation();
    $regles = $regle->loadList($where);
    $count  = count($regles);

    //no result, no work
    if ($count == 0) {
      CAppUI::setMsg("CRegleSectorisation-no-rules-found", UI_MSG_WARNING);
      return false;
    }

    // one or more rules, lets do the work
    if ($count > 0) {
      //check if all result = same service
      $first = reset($regles);
      $result = 0;
      foreach ($regles as $_regle) {
        if ($_regle->service_id == $first->service_id) {
          $result++;
        }
      }

      //too much results & differents result
      if ($result != $count) {
        CAppUI::setMsg("CRegleSectorisation-number%d-rules-unable-to-resolve", UI_MSG_WARNING, $count);
        return false;
      }

      $first->loadRefService();
      $this->service_id = $first->service_id;
      CAppUI::setMsg("CRegleSectorisation-rule%d-rule%s", UI_MSG_OK, $count, $first->_ref_service->nom);
      return true;
    }

  }

  /**
   * Object store in DB
   *
   * @return null|string|void
   */
  function store() {
    $this->completeField("entree_reelle", "entree", "patient_id", "type_pec");

    // Sectorisation Rules
    $this->getServiceFromSectorisationRules();

    // Vérification de la validité des codes CIM
    if ($this->DP != null) {
      $dp = new CCodeCIM10($this->DP, 1);
      if (!$dp->exist) {
        CAppUI::setMsg("Le code CIM saisi n'est pas valide", UI_MSG_WARNING);
        $this->DP = "";
      }
    }
    if ($this->DR != null) {
      $dr = new CCodeCIM10($this->DR, 1);
      if (!$dr->exist) {
        CAppUI::setMsg("Le code CIM saisi n'est pas valide", UI_MSG_WARNING);
        $this->DR = "";
      }
    }

    // Annulation de l'établissement de transfert si le mode de sortie n'est pas transfert
    if (null !== $this->mode_sortie) {
      if ("transfert" != $this->mode_sortie) {
        $this->etablissement_sortie_id = "";
      }
      if ("mutation" != $this->mode_sortie) {
        $this->service_sortie_id = "";
      }
    }

    // Mise à jour du type PEC si vide
    if (!$this->_id && !$this->type_pec) {
      $this->type_pec = ($this->grossesse_id ? "O" : "M");
    }

    // Annulation de la sortie réelle si on annule le mode de sortie
    if ($this->mode_sortie === "") {
      $this->sortie_reelle = "";
    }

    // Annulation de l'établissement de provenance si le mode d'entrée n'est pas transfert
    if ($this->fieldModified("mode_entree")) {
      if ("7" != $this->mode_entree) {
        $this->etablissement_entree_id = "";
      }

      if ("6" != $this->mode_entree) {
        $this->service_entree_id = "";
      }
    }

    // Passage au mode transfert si on value un établissement de provenance
    if ($this->fieldModified("etablissement_entree_id")) {
      if ($this->etablissement_entree_id != null) {
        $this->mode_entree = 7;
      }
    }

    // Passage au mode mutation si on value un service de provenance
    if ($this->fieldModified("service_entree_id")) {
      if ($this->service_entree_id != null) {
        $this->mode_entree = 6;
      }
    }

    $patient_modified = $this->fieldModified("patient_id");

    // Pour un séjour non annulé, mise à jour de la date de décès du patient
    // suivant le mode de sortie
    if (!$this->annule) {
      $patient = new CPatient;
      $patient->load($this->patient_id);

      if ("deces" === $this->mode_sortie) {
        $patient->deces = $this->_date_deces;
      }
      else {
        $patient->deces = "";
      }

      // On verifie que le champ a été modifié pour faire le store (sinon probleme lors de la fusion de patients)
      if ($patient->fieldModified("deces")) {
        // Ne pas faire de return $msg ici, car ce n'est pas "bloquant"
        $patient->store();
      }
    }

    // Si annulation possible que par le chef de bloc
    if (CAppUI::conf("dPplanningOp COperation cancel_only_for_resp_bloc") &&
      $this->fieldModified("annule", 1) &&
      $this->entree_reelle &&
      !CModule::getCanDo("dPbloc")->edit) {
      foreach ($this->loadRefsOperations() as $_operation) {
        if ($_operation->rank) {
          CAppUI::setMsg("Impossible de sauvegarder : une des interventions du séjour est validée.\nContactez le responsable de bloc", UI_MSG_ERROR);
          return;
        }
      }
    }
    
    // On fixe la récusation si pas définie pour un nouveau séjour
    if (!$this->_id && ($this->recuse === "" || $this->recuse === null)) {
      $this->recuse = CAppUI::conf("dPplanningOp CSejour use_recuse") ? -1 : 0;
    }

    // Si gestion en mode expert de l'isolement
    if (CAppUI::conf("dPplanningOp CSejour systeme_isolement") == "expert") {
      $this->isolement_date =
        $this->_isolement_date !== $this->entree && $this->isolement ?
        $this->_isolement_date : "";
      if (!$this->isolement) {
        $this->isolement_fin = "";
      }
    }
    
    $facture = null;
    if (CModule::getActive("dPfacturation") && CAppUI::conf("dPplanningOp CFactureEtablissement use_facture_etab")) {
      // Création de la facture de sejour 
      $this->loadRefsFactureEtablissement();
      $facture = $this->_ref_last_facture;
      if (!$facture->_id) {
        $facture->ouverture         = CMbDT::date();
      }
      if (CAppUI::conf("dPfacturation CFactureEtablissement use_temporary_bill")) {
        $facture->temporaire = 1;
      }
      $facture->patient_id          = $this->patient_id;
      $facture->praticien_id        = $this->praticien_id;
      $facture->type_facture        = $this->_type_sejour;
      $facture->dialyse             = $this->_dialyse;
      $facture->cession_creance     = $this->_cession_creance;
      $facture->statut_pro          = $this->_statut_pro;
      $facture->assurance_maladie   = $this->_assurance_maladie;
      $facture->assurance_accident  = $this->_assurance_accident;
      $facture->rques_assurance_accident = $this->_rques_assurance_accident;
      $facture->rques_assurance_maladie  = $this->_rques_assurance_maladie;
      
      //Store de la facture 
      if ($msg = $facture->store()) {
        return $msg;
      }
    }
    
    $this->completeField("mode_entree_id");
    if ($this->mode_entree_id) {
      $mode = $this->loadFwdRef("mode_entree_id");
      $this->mode_entree = $mode->mode;
    }

    $this->completeField("mode_sortie_id");
    if ($this->mode_sortie_id) {
      $mode = $this->loadFwdRef("mode_sortie_id");
      $this->mode_sortie = $mode->mode;
    }

    // Gestion du tarif et precodage des actes
    if ($this->_bind_tarif && $this->_id) {
      if ($msg = $this->bindTarif()) {
        return $msg;
      }
    }

    // On fait le store du séjour
    if ($msg = parent::store()) {
      return $msg;
    }

    if (CModule::getActive("dPfacturation") && CAppUI::conf("dPplanningOp CFactureEtablissement use_facture_etab")) {
      if (count($this->_ref_factures) ==0) {
        $liaison = new CFactureLiaison();
        $liaison->object_id     = $this->_id;
        $liaison->object_class  = $this->_class;
        $liaison->facture_id    = $facture->_id;
        $liaison->facture_class = "CFactureEtablissement";
        //Store de la table de liaison entre facture et séjour
        if ($msg = $liaison->store()) {
          return $msg;
        }
      }
    }
    
    if ($patient_modified) {
      $consultations = $this->loadBackRefs("consultations");
      foreach ($consultations as $_consult) {
        if ($_consult->patient_id != $this->patient_id) {
          $_consult->patient_id = $this->patient_id;
          if ($msg = $_consult->store()) {
            CAppUI::setMsg($msg, UI_MSG_WARNING);
          }
        }
      }
    }

    // Cas d'une annulation de séjour
    if ($this->annule) {
      if ($msg = $this->delAffectations()) {
        return $msg;
      }
      if ($msg = $this->cancelOperations()) {
        return $msg;
      }
    }

    // Synchronisation des affectations
    if (!$this->_no_synchro && !($this->type == "seances")) {
      $this->loadRefsAffectations();
      $firstAff =& $this->_ref_first_affectation;
      $lastAff =& $this->_ref_last_affectation;

      // Cas où on a une premiere affectation différente de l'heure d'admission
      if ($firstAff->_id && ($firstAff->entree != $this->_entree)) {
        $firstAff->entree = $this->_entree;
        $firstAff->_no_synchro = 1;
        $firstAff->store();
      }

      // Cas où on a une dernière affectation différente de l'heure de sortie
      if ($lastAff->_id && ($lastAff->sortie != $this->_sortie)) {
        $lastAff->sortie = $this->_sortie;
        $lastAff->_no_synchro = 1;
        $lastAff->store();
      }

      //si le sejour a une sortie ==> compléter le champ effectue de la derniere affectation
      if ($lastAff->_id) {
        $this->_ref_last_affectation->effectue = $this->sortie_reelle ? 1 : 0;
        $this->_ref_last_affectation->store();
      }
    }

    // Unique affectation de lit
    if ($this->_unique_lit_id) {
      // Une affectation maximum
      if (count($this->_ref_affectations) > 1) {
        foreach ($this->_ref_affectations as $_affectation) {
          if ($msg = $_affectation->delete()) {
            return "Impossible de supprimer une ancienne affectation: $msg";
          }
        }
      }

      // Affectation unique sur le lit
      $this->loadRefsAffectations();
      $unique = $this->_ref_first_affectation;
      $unique->sejour_id = $this->_id;
      $unique->entree = $this->_entree;
      $unique->sortie = $this->_sortie;
      $unique->lit_id = $this->_unique_lit_id;
      if ($msg = $unique->store()) {
        return "Impossible d'affecter un lit unique: $msg";
      }
    }

    // Génération du NDA ?
    if ($this->_generate_NDA) {
      if ($msg = $this->generateNDA()) {
        return $msg;
      }
    }
  }
  
  function loadRefsFactureEtablissement(){
    if (CModule::getActive("dPfacturation") && CAppUI::conf("dPplanningOp CFactureEtablissement use_facture_etab")) {
      $ljoin = array();
      $ljoin["facture_liaison"] = "facture_liaison.facture_id = facture_etablissement.facture_id";
      $where = array();
      $where["facture_liaison.facture_class"]    = " = 'CFactureEtablissement'";
      $where["facture_liaison.object_class"] = " = 'CSejour'";
      $where["facture_liaison.object_id"]    = " = '$this->_id'";
      
      $facture = new CFactureEtablissement();
      $this->_ref_factures = $facture->loadList($where, "ouverture ASC", null, null, $ljoin);
     if (count($this->_ref_factures) > 0) {
        $this->_ref_last_facture = end($this->_ref_factures);
        $this->_ref_last_facture->loadRefsReglements();
      }
      else {
        $this->_ref_last_facture = new CFactureEtablissement();
      }
      return $this->_ref_factures;
    }
  }
  
  function generateNDA() {
    $group = CGroups::loadCurrent();
    if (!$group->isNDASupplier()) {
      return;
    }

    $this->loadNDA($group->_id);
    if ($this->_NDA) {
      return;
    }

    if (!$NDA = CIncrementer::generateIdex($this, self::getTagNDA($group->_id), $group->_id)) {
      return CAppUI::tr("CIncrementer_undefined");
    }
  }

  function delAffectations() {
    $this->loadRefsAffectations();

    $msg = null;
    // dPhospi might not be active
    if ($this->_ref_affectations) {
      foreach ($this->_ref_affectations as $key => $value) {
        $msg .= $this->_ref_affectations[$key]->deleteOne();
      }
    }

    return $msg;
  }

  function cancelOperations() {
    $this->loadRefsOperations();

    $msg = null;
    foreach ($this->_ref_operations as $key => $value) {
      $value->annulee = 1;
      $msg .= $this->_ref_operations[$key]->store();
    }

    return $msg;
  }

  function getActeExecution() {
    $this->updateFormFields();
  }

  function updateEntreeSortie() {
    $this->_entree = CValue::first($this->entree_reelle, $this->entree_prevue);
    $this->_sortie = CValue::first($this->sortie_reelle, $this->sortie_prevue);
  }

  function updateIsolement() {
    $this->_isolement_date = CValue::first($this->isolement_date, $this->_entree);
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->updateEntreeSortie();

    if (CAppUI::conf("dPplanningOp CSejour systeme_isolement") == "expert") {
      $this->updateIsolement();
    }

    // Durées
    $this->_duree_prevue       = CMbDT::daysRelative($this->entree_prevue, $this->sortie_prevue);
    $this->_duree_reelle       = CMbDT::daysRelative($this->entree_reelle, $this->sortie_reelle);
    $this->_duree              = CMbDT::daysRelative($this->_entree, $this->_sortie);

    // Dates
    $this->_date_entree_prevue = CMbDT::date(null, $this->entree_prevue);
    $this->_date_sortie_prevue = CMbDT::date(null, $this->sortie_prevue);

    // Horaires
    // @todo: A supprimer
    $this->_time_entree_prevue = CMbDT::transform(null, $this->entree_prevue, "%H:%M:00");
    $this->_time_sortie_prevue = CMbDT::transform(null, $this->sortie_prevue, "%H:%M:00");
    $this->_hour_entree_prevue = CMbDT::transform(null, $this->entree_prevue, "%H");
    $this->_hour_sortie_prevue = CMbDT::transform(null, $this->sortie_prevue, "%H");
    $this->_min_entree_prevue  = CMbDT::transform(null, $this->entree_prevue, "%M");
    $this->_min_sortie_prevue  = CMbDT::transform(null, $this->sortie_prevue, "%M");

    switch (CAppUI::conf("dPpmsi systeme_facturation")) {
      case "siemens" :
        $this->_guess_NDA = CMbDT::transform(null, $this->entree_prevue, "%y");
        $this->_guess_NDA .=
          $this->type == "exte" ? "5" :
          $this->type == "ambu" ? "4" : "0";
        $this->_guess_NDA .="xxxxx";
        break;
      default:
        $this->_guess_NDA = "-";
    }
    $this->_at_midnight = ($this->_date_entree_prevue != $this->_date_sortie_prevue);

    if ($this->entree_prevue && $this->sortie_prevue) {
      $this->_view      = "Séjour du " . CMbDT::transform(null, $this->_entree, CAppUI::conf("date"));
      $this->_shortview = "Du "        . CMbDT::transform(null, $this->_entree, CAppUI::conf("date"));
      if (CMbDT::transform(null, $this->_entree, CAppUI::conf("date")) != CMbDT::transform(null, $this->_sortie, CAppUI::conf("date"))) {
        $this->_view      .= " au " . CMbDT::transform(null, $this->_sortie, CAppUI::conf("date"));
        $this->_shortview .= " au " . CMbDT::transform(null, $this->_sortie, CAppUI::conf("date"));
      }
    }
    $this->_acte_execution = CMbDT::dateTime($this->entree_prevue);

    $this->_praticien_id = $this->praticien_id;

    $this->_adresse_par = ($this->etablissement_entree_id || $this->adresse_par_prat_id);

    if ($this->_adresse_par) {
      $medecin_adresse_par = new CMedecin();
      $medecin_adresse_par->load($this->adresse_par_prat_id);
      $this->_adresse_par_prat = $medecin_adresse_par->_view;

      $etab = new CEtabExterne();
      $etab->load($this->etablissement_entree_id);
      $this->_ref_etablissement_provenance = $etab->_view;
    }

    // Etat d'un sejour : encours, clôturé ou preadmission
    $this->_etat = "preadmission";
    if ($this->entree_reelle) {
      $this->_etat = "encours";
    }
    if ($this->sortie_reelle) {
      $this->_etat = "cloture";
    }

    // Motif complet du séjour
    $this->_motif_complet .= $this->libelle;
    $this->_motif_complet = "";
    if ($this->recuse == -1) {
      $this->_motif_complet .= "[Att] ";
    }
    $this->_motif_complet .= $this->libelle;

    if (!$this->annule && $this->recuse == -1) {
      $this->_view = "[Att] " . $this->_view;
    }
    
    if (CModule::getActive("dPfacturation") && CAppUI::conf("dPplanningOp CFactureEtablissement use_facture_etab")) {
      $this->loadRefsFactureEtablissement();
      if ($this->_ref_last_facture) {
        $facture = $this->_ref_last_facture;
        $this->_type_sejour     = $facture->type_facture;
        $this->_statut_pro      = $facture->statut_pro;
        $this->_dialyse         = $facture->dialyse;
        $this->_cession_creance = $facture->cession_creance;
        $this->_assurance_maladie         = $facture->assurance_maladie;
        $this->_assurance_accident        = $facture->assurance_accident;
        $this->_rques_assurance_maladie   = $facture->rques_assurance_maladie;
        $this->_rques_assurance_accident  = $facture->rques_assurance_accident;
      }
    }
  }

  function checkDaysRelative($date) {
    if ($this->_entree && $this->_sortie) {
      $this->_entree_relative = CMbDT::daysRelative($date, CMbDT::date($this->_entree));
      $this->_sortie_relative = CMbDT::daysRelative($date, CMbDT::date($this->_sortie));
    }
  }


  function updatePlainFields() {
    // Annulation / Récusation
    $this->completeField("annule", "recuse");
    $annule = $this->annule;
    if ($this->fieldModified("recuse", "1"))  $annule = "1";
    if ($this->fieldModified("recuse", "0"))  $annule = "0";
    if ($this->fieldModified("recuse", "-1")) $annule = "0";
    $this->annule = $annule;

    // Détail d'horaire d'entrée, ne pas comparer la date_entree_prevue à null
    // @todo Passer au TimePicker
    if ($this->_date_entree_prevue && $this->_hour_entree_prevue !== null && $this->_min_entree_prevue !== null) {
      $this->entree_prevue = "$this->_date_entree_prevue";
      $this->entree_prevue.= " ".str_pad($this->_hour_entree_prevue, 2, "0", STR_PAD_LEFT);
      $this->entree_prevue.= ":".str_pad($this->_min_entree_prevue , 2, "0", STR_PAD_LEFT);
      $this->entree_prevue.= ":00";
    }

    // Détail d'horaire de sortie, ne pas comparer la date_sortie_prevue à null
    // @todo Passer au TimePicker
    if ($this->_date_sortie_prevue && $this->_hour_sortie_prevue !== null && $this->_min_sortie_prevue !== null) {
      $this->sortie_prevue = "$this->_date_sortie_prevue";
      $this->sortie_prevue.= " ".str_pad($this->_hour_sortie_prevue, 2, "0", STR_PAD_LEFT);
      $this->sortie_prevue.= ":".str_pad($this->_min_sortie_prevue , 2, "0", STR_PAD_LEFT);
      $this->sortie_prevue.= ":00";
    }

    $this->completeField('entree_prevue', 'sortie_prevue', 'entree_reelle', 'sortie_reelle', 'type');

    // Signaler l'action de validation de la sortie
    if ($this->_modifier_sortie === '1') {
      $this->sortie_reelle = CMbDT::dateTime();
    }

    if ($this->_modifier_sortie === '0') {
      $this->sortie_reelle = "";
    }

    // Signaler l'action de validation de l'entrée
    if ($this->_modifier_entree === '1') {
      $this->entree_reelle = CMbDT::dateTime();
    }

    if ($this->_modifier_entree === '0') {
      $this->entree_reelle = "";
    }

    // Affectation de la date d'entrée prévue si on a la date d'entrée réelle
    if ($this->entree_reelle && !$this->entree_prevue) {
      $this->entree_prevue = $this->entree_reelle;
    }

    // Affectation de la date de sortie prévue si on a la date de sortie réelle
    if ($this->sortie_reelle && !$this->sortie_prevue) {
      $this->sortie_prevue = $this->sortie_reelle;
    }

    // Nouveau séjour relié à une grossesse
    // Si l'entrée prévue est à l'heure courante, alors on value également l'entrée réelle
    if (CModule::getActive("maternite") && !$this->_id && $this->grossesse_id) {
      $current_hour = CMbDT::transform(CMbDT::time(), null, "%H");
      $hour_sejour = $this->_hour_entree_prevue;

      if (CMbDT::date() == $this->_date_entree_prevue && $current_hour == $hour_sejour) {
        $this->entree_reelle = CMbDT::dateTime();
      }
    }

    //@TODO : mieux gérer les current et now dans l'updatePlainFields et le store
    $entree_reelle = ($this->entree_reelle === 'current'|| $this->entree_reelle ===  'now') ? CMbDT::dateTime() : $this->entree_reelle;
    if ($entree_reelle && ($this->sortie_prevue < $entree_reelle)) {
      $this->sortie_prevue = $this->type == "comp" ? CMbDT::dateTime("+1 DAY", $entree_reelle) : $entree_reelle;
    }

    // Synchro durée d'hospi / type d'hospi
    $this->_at_midnight = (CMbDT::date(null, $this->entree_prevue) != CMbDT::date(null, $this->sortie_prevue));
    if ($this->_at_midnight && $this->type == "ambu") {
      $this->type = "comp";
    }
    elseif (!$this->_at_midnight && $this->type == "comp") {
      $this->type = "ambu";
    }

    // Has to be donne once entree / sortie - reelle / prevue is not modified
    $this->entree = $this->entree_reelle ? $this->entree_reelle : $this->entree_prevue;
    $this->sortie = $this->sortie_reelle ? $this->sortie_reelle : $this->sortie_prevue;
  }

  /**
   * Count sejours including a specific date
   *
   * @param string $date     Date to check for inclusion
   * @param array  $where    Array of additional where clauses
   * @param array  $leftjoin Array of left join clauses
   *
   * @return int Count null if module is not installed
   */
  static function countForDate($date, $where = null, $leftjoin = null) {
    $where[] = "sejour.entree <= '$date 23:59:59'";
    $where[] = "sejour.sortie >= '$date 00:00:00'";
    $sejour = new CSejour;
    return $sejour->countList($where, null, $leftjoin);
  }

  /**
   * Count sejours including a specific date
   *
   * @param string $datetime Date to check for inclusion
   * @param array  $where    Array of additional where clauses
   * @param array  $leftjoin Array of left join clauses
   *
   * @return int Count null if module is not installed
   */
  static function countForDateTime($datetime, $where = null, $leftjoin = null) {
    $where[] = "sejour.entree <= '$datetime'";
    $where[] = "sejour.sortie >= '$datetime'";
    $sejour = new CSejour;
    return $sejour->countList($where, null, $leftjoin);
  }

  /**
   * Load sejours including a specific date
   *
   * @param date   $date  Date to check for inclusion
   * @param array  $where Array of additional where clauses
   * @param array  $order Array of order fields
   * @param string $limit MySQL limit clause
   * @param array  $group Array of group by clauses
   * @param array  $ljoin Array of left join clauses
   *
   * @return self[] List of found sejour, null if module is not installed
   */
  static function loadListForDate($date, $where = null, $order = null, $limit = null, $group = null, $ljoin = null) {
    $where[] = "sejour.entree <= '$date 23:59:59'";
    $where[] = "sejour.sortie >= '$date 00:00:00'";
    $sejour = new CSejour;
    return $sejour->loadList($where, $order, $limit, $group, $ljoin);
  }

  /**
   * Load sejours including a specific datetime
   *
   * @param datetime $datetime Datetime to check for inclusion
   * @param array    $where    Array of additional where clauses
   * @param array    $order    Array of order fields
   * @param string   $limit    MySQL limit clause
   * @param array    $group    Array of group by clauses
   * @param array    $ljoin    Array of left join clauses
   *
   * @return self[] List of found sejour, null if module is not installed
   */
  static function loadListForDateTime($datetime, $where = null, $order = null, $limit = null, $group = null, $ljoin = null) {
    $where[] = "sejour.entree <= '$datetime'";
    $where[] = "sejour.sortie >= '$datetime'";
    $sejour = new CSejour;
    return $sejour->loadList($where, $order, $limit, $group, $ljoin);
  }

  function getTemplateClasses() {
    $this->loadRefsFwd();

    $tab = array();

    // Stockage des objects liés au séjour
    $tab['CSejour'] = $this->_id;
    $tab['CPatient'] = $this->_ref_patient->_id;

    $tab['CConsultation'] = 0;
    $tab['CConsultAnesth'] = 0;
    $tab['COperation'] = 0;

    return $tab;
  }

  // Calcul des droits CMU pour la duree totale du sejour
  function getDroitsCMU() {
    if ($this->_date_sortie_prevue <= $this->_ref_patient->fin_amo && $this->_ref_patient->cmu) {
      $this->_couvert_cmu = 1;
    }
    else {
      $this->_couvert_cmu = 0;
    }
    if ($this->_date_sortie_prevue <= $this->_ref_patient->fin_amo && $this->_ref_patient->ald) {
      $this->_couvert_ald = 1;
    }
    else {
      $this->_couvert_ald = 0;
    }
  }

  function loadRefSejour() {
    $this->_ref_sejour =& $this;
  }

  /**
   * Load current affectation relative to a date
   *
   * @param datetime $date Reference datetime, now if null
   *
   * @return CAffectation
   */
  /* @todo A dédoublonner avec getCurrAffectation  */
  function loadRefCurrAffectation($date = "", $service_id = "") {
    if (!$date) {
      $date = CMbDT::dateTime();
    }

    $affectation = new CAffectation();
    $where = array();
    $where["sejour_id"] = " = '$this->_id'";
    if ($service_id) {
      $where["service_id"] = " = '$service_id'";
    }
    $where[] = "'$date' BETWEEN entree AND sortie";
    $affectation->loadObject($where);
    if ($affectation->_id) {
      $affectation->loadRefLit()->loadCompleteView();
    }

    return  $this->_ref_curr_affectation = $affectation;
  }


  /**
   * Load surrounding affectations
   *
   * @param date $date Current date, now if null
   *
   * @return array[CAffectation] Affectations array with curr, prev and next keys
   */
  function loadSurrAffectations($date = "") {
    if (!$date) {
      $date = CMbDT::dateTime();
    }

    // Current affectation
    $affectations = array();
    $affectations["curr"] = $this->loadRefCurrAffectation($date);

    // Previous affection
    $affectation = new CAffectation();
    $where = array();
    $where["sortie"] = " < '$date'";
    $where["sejour_id"] = " = '$this->_id'";
    $affectation->loadObject($where);
    if ($affectation->_id) {
      $affectation->loadRefLit()->loadCompleteView();
    }
    $affectations["prev"] = $this->_ref_prev_affectation = $affectation;

    // Next affectation
    $affectation = new CAffectation();
    $where = array();
    $where["entree"] = "> '$date'";
    $where["sejour_id"] = " = '$this->_id'";
    $affectation->loadObject($where);
    if ($affectation->_id) {
      $affectation->loadRefLit()->loadCompleteView();
    }
    $affectations["next"] = $this->_ref_next_affectation = $affectation;

    return $affectations;
  }

  /**
   * @return CDossierMedical
   */
  function loadRefDossierMedical() {
    return $this->_ref_dossier_medical = $this->loadUniqueBackRef("dossier_medical");
  }

  /**
   * @return CEtabExterne
   */
  function loadRefEtablissementProvenance($cache = true) {
    return $this->_ref_etablissement_provenance = $this->loadFwdRef("etablissement_entree_id", $cache);
  }

  /**
   * @return CEtabExterne
   */
  function loadRefEtablissementTransfert($cache = true) {
    return $this->_ref_etablissement_transfert = $this->loadFwdRef("etablissement_sortie_id", $cache);
  }

  /**
   * @return CService
   */
  function loadRefServiceMutation($cache = true) {
    return $this->_ref_service_mutation = $this->loadFwdRef("service_sortie_id", $cache);
  }

  /**
   * @return CChargePriceIndicator
   */
  function loadRefChargePriceIndicator($cache = true) {
    return $this->_ref_charge_price_indicator = $this->loadFwdRef("charge_id", $cache);
  }

  /**
 * @return CModeEntreeSejour
 */
  function loadRefModeEntree($cache = true) {
    return $this->_ref_mode_entree = $this->loadFwdRef("mode_entree_id", $cache);
  }

  /**
   * @return CModeSortieSejour
   */
  function loadRefModeSortie($cache = true) {
    return $this->_ref_mode_sortie = $this->loadFwdRef("mode_sortie_id", $cache);
  }

  function countNotificationVisite($date = '') {
    if (!$date) {
      $date = CMbDT::date();
    }
    $this->completeField("praticien_id");
    $observation = new CObservationMedicale();
    $where = array();
    $where["sejour_id"]  = " = '$this->_id'";
    $where["user_id"]  = " = '$this->praticien_id'";
    $where["degre"]  = " = 'info'";
    $where["date"]  = " LIKE '$date%'";
    return $observation->countList($where);
  }

  /**
   * @param bool $cache Use cache
   *
   * @return CPatient
   */
  function loadRefPatient($cache = true) {
    $this->_ref_patient = $this->loadFwdRef("patient_id", $cache);
    $this->getDroitsCMU();

    // View
    if (substr($this->_view, 0, 9) == "Séjour du") {
      $this->_view = $this->_ref_patient->_view . " - " . $this->_view;
    }

    return $this->_ref_patient;
  }

  /**
   * @param bool $cache
   *
   * @return CMediusers
   */
  function loadRefPraticien($cache = true) {
    $this->_ref_praticien = $this->loadFwdRef("praticien_id", $cache);
    $this->_ref_executant = $this->_ref_praticien;
    $this->_ref_praticien->loadRefFunction();
    return $this->_ref_praticien;
  }

  function loadExtDiagnostics() {
    $this->_ext_diagnostic_principal = $this->DP ? new CCodeCIM10($this->DP, 1) : null;
    $this->_ext_diagnostic_relie     = $this->DR ? new CCodeCIM10($this->DR, 1) : null;
  }

  function loadDiagnosticsAssocies($split = true) {
    $this->_diagnostics_associes = array();
    if ($this->_ref_dossier_medical->_id) {
      foreach ($this->_ref_dossier_medical->_codes_cim as $code) {
        if ($split && strlen($code) >= 4) {
          $this->_diagnostics_associes[] = substr($code, 0, 3).".".substr($code, 3);
        }
        else {
          $this->_diagnostics_associes[] = $code;
        }
      }
    }

    return $this->_diagnostics_associes;
  }

  /**
   * @return CPrestation
   */
  function loadRefPrestation() {
    return $this->_ref_prestation = $this->loadFwdRef("prestation_id", true);
  }

  function loadRefsTransmissions($cible_importante = false, $important = false, $macro_cible = false, $limit = "") {
    $this->_ref_transmissions = array();
    if ($cible_importante) {
      // Chargement de la derniere transmission importante (macrocible)
      $transmission = new CTransmissionMedicale();
      $ljoin = array();
      $ljoin["category_prescription"] = "category_prescription.category_prescription_id = transmission_medicale.object_id";

      $where = array();
      $where["object_class"] = " = 'CCategoryPrescription'";
      $where["sejour_id"] = " = '$this->_id'";
      $where["category_prescription.cible_importante"] = " = '1'";

      if ($macro_cible) {
        $where["category_prescription.only_cible"] = " = '1'";
      }

      $order = "date DESC";
      $this->_ref_transmissions = $transmission->loadList($where, $order, $limit, null, $ljoin);
    }
    if ($important) {
      // Chargement des transmissions de degré important
      $transmission = new CTransmissionMedicale;
      $where = array();
      $where["sejour_id"] =  "= '$this->_id'";
      $order = "date DESC";
      $where["degre"] = " = 'high'";
      $this->_ref_transmissions = $this->_ref_transmissions + $transmission->loadList($where, $order, $limit);
    }
    if (!$cible_importante && !$important) {
      $this->_ref_transmissions = $this->loadBackRefs("transmissions");
    }

    return $this->_ref_transmissions;
  }

  /**
   * @param bool $important
   *
   * @return CObservationMedicale[]
   */
  function loadRefsObservations($important = false) {
    $order = "date DESC";
    if ($important) {
      $obs = new CObservationMedicale;
      $where = array();
      $where["sejour_id"] = " = '$this->_id'";
      $where["degre"]     = " = 'high'";
      return $this->_ref_observations = $obs->loadList($where, $order);
    }

    return $this->_ref_observations = $this->loadBackRefs("observations", $order);
  }

  function countTasks() {
    $where["realise"] = "!= '1'";
    $this->_count_pending_tasks =  $this->countBackRefs("tasks", $where);
    return $this->_count_tasks = $this->countBackRefs("tasks");
  }

  function loadRefsTasks() {
    return $this->_ref_tasks = $this->loadBackRefs("tasks");
  }

  function loadRefsExamsIGS() {
    return $this->_ref_exams_igs = $this->loadBackRefs("exams_igs");
  }

  function loadSuiviMedical() {
    $this->loadBackRefs("observations");
    $this->loadBackRefs("transmissions");

    $consultations = $this->loadRefsConsultations();
    $consultations_patient = $this->loadRefPatient()->loadRefsConsultations();

    $this->_ref_suivi_medical = array();

    if (isset($this->_back["observations"])) {
      foreach ($this->_back["observations"] as $curr_obs) {
        $curr_obs->loadRefsFwd();
        $curr_obs->_ref_user->loadRefFunction();
        $this->_ref_suivi_medical[$curr_obs->date.$curr_obs->_id."obs"] = $curr_obs;
      }
    }

    if (isset($this->_back["transmissions"])) {
      foreach ($this->_back["transmissions"] as $curr_trans) {
        $curr_trans->loadRefsFwd();
        if ($curr_trans->_ref_object instanceof CAdministration) {
          $curr_trans->_ref_object->loadRefsFwd();
        }

        $this->_ref_suivi_medical[$curr_trans->date.$curr_trans->_id."trans"][] = $curr_trans;
      }
    }

    foreach ($consultations as $_consultation) {
      $_consultation->canEdit();
      $_consultation->loadRefConsultAnesth();
      foreach ($_consultation->_refs_dossiers_anesth as $_dossier_anesth) {
        $_dossier_anesth->loadRefOperation();
        $_dossier_anesth->loadRefsTechniques();
      }
      $_consultation->loadRefPlageConsult();
      $_consultation->loadRefPraticien()->loadRefFunction();
      $this->_ref_suivi_medical[$_consultation->_datetime] = $_consultation;
    }

    // Ajout des consultations d'anesthésie hors séjour
    foreach ($consultations_patient as $_consultation) {
      $_consult_anesth = $_consultation->loadRefConsultAnesth();
      if (!count($_consultation->_refs_dossiers_anesth)) {
        continue;
      }
      foreach ($_consultation->_refs_dossiers_anesth as $_dossier_anesth) {
        $_dossier_anesth->loadRefOperation();
        $_dossier_anesth->loadRefsTechniques();
      }
      $_consultation->loadRefPlageConsult();
      $_consultation->loadRefPraticien()->loadRefFunction();
      $this->_ref_suivi_medical[$_consultation->_datetime] = $_consultation;
    }

    if (CModule::getActive("dPprescription") && $this->type == "urg" && CAppUI::conf("dPprescription CPrescription prescription_suivi_soins", CGroups::loadCurrent())) {
      $this->loadRefPrescriptionSejour();
      $prescription = $this->_ref_prescription_sejour;

      // Chargement des lignes de prescriptions d'elements
      $prescription->loadRefsLinesElement();
      $prescription->loadRefsLinesAllComments();

      foreach ($prescription->_ref_prescription_lines_all_comments as $_comment) {
        $_comment->canEdit();
        $_comment->countBackRefs("transmissions");
        $this->_ref_suivi_medical["$_comment->debut $_comment->time_debut $_comment->_guid"] = $_comment;
      }

      // Ajout des lignes de prescription dans la liste du suivi de soins
      foreach ($prescription->_ref_prescription_lines_element as $_line_element) {
        $_line_element->canEdit();
        $_line_element->countBackRefs("transmissions");
        $this->_ref_suivi_medical["$_line_element->debut $_line_element->time_debut $_line_element->_guid"] = $_line_element;
      }
    }

    krsort($this->_ref_suivi_medical);
    return $this->_ref_suivi_medical;
  }

  function loadRefConstantes($user_id = null) {
    $this->loadListConstantesMedicales();
    $constantes = $this->_list_constantes_medicales;

    foreach ($constantes as $_const) {
      $_const->loadRefUser();
      if ($_const->context_class != "CSejour" || $_const->context_id != $this->_id) {
        unset($constantes[$_const->_id]);
      }
      if ($user_id) {
        $first_log = $_const->loadFirstLog();
        $first_log->loadRefUser();
        if ($first_log->_ref_user->_id != $user_id) {
          unset($constantes[$_const->_id]);
        }
      }
    }

    if (!$this->_ref_suivi_medical) {
      $this->_ref_suivi_medical = array();
    }

    $this->_ref_suivi_medical = array_merge($constantes, $this->_ref_suivi_medical);
  }

  /**
   * Load associated Group
   *
   * @return CGroups
   */
  function loadRefEtablissement($cache = true) {
    return $this->_ref_group = $this->loadFwdRef("group_id", $cache);
  }

  /**
   * Load associated RPU
   *
   * @return CRPU
   */
  function loadRefRPU() {
    return $this->_ref_rpu = $this->loadUniqueBackRef("rpu");
  }

  /**
   * Load associated BilanSSR
   *
   * @return CBilanSSR
   */
  function loadRefBilanSSR() {
    return $this->_ref_bilan_ssr = $this->loadUniqueBackRef("bilan_ssr");
  }

  /**
   * Charge la fiche d'autonomie associé
   *
   * @return CFicheAutonomie
   */
  function loadRefFicheAutonomie() {
    return $this->_ref_fiche_autonomie = $this->loadUniqueBackRef("fiche_autonomie");
  }

  function loadRefAdresseParPraticien() {
    $this->_ref_adresse_par_prat = new CMedecin();
    return $this->_ref_adresse_par_prat->load($this->adresse_par_prat_id);
  }

  function loadRefsConsultAnesth() {
    if ($this->_ref_consult_anesth) {
      return $this->_ref_consult_anesth;
    }

    $order = "consultation_anesth_id ASC";
    $this->_ref_consult_anesth = new CConsultAnesth();
    $this->_ref_consult_anesth->sejour_id = $this->_id;
    $this->_ref_consult_anesth->loadMatchingObject($order);

    return $this->_ref_consult_anesth;
  }

  /**
   * Charge les consultations, en particulier l'ATU dans le cas UPATOU
   *
   * @return CConsultation[]
   */
  function loadRefsConsultations() {
    $this->_ref_consultations = $this->loadBackRefs("consultations");

    $this->_ref_consult_atu = new CConsultation;

    if ($this->countBackRefs("rpu") > 0) {
      foreach ($this->_ref_consultations as $_consult) {
        $_consult->loadRefPraticien();
        $praticien = $_consult->_ref_praticien;
        $praticien->loadRefFunction();
        if ($praticien->isUrgentiste()) {
          $this->_ref_consult_atu = $_consult;
          $this->_ref_consult_atu->countDocItems();
          break;
        }
      }
    }

    return $this->_ref_consultations;
  }

  /*
   * Chargement de toutes les prescriptions liées au sejour (object_class CSejour)
   */
  function loadRefsPrescriptions() {
    $prescriptions = $this->loadBackRefs("prescriptions");
    // Si $prescriptions n'est pas un tableau, module non installé
    if (!is_array($prescriptions)) {
      $this->_ref_last_prescription = null;
      return;
    }
    $this->_count_prescriptions = count($prescriptions);
    $this->_ref_prescriptions["pre_admission"] = new CPrescription();
    $this->_ref_prescriptions["sejour"] = new CPrescription();
    $this->_ref_prescriptions["sortie"] = new CPrescription();

    // Stockage des prescriptions par type
    foreach ($prescriptions as $_prescription) {
      $this->_ref_prescriptions[$_prescription->type] = $_prescription;
    }

    return $this->_ref_prescriptions;
  }

  /**
   * @return CPrescription|null
   */
  function loadRefPrescriptionSejour() {
    if (!CModule::getActive("dPprescription")) {
      return null;
    }

    $this->_ref_prescription_sejour = new CPrescription();
    $this->_ref_prescription_sejour->object_class = "CSejour";
    $this->_ref_prescription_sejour->object_id = $this->_id;
    $this->_ref_prescription_sejour->type = "sejour";
    $this->_ref_prescription_sejour->loadMatchingObject();

    return $this->_ref_prescription_sejour;
  }

  function loadRefsPrescripteurs() {
    $this->loadRefsPrescriptions();
    foreach ($this->_ref_prescriptions as $_prescription) {
      $_prescription->getPraticiens();
      if (is_array($_prescription->_praticiens)) {
        foreach ($_prescription->_praticiens as $_praticien_id => $_praticien_view) {
          if (!is_array($this->_ref_prescripteurs) || !array_key_exists($_praticien_id, $this->_ref_prescripteurs)) {
            $praticien = new CMediusers();
            $this->_ref_prescripteurs[$_praticien_id] = $praticien->load($_praticien_id);
          }
        }
      }
    }
  }

  /**
   * @return CReplacement[]
   */
  function loadRefReplacements(){
    return $this->_ref_replacements = $this->loadBackRefs("replacements");
  }

  /**
   * @param int $conge_id
   *
   * @return CReplacement
   */
  function loadRefReplacement($conge_id) {
    $this->_ref_replacement = new CReplacement;
    $this->_ref_replacement->sejour_id = $this->_id;
    $this->_ref_replacement->conge_id = $conge_id;
    $this->_ref_replacement->loadMatchingObject();
    return $this->_ref_replacement;
  }

  /**
   * @return CGrossesse
   */
  function loadRefGrossesse() {
    return $this->_ref_grossesse = $this->loadFwdRef("grossesse_id");
  }

  function isReplacer($replacer_id) {
    $replacement = new CReplacement;
    $replacement->sejour_id   = $this->_id;
    $replacement->replacer_id = $replacer_id;
    return $replacement->countMatchingList();
  }

  function loadListConstantesMedicales($where = array()) {
    if ($this->_list_constantes_medicales) {
      return $this->_list_constantes_medicales;
    }

    $constantes = new CConstantesMedicales();
    $where['context_class'] = " = '$this->_class'";
    $where['context_id']    = " = '$this->_id'";
    $where['patient_id']    = " = '$this->patient_id'";

    return $this->_list_constantes_medicales = $constantes->loadList($where, 'datetime ASC');
  }

  function loadRefsFwd($cache = true) {
    $this->loadRefPatient($cache);
    $this->loadRefPraticien($cache);
    $this->loadRefEtablissement($cache);
    $this->loadRefEtablissementTransfert($cache);
    $this->loadRefServiceMutation($cache);
    $this->loadExtCodesCCAM();
  }

  function loadComplete() {
    parent::loadComplete();
    foreach ($this->_ref_operations as &$operation) {
      $operation->loadRefsFwd();
      $operation->_ref_chir->loadRefsFwd();
    }
    foreach ($this->_ref_affectations as &$affectation) {
      $affectation->loadRefLit();
      $affectation->_ref_lit->loadCompleteView();
    }

    if ($this->_ref_actes_ccam) {
      foreach ($this->_ref_actes_ccam as &$acte_ccam) {
        $acte_ccam->loadRefsFwd();
      }
    }
    $this->loadExtDiagnostics();

    // Chargement du RPU dans le cas des urgences
    $this->loadRefRPU();
    if ($this->_ref_rpu) {
      $this->_ref_rpu->loadRefSejour();
    }

    $this->loadNDA();

    // Chargement de la consultation anesth pour l'affichage de la fiche d'anesthesie
    $this->loadRefsConsultAnesth();
    $this->_ref_consult_anesth->loadRefConsultation();

    $this->loadSuiviMedical();
    $this->_ref_patient->loadRefPhotoIdentite();
  }

  function loadView() {
    parent::loadView();
    $this->loadRefPatient()->loadRefPhotoIdentite();
    $this->loadRefEtablissement();
    $affectations = $this->loadRefsAffectations();

    foreach ($this->loadRefsOperations() as $_operation) {
      $_operation->loadRefChir();
      $_operation->loadRefPlageOp();
    }

    if (is_array($affectations) && count($affectations)) {
      foreach ($affectations as $_affectation) {
        if (!$_affectation->lit_id) {
          $_affectation->_view = $_affectation->loadRefService()->_view;
        }
        else {
          $_affectation->loadRefLit()->loadCompleteView();
          $_affectation->_view = $_affectation->_ref_lit->_view;
        }

        $_affectation->loadRefParentAffectation();
      }
    }

    $this->loadNDA();

    if (CModule::getActive("printing")) {
      // Compter les imprimantes pour l'impression d'étiquettes
      $user_printers = CMediusers::get();
      $function      = $user_printers->loadRefFunction();
      $this->_nb_printers = $function->countBackRefs("printers");
    }

    // On compte les modèles d'étiquettes pour :
    // - stream si un seul
    // - modale de choix si plusieurs
    $modele_etiquette = new CModeleEtiquette();
    $modele_etiquette->object_class = "CSejour";
    $modele_etiquette->group_id = $this->group_id;
    $this->_count_modeles_etiq = $modele_etiquette->countMatchingList();
  }

/**
   * Charge le sejour ayant les traits suivants :
   * - Meme patient
   * - Meme praticien si praticien connu
   * - Date de d'entree et de sortie équivalentes
   * @return Nombre d'occurences trouvées
   */
  function loadMatchingSejour($strict = null, $notCancel = false, $useSortie = true) {
    if ($strict && $this->_id) {
      $where["sejour_id"] = " != '$this->_id'";
    }
    $where["patient_id"] = " = '$this->patient_id'";

    $this->_entree = CValue::first($this->entree_reelle, $this->entree_prevue);
    if ($useSortie) {
      $this->_sortie = CValue::first($this->sortie_reelle, $this->sortie_prevue);
    }

    if (!$this->_entree) {
      return;
    }

    if ($this->_entree) {
      $date_entree = CMbDT::date($this->_entree);
      $where[] = "DATE(entree_prevue) = '$date_entree' OR DATE(entree_reelle) = '$date_entree'";
    }
    if ($useSortie) {
      if ($this->_sortie) {
        $date_sortie = CMbDT::date($this->_sortie);
        $where[] = "DATE(sortie_prevue) = '$date_sortie' OR DATE(sortie_reelle) = '$date_sortie'";
      }
    }

    if ($notCancel) {
      $where["annule"] = " = '0'";
    }

    if ($this->type) {
      $where["type"] = " = '$this->type'";
    }

    $this->loadObject($where);
    return $this->countList($where);
  }

  /**
   * Construit le tag NDOS en fonction des variables de configuration
   * @param $group_id Permet de charger le NDOS pour un établissement donné si non null
   * @return string
   */
  static function getTagNDA($group_id = null, $type_tag = "tag_dossier") {
    $context = array(__METHOD__, func_get_args());    
    if (CFunctionCache::exist($context)) {
      return CFunctionCache::get($context);
    }
     
    // Gestion du tag NDA par son domaine d'identification
    if (CAppUI::conf("eai use_domain")) {
      $tag_NDA = CDomain::getTagMasterDomain("CSejour", $group_id);

      if ($type_tag != "tag_dossier") {
        $tag_NDA = CAppUI::conf("dPplanningOp CSejour $type_tag") . $tag_NDA;
      }

      return $tag_NDA;
    }

    $tag_NDA = CAppUI::conf("dPplanningOp CSejour tag_dossier");

    if ($type_tag != "tag_dossier") {
      $tag_NDA = CAppUI::conf("dPplanningOp CSejour $type_tag") . $tag_NDA;
    }

    // Permettre des IPP en fonction de l'établissement
    $group = CGroups::loadCurrent();
    if (!$group_id) {
      $group_id = $group->_id;
    }

    // Si on est dans le cas d'un établissement gérant la numérotation
    $group->loadConfigValues();
    if ($group->_configs["smp_idex_generator"]) {
      $tag_NDA = CAppUI::conf("smp tag_nda");
    }

    // Pas de tag Num dossier
    if (null == $tag_NDA) {
      return;
    }

    // Préférer un identifiant externe de l'établissement
    if ($tag_group_idex = CAppUI::conf("dPplanningOp CSejour tag_dossier_group_idex")) {
      $idex = new CIdSante400();
      $idex->loadLatestFor($group, $tag_group_idex);
      $group_id = $idex->id400;
    }

    return CFunctionCache::set($context, str_replace('$g', $group_id, $tag_NDA));
  }

  /**
   * Construit le tag NPA (préad) en fonction des variables de configuration
   * @param $group_id Permet de charger le NPA pour un établissement donné si non null
   * @return string
   */
  static function getTagNPA($group_id = null) {
    return self::getTagNDA($group_id, "tag_dossier_pa");
  }

  /**
   * Construit le tag NTA (trash) en fonction des variables de configuration
   * @param $group_id Permet de charger le NTA pour un établissement donné si non null
   * @return string
   */
  static function getTagNTA($group_id = null) {
    return self::getTagNDA($group_id, "tag_dossier_trash");
  }

  /**
   * Construit le tag NRA (rang) en fonction des variables de configuration
   * @param $group_id Permet de charger le NRA pour un établissement donné si non null
   * @return string
   */
  static function getTagNRA($group_id = null) {
    return self::getTagNDA($group_id, "tag_dossier_rang");
  }

  /**
   * Charge le NDA du séjour pour l'établissement courant
   *
   * @param $group_id Permet de charger le NDA pour un établissement donné si non null
   *
   * @return void
   */
  function loadNDA($group_id = null) {
    // Objet inexistant
    if (!$this->_id) {
      return "-";
    }

    // Aucune configuration de numéro de dossier
    if (null == $tag_NDA = $this->getTagNDA($group_id)) {
      $this->_NDA_view = $this->_NDA = str_pad($this->_id, 6, "0", STR_PAD_LEFT);
      return;
    }


    // Recuperation de la valeur de l'id400
    $idex = CIdSante400::getMatchFor($this, $tag_NDA);

    // Stockage de la valeur de l'id400
    $this->_ref_NDA  = $idex;
    $this->_NDA_view = $this->_NDA = $idex->id400;

    // Cas de l'utilisation du rang
    $this->loadNRA($group_id);
  }

  /**
   * Charge le Numéro de pré-admission du séjour pour l'établissement courant
   *
   * @param $group_id Permet de charger le NPA pour un établissement donné si non null
   *
   * @return void
   */
  function loadNPA($group_id = null) {
    // Objet inexistant
    if (!$this->_id) {
      return "-";
    }

    // Aucune configuration de numéro de dossier
    if (null == $tag_NPA = $this->getTagNDA($group_id, "tag_dossier_pa")) {
      $this->_NPA = str_pad($this->_id, 6, "0", STR_PAD_LEFT);
      return;
    }

    // Recuperation de la valeur de l'id400
    $idex = CIdSante400::getMatchFor($this, $tag_NPA);

    // Stockage de la valeur de l'id400
    $this->_ref_NPA = $idex;
    $this->_NPA     = $idex->id400;
  }

  /**
   * Charge le Numéro de rang du séjour pour l'établissement courant
   *
   * @param $group_id Permet de charger le NRA pour un établissement donné si non null
   *
   * @return void
   */
  function loadNRA($group_id = null) {
    // Utilise t-on le rang pour le dossier
    if (!CAppUI::conf("dPplanningOp CSejour use_dossier_rang")) {
      return;
    }

    // Objet inexistant
    if (!$this->_id) {
      return "-";
    }

    // Aucune configuration du numero de rang
    if (null == $tag_NRA = $this->getTagNRA($group_id)) {
      return;
    }

    // Recuperation de la valeur de l'id400
    $idex = CIdSante400::getMatchFor($this, $tag_NRA);

    // Stockage de la valeur de l'id400
    $this->_ref_NRA = $idex;
    $NRA            = $idex->_id ? $idex->id400 : "-";

    // Récupération de l'IPP du patient
    $this->loadRefPatient();
    $this->_ref_patient->loadIPP();

    $this->_NDA_view = $this->_ref_patient->_IPP."/".$NRA;
  }

  function loadFromNDA($nda) {
    // Aucune configuration de numéro de dossier
    if (null == $tag_NDA = $this->getTagNDA()) {
      return;
    }

    $idDossier               = new CIdSante400();
    $idDossier->id400        = $nda;
    $idDossier->tag          = $tag_NDA;
    $idDossier->object_class = $this->_class;
    $idDossier->loadMatchingObject();

    if ($idDossier->_id) {
      $this->load($idDossier->object_id);
      $this->_NDA = $idDossier->id400;
    }
  }

  function getExecutantId($code_activite) {
      return $this->praticien_id;
  }

  function getPerm($permType) {
    if (!$this->_ref_praticien) {
      $this->loadRefPraticien();
    }
    if (!$this->_ref_group) {
      $this->loadRefEtablissement();
    }
    switch ($permType) {
      case PERM_EDIT :
        return ($this->_ref_group->getPerm($permType) && $this->_ref_praticien->getPerm($permType));
        break;
      default :
        return parent::getPerm($permType);
    }
  }

  /* @todo A dédoublonner avec loadRefCurrAffectation  */
  function getCurrAffectation($dateTime = null) {
    if (!$dateTime) {
      $dateTime = CMbDT::dateTime();
    }

    $ds = $this->_spec->ds;

    $where = array();
    $where["sejour_id"] = $ds->prepare("= %", $this->sejour_id);

    if (CMbDT::time(null, $dateTime) == "00:00:00") {
      $where["entree"] = $ds->prepare("<= %", CMbDT::date(null, $dateTime)." 23:59:59");
      $where["sortie"] = $ds->prepare(">= %", CMbDT::date(null, $dateTime)." 00:00:01");
    }
    else {
      $where["entree"] = $ds->prepare("<= %", $dateTime);
      $where["sortie"] = $ds->prepare(">= %", $dateTime);
    }

    $order = "entree";

    $curr_affectation = new CAffectation();
    $curr_affectation->loadObject($where, $order);

    return $curr_affectation;
  }

  /**
   * @param string $order
   *
   * @return CAffectation[]
   */
  function loadRefsAffectations($order = "sortie DESC") {
    $affectations = $this->loadBackRefs("affectations", $order);

    if (count($affectations) > 0) {
      $this->_ref_first_affectation = end  ($affectations);
      $this->_ref_last_affectation  = reset($affectations);
    }
    else {
      $this->_ref_first_affectation = new CAffectation;
      $this->_ref_last_affectation  = new CAffectation;
    }

    return $this->_ref_affectations = $affectations;
  }

  function loadRefsMovements() {
    return $this->_ref_movements = $this->loadBackRefs("movements");
  }

  /**
   * Load first transfer
   *
   * @return CAffectation
   */
  function loadRefFirstAffectation() {
    if (!$this->_ref_first_affectation) {
      $this->loadRefsAffectations();
    }

    return $this->_ref_first_affectation;
  }

  function forceAffectation(CAffectation $affectation) {
    $datetime   = $affectation->entree;
    $lit_id     = $affectation->lit_id;
    $service_id = $affectation->service_id;

    $splitting            = new CAffectation();
    $where["sejour_id"] = "=  '$this->_id'";
    $where["entree"]    = "<= '$datetime'";
    $where["sortie"]    = ">= '$datetime'";
    $splitting->loadObject($where);

    $create = new CAffectation();

    // On retrouve une affectation a spliter
    if ($splitting->_id) {
      // Affecte la sortie de l'affectation a créer avec l'ancienne date de sortie
      $create->sortie = $splitting->sortie;

      // On passe à effectuer la split
      $splitting->effectue    = 1;
      $splitting->sortie      = $datetime;
      $splitting->_no_synchro = true;
      if ($msg = $splitting->store()) {
        return $msg;
      }
    }
    // On créé une première affectation
    else {
      $create->sortie  = $this->sortie;
    }

    // Créé la nouvelle affectation
    $create->sejour_id  = $this->_id;
    $create->entree     = $datetime;
    $create->lit_id     = $lit_id;
    $create->service_id = $service_id;
    if ($msg = $create->store()) {
      return $msg;
    }

    return $create;
  }

  /**
   * @param array $where
   *
   * @return COperation[]
   */
  function loadRefsOperations($where = array()) {
    $where["sejour_id"] = "= '$this->_id'";
    $order = "date ASC";

    $operations = new COperation;
    $this->_ref_operations = $operations->loadList($where, $order);

    // Motif complet
    if (!$this->libelle) {
      $this->_motif_complet = "";
      if ($this->recuse == -1) {
        $this->_motif_complet .= "[Att] ";
      }
      $motif = array();
      foreach ($this->_ref_operations as $_op) {
        if ($_op->libelle) {
          $motif[] = $_op->libelle;
        }
        else {
           $motif[] = implode("; ", $_op->_codes_ccam);
        }
      }
      $this->_motif_complet .= implode("; ", $motif);
    }

    // Agrégats des codes CCAM des opérations
    $this->_codes_ccam_operations = CMbArray::pluck($this->_ref_operations, "codes_ccam");
    CMbArray::removeValue("", $this->_codes_ccam_operations);
    $this->_codes_ccam_operations = implode("|", $this->_codes_ccam_operations);

    if (count($this->_ref_operations) > 0) {
      $this->_ref_last_operation = reset($this->_ref_operations);
    }
    else {
      $this->_ref_last_operation = new COperation;
    }
    return $this->_ref_operations;
  }

  function loadRefLastOperation() {
    $operation = new COperation;
    $operation->sejour_id = $this->_id;
    $operation->loadMatchingObject("date DESC");

    return $this->_ref_last_operation = $operation;
  }

  function getCurrOperation($date, $show_trace = false, $only_one = true) {
    $date = CMbDT::date($date);

    $where["operations.sejour_id"] = "= '$this->_id'";
    $where[]            = "plagesop.date = '$date' OR operations.date = '$date'";

    $leftjoin = array();
    $leftjoin["plagesop"]   = "plagesop.plageop_id = operations.plageop_id";

    $operation = new COperation;
    if ($show_trace) {
      CSQLDataSource::$trace = true;
    }

    if ($only_one) {
      $operation->loadObject($where, null, null, $leftjoin);
    }
    else {
      // C'est bien la liste d'interventions que l'on retourne
      $operation = $operation->loadList($where, null, null, null, $leftjoin);
    }

    if ($show_trace) {
      CSQLDataSource::$trace = false;
    }

    return $operation;
  }

  function loadRefCurrOperation($date) {
    return $this->_ref_curr_operation = $this->getCurrOperation($date, false);
  }

  function loadRefCurrOperations($date) {
    return $this->_ref_curr_operations = $this->getCurrOperation($date, false, false);
  }

  function loadRefsBack() {
    $this->loadRefsFiles();
    $this->loadRefsAffectations();
    $this->loadRefsOperations();
    $this->loadRefsActesCCAM();
  }

  function loadRefGHM() {
    $this->_ref_GHM = $this->loadUniqueBackRef("ghm");
    if (!$this->_ref_GHM->_id) {
      $this->_ref_GHM->sejour_id = $this->sejour_id;
    }
    $this->_ref_GHM->_ref_sejour = $this;
    $this->_ref_GHM->bindInfos();
    $this->_ref_GHM->getGHM();
  }

  function fillLimitedTemplate(&$template) {
    // Ajout du praticien pour les destinataires possibles (dans l'envoi d'un email)
    $chir = $this->loadRefPraticien();
    $template->destinataires[] = array(
      "nom"   => "Dr " . $chir->_user_last_name . " " . $chir->_user_first_name,
      "email" => $chir->_user_email,
      "tag"   => "Praticien"
    );

    $this->notify("BeforeFillLimitedTemplate", $template);

    $template->addLongDateProperty("Admission - Date longue"  , $this->entree_prevue);
    $template->addDateProperty("Admission - Date"             , $this->entree_prevue);
    $template->addTimeProperty("Admission - Heure"            , $this->entree_prevue);
    $template->addProperty("Admission - Type"                 , $this->getFormattedValue("type"));
    $template->addProperty("Hospitalisation - Durée"          , $this->_duree_prevue);
    $template->addDateProperty("Hospitalisation - Date sortie", $this->sortie_prevue);
    $template->addProperty("Hospitalisation - Date sortie longue", $this->getFormattedValue("sortie_prevue"));
    $this->loadNDA();
    $template->addProperty("Sejour - Numéro de dossier"       , $this->_NDA );
    $template->addBarcode ("Sejour - Code barre ID"           , "SID$this->_id"     );
    $template->addBarcode ("Sejour - Code barre NDOS"         , "NDOS$this->_NDA");

    $template->addDateProperty("Sejour - Date entrée"         , $this->entree);
    $template->addLongDateProperty("Sejour - Date entrée (longue)", $this->entree);
    $template->addTimeProperty("Sejour - Heure entrée"        , $this->entree);
    $template->addDateProperty("Sejour - Date sortie"         , $this->sortie);
    $template->addLongDateProperty("Sejour - Date sortie (longue)", $this->sortie);
    $template->addTimeProperty("Sejour - Heure sortie"        , $this->sortie);

    $template->addDateProperty("Sejour - Date entrée réelle"  , $this->entree_reelle);
    $template->addTimeProperty("Sejour - Heure entrée réelle" , $this->entree_reelle);
    $template->addDateProperty("Sejour - Date sortie réelle"  , $this->sortie_reelle);
    $template->addTimeProperty("Sejour - Heure sortie réelle" , $this->sortie_reelle);

    $template->addProperty("Sejour - Mode d'entrée"           , $this->getFormattedValue("mode_entree"));
    $template->addProperty("Sejour - Mode de sortie"          , $this->getFormattedValue("mode_sortie"));
    $template->addProperty("Sejour - Service de sortie"       , $this->getFormattedValue("service_sortie_id"));
    $template->addProperty("Sejour - Etablissement de sortie" , $this->getFormattedValue("etablissement_sortie_id"));
    $template->addProperty("Sejour - Commentaires de sortie"  , $this->getFormattedValue("commentaires_sortie"));

    $template->addProperty("Sejour - Libelle"                 , $this->getFormattedValue("libelle"));
    $template->addProperty("Sejour - Transport"               , $this->getFormattedValue("transport"));

    $consult_anesth = $this->loadRefsConsultAnesth();
    $consult = $consult_anesth->loadRefConsultation();
    $consult->loadRefPlageConsult();
    
    $template->addDateProperty("Sejour - Consultation anesthésie - Date", $consult->_id ? $consult->_datetime : "");
    $template->addLongDateProperty("Sejour - Consultation anesthésie - Date (longue)", $consult->_id ? $consult->_datetime : "");
    $template->addLongDateProperty("Sejour - Consultation anesthésie - Date (longue, minuscule)", $consult->_id ? $consult->_datetime : "", true);
    $template->addTimeProperty("Sejour - Consultation anesthésie - Heure", $consult->_id ? $consult->_datetime : "");
    
    $this->loadRefsFiles();
    $list = CMbArray::pluck($this->_ref_files, "file_name");
    $template->addListProperty("Sejour - Liste des fichiers", $list);

    if (CAppUI::conf("dPurgences old_rpu") == "1") {
      if (CModule::getActive("sherpa")) {
        $rpu = $this->loadRefRPU();
        $template->addProperty("Sejour - Provenance"         , $rpu->_id ? $rpu->getFormattedValue("urprov") : "");
      }
    }
    else {
      $template->addProperty("Sejour - Provenance"           , $this->getFormattedValue("provenance"));
      $template->addProperty("Sejour - Destination"          , $this->getFormattedValue("destination"));
    }

    $this->loadRefPraticien();
    $template->addProperty("Hospitalisation - Praticien"    , "Dr ".$this->_ref_praticien->_view);

    $this->loadRefsAffectations();
    $this->_ref_last_affectation->loadView();
    $template->addProperty("Hospitalisation - Dernière affectation", $this->_ref_last_affectation->_view);

    // Diagnostics
    $this->loadExtDiagnostics();
    $diag = $this->DP ? "$this->DP: {$this->_ext_diagnostic_principal->libelle}" : null;
    $template->addProperty("Sejour - Diagnostic Principal"    , $diag);
    $diag = $this->DR ? "$this->DR: {$this->_ext_diagnostic_relie->libelle}" : null;
    $template->addProperty("Sejour - Diagnostic Relié"        , $diag);
    $template->addProperty("Sejour - Remarques", $this->rques);

    // Chargement du suivi medical (transmissions, observations, prescriptions)
    $this->loadSuiviMedical();

    // Transmissions
    $transmissions = array();
    if (isset($this->_back["transmissions"])) {
      foreach ($this->_back["transmissions"] as $_trans) {
        $datetime = CMbDT::transform(null, $_trans->date, CAppUI::conf('datetime'));
        $transmissions["$_trans->date $_trans->_guid"] = "$_trans->text, le $datetime, {$_trans->_ref_user->_view}";
      }
    }

    $template->addListProperty("Sejour - Transmissions", $transmissions);

    $this->loadRefsTransmissions(false, true, false);

    $transmissions_hautes = array();
    foreach ($this->_ref_transmissions as $_trans) {
      $_trans->loadRefUser();
      $datetime = CMbDT::transform(null, $_trans->date, CAppUI::conf('datetime'));
      $transmissions_hautes["$_trans->date $_trans->_guid"] = "$_trans->text, le $datetime, {$_trans->_ref_user->_view}";

    }
    $template->addListProperty("Sejour - Transmissions - importance haute", $transmissions_hautes);

    $this->loadRefsTransmissions(true, false, true);
    $transmissions_macro  = array();

    foreach ($this->_ref_transmissions as $_trans) {
      $_trans->loadRefUser();
      $datetime = CMbDT::transform(null, $_trans->date, CAppUI::conf('datetime'));
      $transmissions_macro["$_trans->date $_trans->_guid"] = "$_trans->text, le $datetime, {$_trans->_ref_user->_view}";

    }

    $template->addListProperty("Sejour - Transmissions - macrocible", $transmissions_macro);


    // Observations
    $observations = array();
    if (isset($this->_back["observations"])) {
      foreach ($this->_back["observations"] as $_obs) {
        $datetime = CMbDT::transform(null, $_obs->date, CAppUI::conf('datetime'));
        $observations["$_obs->date $_obs->_guid"] = "$_obs->text, le $datetime, {$_obs->_ref_user->_view}";
      }
    }
    $template->addListProperty("Sejour - Observations", $observations);

    // Prescriptions
    $lines = array();
    if (CModule::getActive('dPprescription')) {

      $prescription = $this->loadRefPrescriptionSejour();
      $prescription->loadRefsLinesAllComments();
      $prescription->loadRefsLinesElement();

      if (isset($prescription->_ref_prescription_lines_all_comments)) {
        foreach ($prescription->_ref_prescription_lines_all_comments as $_comment) {
          $datetime = CMbDT::transform(null, "$_comment->debut $_comment->time_debut", CAppUI::conf('datetime'));
          $lines["$_comment->debut $_comment->time_debut $_comment->_guid"] = "$_comment->_view, $datetime, {$_comment->_ref_praticien->_view}";
        }
      }

      if (isset($prescription->_ref_prescription_lines_element)) {
        foreach ($prescription->_ref_prescription_lines_element as $_line_element) {
          $datetime = CMbDT::transform(null, "$_line_element->debut $_line_element->time_debut", CAppUI::conf('datetime'));
          $view = "$_line_element->_view";
          if ($_line_element->commentaire) {
            $view .= " ($_line_element->commentaire)";
          }
          $view .= ", $datetime, ".$_line_element->_ref_praticien->_view;
          $lines["$_line_element->debut $_line_element->time_debut $_line_element->_guid"] = $view;
        }
      }
      krsort($lines);
      $template->addListProperty("Sejour - Prescription light", $lines);
    }

    // Suivi médical: transmissions, observations, prescriptions
    $suivi_medical = $transmissions + $observations + $lines;
    krsort($suivi_medical);
    $template->addListProperty("Sejour - Suivi médical", $suivi_medical);

    // Interventions
    $operations = array();
    foreach ($this->loadRefsOperations() as $_operation) {
      $_operation->loadRefPlageOp(true);
      $datetime = $_operation->getFormattedValue("_datetime");
      $chir = $_operation->loadRefChir(true);
      $operations[] = "le $datetime, par $chir->_view" . ($_operation->libelle ? " : $_operation->libelle" : "");
    }
    $template->addListProperty("Sejour - Intervention - Liste", $operations);

    // Dernière intervention
    $this->_ref_last_operation->fillLimitedTemplate($template);

    // Régime
    $regimes = array();

    if ($this->hormone_croissance) {
      $regimes[] = CAppUI::tr("CSejour-hormone_croissance");
    }

    if ($this->repas_sans_sel) {
      $regimes[] = CAppUI::tr("CSejour-repas_sans_sel");
    }

    if ($this->repas_sans_porc) {
      $regimes[] = CAppUI::tr("CSejour-repas_sans_porc");
    }

    if ($this->repas_diabete) {
      $regimes[] = CAppUI::tr("CSejour-repas_diabete");
    }

    if ($this->repas_sans_residu) {
      $regimes[] = CAppUI::tr("CSejour-repas_sans_residu");
    }

    if (!count($regimes)) {
      $template->addProperty("Sejour - Régime", CAppUI::tr("CSejour-no_diet_specified"));
    }
    else {
      $template->addListProperty("Sejour - Régime", $regimes);
    }

    $this->notify("AfterFillLimitedTemplate", $template);
  }

  function fillTemplate(&$template) {

    // Chargement du fillTemplate du praticien
    $this->loadRefPraticien()->fillTemplate($template);

    // Ajout d'un fillTemplate du patient
    $this->loadRefPatient()->fillTemplate($template);

    $this->fillLimitedTemplate($template);

    // Dossier médical
    $this->loadRefDossierMedical()->fillTemplate($template, "Sejour");

    // Prescription
    if (CModule::getActive('dPprescription')) {
      $this->loadRefsPrescriptions();
      $prescription = isset($this->_ref_prescriptions["pre_admission"]) ? $this->_ref_prescriptions["pre_admission"] : new CPrescription();
      $prescription->type = "pre_admission";
      $prescription->fillLimitedTemplate($template);
      $prescription = isset($this->_ref_prescriptions["sejour"]) ? $this->_ref_prescriptions["sejour"] : new CPrescription();
      $prescription->type = "sejour";
      $prescription->fillLimitedTemplate($template);
      $prescription = isset($this->_ref_prescriptions["sortie"]) ? $this->_ref_prescriptions["sortie"] : new CPrescription();
      $prescription->type = "sortie";
      $prescription->fillLimitedTemplate($template);
    }

    // RPU
    $this->loadRefRPU();
    if ($this->_ref_rpu) {
      $this->_ref_rpu->fillLimitedTemplate($template);
    }
  }

  /**
   * Builds an array containing surgery dates
   */
  function makeDatesOperations() {
    $this->_dates_operations = array();

    // On s'assure d'avoir les opérations
    if (!$this->_ref_operations) {
      $this->loadRefsOperations();
    }

    foreach ($this->_ref_operations as $operation) {
      if ($operation->annulee) {
        continue;
      }

      // On s'assure d'avoir les plages op
      if (!$operation->_ref_plageop) {
        $operation->loadRefPlageOp();
      }

      $this->_dates_operations[$operation->_id] = CMbDT::date($operation->_datetime);
    }
  }

  /**
   * Builds an array containing consults dates
   */
  function makeDatesConsultations() {
    $this->_dates_consultations = array();

    // On s'assure d'avoir les opérations
    if (!$this->_ref_consultations) {
      $this->loadRefsConsultations();
    }

    foreach ($this->_ref_consultations as &$consultation) {
      if ($consultation->annule) {
        continue;
      }

      // On s'assure d'avoir les plages op
      if (!$consultation->_ref_plageconsult) {
        $consultation->loadRefPlageConsult();
      }

      $this->_dates_consultations[$consultation->_id] = CMbDT::date($consultation->_datetime);
    }
  }

  /**
   * Builds an array containing cancel alerts for the sejour
   * @param ref|COperation excluded_id Exclude given operation
   * @return void Valuate $this->_cancel_alert
   */
  function makeCancelAlerts($excluded_id = null) {
    $this->_cancel_alerts = array(
      "all" => array(),
      "acted" => array(),
    );

    // On s'assure d'avoir les opérations
    if (!$this->_ref_operations) {
      $this->loadRefsOperations();
    }

    if ($this->_ref_operations) {
      foreach ($this->_ref_operations as $_operation ) {
        // Needed for correct view
        $_operation->loadRefPraticien();
        $_operation->loadRefPlageOp();

        // Exclude one
        if ($_operation->_id == $excluded_id) {
          continue;
        }

        if ($_operation->annulee == 0) {
          $operation_view = " le "
            . CMbDT::dateToLocale(CMbDT::date($_operation->_datetime))
            . " par le Dr "
            . $_operation->_ref_chir->_view;
          $_operation->countActes();
          if ($_operation->_count_actes) {
            $this->_cancel_alerts["acted"][$_operation->_id] = $operation_view;
          }

          $this->_cancel_alerts["all"][$_operation->_id] = $operation_view;
        }
      }
    }
  }

  function closeSejour() {
    $where = array();
    $where[] = "sejour.patient_id = '$this->patient_id'";
    $where[] = "sejour.entree_reelle IS NOT NULL";
    $where[] = "sejour.sortie_reelle IS NULL";

    $sejours = self::loadListForDateTime($this->entree_reelle, $where);
    foreach ($sejours as $_sejour) {
      $_sejour->sortie_reelle = CMbDT::dateTime();
      $_sejour->store();
    }
  }

  /**
   * Count evenement SSR for a given date;
   * @param date $date
   * @return
   */
  function countEvenementsSSR($date) {
    if (!$this->_id) {
      return;
    }

    $evenement = new CEvenementSSR;
    $ljoin = array();
    $ljoin[] = "evenement_ssr AS evt_seance ON (evt_seance.seance_collective_id = evenement_ssr.evenement_ssr_id)";
    $where[] = "(evenement_ssr.sejour_id = '$this->_id') OR (evenement_ssr.sejour_id IS NULL AND evt_seance.sejour_id = '$this->_id')";
    $where["evenement_ssr.debut"] = "BETWEEN '$date 00:00:00' AND '$date 23:59:59'";
    return $this->_count_evenements_ssr = $evenement->countList($where, null, $ljoin);
  }

  function countEvenementsSSRWeek($kine_id, $date_min, $date_max) {
    if (!$this->_id) {
      return;
    }

    $evenement = new CEvenementSSR;
    $ljoin[] = "evenement_ssr AS evt_seance ON (evt_seance.seance_collective_id = evenement_ssr.evenement_ssr_id)";
    $where[] = "(evenement_ssr.sejour_id = '$this->_id') OR (evenement_ssr.sejour_id IS NULL AND evt_seance.sejour_id = '$this->_id')";
    $where["evenement_ssr.therapeute_id"] = "= '$kine_id'";
    $this->_count_evenements_ssr      = $evenement->countList($where, null, $ljoin);

    $where["evenement_ssr.debut"] = "BETWEEN '$date_min 00:00:00' AND '$date_max 23:59:59'";
    $this->_count_evenements_ssr_week = $evenement->countList($where, null, $ljoin);
  }

  function getNbJourPlanning($date){
    $sunday = CMbDT::date("next sunday", CMbDT::date("- 1 DAY", $date));
    $saturday = CMbDT::date("-1 DAY", $sunday);

    $_evt = new CEvenementSSR();
    $ljoin = array();
    $ljoin[] = "evenement_ssr AS evt_seance ON (evt_seance.seance_collective_id = evenement_ssr.evenement_ssr_id)";
    $where = array();
    $where["evenement_ssr.debut"] = "BETWEEN '$sunday 00:00:00' AND '$sunday 23:59:59'";
    $where[] = "(evenement_ssr.sejour_id = '$this->_id') OR (evenement_ssr.sejour_id IS NULL AND evt_seance.sejour_id = '$this->_id')";
    $count_event_sunday = $_evt->countList($where, null, $ljoin);

    $nb_days = 7;

    // Si aucun evenement le dimanche
    if (!$count_event_sunday) {
      $nb_days = 6;
      $where["evenement_ssr.debut"] = "BETWEEN '$saturday 00:00:00' AND '$saturday 23:59:59'";
      $count_event_saturday= $_evt->countList($where, null, $ljoin);
      // Aucun evenement le samedi et aucun le dimanche
      if (!$count_event_saturday) {
        $nb_days = 5;
      }
    }
    return $nb_days;
  }

  function completeLabelFields(&$fields) {
    $this->loadRefPatient()->completeLabelFields($fields);
    $this->loadRefPraticien();
    $this->loadNDA();
    $this->loadNRA();
    $affectation = $this->getCurrAffectation();
    $affectation->loadView();
    $fields = array_merge($fields,
                array("DATE ENT"         => CMbDT::dateToLocale(CMbDT::date($this->entree)),
                      "HEURE ENT"        => CMbDT::transform($this->entree, null, "%H:%M"),
                      "DATE SORTIE"      => CMbDT::dateToLocale(CMbDT::date($this->sortie)),
                      "HEURE SORTIE"     => CMbDT::transform($this->sortie, null, "%H:%M"),
                      "PRAT RESPONSABLE" => $this->_ref_praticien->_view,
                      "NDOS"             => $this->_NDA,
                      "NRA"              => $this->_ref_NRA ? $this->_ref_NRA->id400 : "",
                      "CODE BARRE NDOS"  => "@BARCODE_".$this->_NDA."@",
                      "CHAMBRE COURANTE" => $affectation->_view));

    if (CAppUI::conf("ref_pays") == 2) {
      $fields["NATURE SEJOUR"]  = $this->getFormattedValue("_type_sejour");
      $fields["MODE TRT"]       = $this->loadRefChargePriceIndicator()->code;
      $this->loadRefsFactureEtablissement();

      if ($this->_ref_last_facture) {
        $this->_ref_last_facture->loadRefAssurance();
        $fields["ASSUR MALADIE"]  = $this->_ref_last_facture->_ref_assurance_maladie->nom;
        $fields["ASSUR ACCIDENT"] = $this->_ref_last_facture->_ref_assurance_accident->nom;
      }
    }
  }

  function checkMerge($sejours = array()/*<CSejour>*/) {
    if ($msg = parent::checkMerge($sejours)) {
      return $msg;
    }
    $count_prescription = 0;
    foreach ($sejours as $_sejour) {
      $_sejour->loadRefPrescriptionSejour();
      if ($_sejour->_ref_prescription_sejour->_id) {

        // Suppression des prescriptions vide
        $prescription = new CPrescription;
        $prescription->load($_sejour->_ref_prescription_sejour->_id);
        $back_props = $prescription->getBackProps();

        $count_back_props = 0;

        // On retire les logs de la liste des backprops
        unset($back_props["logs"]);

        foreach ($back_props as $back_prop => $object) {
          $count_back_props += $prescription->countBackRefs($back_prop);
        }

        if ($count_back_props == 0) {
          $prescription->delete();
          continue;
        }

        if ($count_prescription == 1) {
          return "Impossible de fusionner des sejours qui comportent chacun des prescriptions de séjour";
        }
        $count_prescription++;
      }
    }
  }

  function docsEditable() {
    if (parent::docsEditable()) {
      return true;
    }

    $fix_edit_doc = CAppUI::conf("dPplanningOp CSejour fix_doc_edit");

    return !$fix_edit_doc ? true : $this->sortie_reelle === null;
  }

  function getUFs($date = null, $affectation_id = null) {
    if (!$date) {
      $date = CMbDT::dateTime();
    }

    if ($affectation_id) {
      $affectation = new CAffectation();
      $affectation->load($affectation_id);
    }
    else {
      // Chargement de l'affectation courante
      $affectation = $this->getCurrAffectation($date);

      // Si on n'a pas d'affectation on va essayer de chercher la première
      if (!$affectation->_id) {
        $this->loadSurrAffectations();
        $affectation = $this->_ref_next_affectation;
      }
    }

    if ($affectation->_id) {
      return $affectation->getUFs();
    }

    if ($this->uf_hebergement_id) {
      return array(
        "hebergement" => $this->loadRefUFHebergement(),
        "medicale"    => $this->loadRefUFMedicale(),
        "soins"       => $this->loadRefUFSoins(),
      );
    }

    $affectation_uf = new CAffectationUniteFonctionnelle();

    // Service
    if ($this->service_id) {
      $affectation_uf->object_id    = $this->service_id;
      $affectation_uf->object_class = "CService";
    }
    // Praticien
    else {
      $affectation_uf->object_id    = $this->loadRefPraticien()->_id;
      $affectation_uf->object_class = "CMediusers";
    }

    $affectation_uf->loadMatchingObject();

    return array(
      "hebergement" => $affectation_uf->loadRefUniteFonctionnelle(),
      "medicale"    => $this->loadRefUFMedicale(),
      "soins"       => $this->loadRefUFSoins(),
    );
  }

  function getIncrementVars() {
    return array(
      "typeHospi" => $this->type
    );
  }

  function getMovementType($code = null) {
    // Cas d'une pré-admission
    if ($this->_etat == "preadmission") {
      return "PADM";
    }

    if ($this->_etat == "encours" && ($this->service_entree_id || $code == "A02")) {
      return "MUTA";
    }

    // Cas d'une absence provisoire
    if ($code == "A21") {
      return "AABS";
    }

    // Cas d'un retour d'absence provisoire
    if ($code == "A22") {
      return "RABS";
    }

    // Cas d'une entrée autorisée
    if ($code == "A14") {
      return "EATT";
    }

    // Cas d'un transfert autorisé
    if ($code == "A15") {
      return "TATT";
    }

    // Cas d'une sortie autorisée
    if ($code == "A16") {
      return "SATT";
    }

    // Cas d'une admission
    if ($this->_etat == "encours") {
      return "ADMI";
    }

    // Cas d'une sortie
    if ($this->_etat == "cloture") {
      return "SORT";
    }
  }

  function getPrestations() {
    $this->_ref_prestations = array();

    $items_liaisons = $this->loadBackRefs("items_liaisons");

    CMbObject::massLoadFwdRef($items_liaisons, "item_souhait_id");
    CMbObject::massLoadFwdRef($items_liaisons, "item_realise_id");

    foreach ($items_liaisons as $_item_liaison) {
      $_item_souhait = $_item_liaison->loadRefItem();
      $_item_realise = $_item_liaison->loadRefItemRealise();

      if ($_item_realise->_id) {
        $this->_ref_prestations[$_item_liaison->date][] = $_item_realise;
        $_item_realise->_quantite = 1;
        $_item_realise->loadRefObject();
      }
      elseif ($_item_souhait->object_class == "CPrestationPonctuelle") {
        $this->_ref_prestations[$_item_liaison->date][] = $_item_souhait;
        $_item_souhait->_quantite = $_item_liaison->quantite;
        $_item_souhait->loadRefObject();
      }
    }
    return $this->_ref_prestations;
  }

  function loadRefFirstLiaisonForPrestation($prestation_id) {
    $this->_first_liaison_for_prestation = new CItemLiaison();
    $where = array();
    $ljoin = array();
    $where["sejour_id"] = "= '$this->_id'";
    $ljoin["item_prestation"] =
      "item_prestation.item_prestation_id = item_liaison.item_realise_id OR
       item_prestation.item_prestation_id = item_liaison.item_souhait_id";

    $where["object_class"] = " = 'CPrestationJournaliere'";
    $where["object_id"] = " = '$prestation_id'";
    $this->_first_liaison_for_prestation->loadObject($where, null, null, $ljoin);
  }

  function loadLiaisonsForPrestation($prestation_id) {
    if ($prestation_id == "all") {
      $prestation_id = null;
    }
    $item_liaison = new CItemLiaison();
    $where = array();
    $ljoin = array();

    $where["sejour_id"] = "= '$this->_id'";
    $ljoin["item_prestation"] =
      "  item_prestation.item_prestation_id = item_liaison.item_souhait_id
      OR item_prestation.item_prestation_id = item_liaison.item_realise_id";

    $where["object_class"] = " = 'CPrestationJournaliere'";
    if ($prestation_id) {
      $where["object_id"] = " = '$prestation_id'";
    }
    $this->_liaisons_for_prestation = $item_liaison->loadList($where, "date ASC", null, null, $ljoin);

    CMbObject::massLoadFwdRef($this->_liaisons_for_prestation, "item_souhait_id");
    CMbObject::massLoadFwdRef($this->_liaisons_for_prestation, "item_realise_id");

    foreach ($this->_liaisons_for_prestation as $_liaison) {
      $_liaison->loadRefItem();
      $_liaison->loadRefItemRealise();
    }
  }

  function countPrestationsSouhaitees() {
    $where["item_souhait_id"] = "IS NOT NULL";
    return $this->countBackRefs("items_liaisons", $where);
  }
  
  static function massCountPrestationSouhaitees($sejours) {
    $where["item_souhait_id"] = "IS NOT NULL";
    CStoredObject::massCountBackRefs($sejours, "items_liaisons", $where);
  }

  function loadRefsNaissances() {
    return $this->_ref_naissances = $this->loadBackRefs("naissances");
  }

  function loadRefUFHebergement($cache = true) {
    return $this->_ref_uf_hebergement = $this->loadFwdRef("uf_hebergement_id", $cache);
  }

  function loadRefUFMedicale($cache = true) {
    return $this->_ref_uf_medicale = $this->loadFwdRef("uf_medicale_id", $cache);
  }

  function loadRefUFSoins($cache = true) {
    return $this->_ref_uf_soins = $this->loadFwdRef("uf_soins_id", $cache);
  }

  /**
   * Return idex type if it's special (e.g. NDA/...)
   *
   * @param CIdSante400 $idex Idex
   *
   * @return string|null
   */
  function getSpecialIdex(CIdSante400 $idex) {
    // L'identifiant externe est le NDA
    if ($idex->tag == self::getTagNDA()) {
      return "NDA";
    }
  }
}

if (CAppUI::conf("ref_pays") == 2) {
  CSejour::$fields_etiq[] = "NATURE SEJOUR";
  CSejour::$fields_etiq[] = "MODE TRT";
  CSejour::$fields_etiq[] = "ASSUR MALADIE";
  CSejour::$fields_etiq[] = "ASSUR ACCIDENT";
}
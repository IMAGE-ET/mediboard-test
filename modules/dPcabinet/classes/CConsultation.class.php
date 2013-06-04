<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Consultation d'un patient par un praticien, éventuellement pendant un séjour
 * Un des évenements fondamentaux du dossier patient avec l'intervention
 */
class CConsultation extends CFacturable {
  const PLANIFIE       = 16;
  const PATIENT_ARRIVE = 32;
  const EN_COURS       = 48;
  const TERMINE        = 64;

  // DB Table key
  public $consultation_id;

  // DB References
  public $plageconsult_id;
  public $patient_id;
  public $sejour_id;
  public $grossesse_id;
  public $facture_id;

  // DB fields
  public $type;
  public $heure;
  public $duree;
  public $secteur1;
  public $secteur2;
  public $chrono;
  public $annule;

  public $patient_date_reglement;
  public $tiers_date_reglement;

  public $motif;
  public $rques;
  public $examen;
  public $histoire_maladie;
  public $brancardage;
  public $conclusion;

  public $traitement;
  public $premiere;
  public $derniere;
  public $adresse; // Le patient a-t'il été adressé ?
  public $adresse_par_prat_id;
  public $tarif;

  public $arrivee;
  public $categorie_id;
  public $valide; // Cotation validée
  public $si_desistement;

  public $total_assure;
  public $total_amc;
  public $total_amo;

  public $du_patient; // somme que le patient doit régler
  public $du_tiers;
  public $type_assurance;
  public $date_at;
  public $fin_at;
  public $pec_at;
  public $num_at;
  public $cle_at;
  public $reprise_at;
  public $at_sans_arret;
  public $arret_maladie;
  public $concerne_ALD;

  // Form fields
  public $_etat;
  public $_hour;
  public $_min;
  public $_check_adresse;
  public $_somme;
  public $_types_examen;
  public $_precode_acte;
  public $_exam_fields;
  public $_acte_dentaire_id;
  public $_function_secondary_id;
  public $_semaine_grossesse;
  public $_type;  // Type de la consultation
  public $_duree;
  public $_force_create_sejour;
  public $_rques_consult;
  public $_examen_consult;
  public $_line_element_id;

  // References
  /** @var CMediusers */
  public $_ref_chir;
  /** @var CPlageconsult */
  public $_ref_plageconsult;
  /** @var CMedecin */
  public $_ref_adresse_par_prat;
  /** @var CGroups */
  public $_ref_group;
  /** @var CConsultAnesth */
  public $_ref_consult_anesth;
  /** @var CExamAudio */
  public $_ref_examaudio;
  /** @var CExamNyha */
  public $_ref_examnyha;
  /** @var CExamPossum */
  public $_ref_exampossum;
  /** @var CGrossesse */
  public $_ref_grossesse;
  /** @var CFactureCabinet */
  public $_ref_facture;
  /** @var CPrescription */
  public $_ref_prescription;
  /** @var CConsultationCategorie */
  public $_ref_categorie;

  // Collections
  /** @var CConsultAnesth[] */
  public $_refs_dossiers_anesth;
  /** @var CReglement[] */
  public $_ref_reglements;
  /** @var CReglement[] */
  public $_ref_reglements_patient;
  /** @var CReglement[] */
  public $_ref_reglements_tiers;
  /** @var  CExamComp[] */
  public $_ref_examcomp;

  // Counts
  public $_count_fiches_examen;
  public $_count_matching_sejours;
  public $_count_prescriptions;

  // FSE
  public $_bind_fse;
  public $_ids_fse;
  public $_ext_fses;
  public $_current_fse;
  public $_fse_intermax;

  // Distant fields
  public $_date;
  public $_datetime;
  public $_date_fin;
  public $_is_anesth;
  public $_is_dentiste;
  public $_du_restant_patient;
  public $_du_restant_tiers;
  public $_reglements_total_patient;
  public $_reglements_total_tiers;
  public $_forfait_se;
  public $_forfait_sd;
  public $_facturable;

  // Filter Fields
  public $_date_min;
  public $_date_max;
  public $_prat_id;
  public $_etat_reglement_patient;
  public $_etat_reglement_tiers;
  public $_type_affichage;
  public $_telephone;
  public $_coordonnees;
  public $_plages_vides;
  public $_empty_places;
  public $_non_pourvues;

  // Behaviour fields
  public $_adjust_sejour;
  public $_operation_id;
  public $_dossier_anesth_completed_id;
  public $_docitems_from_dossier_anesth;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    
    $spec->table = 'consultation';
    $spec->key   = 'consultation_id';
    $spec->measureable = true;
    $spec->events = array(
      "examen" => array(
        "reference1" => array("CSejour",  "sejour_id"),
        "reference2" => array("CPatient", "patient_id"),
      ),
    );
    
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    
    $backProps["consult_anesth"]    = "CConsultAnesth consultation_id";
    $backProps["examaudio"]         = "CExamAudio consultation_id";
    $backProps["examcomp"]          = "CExamComp consultation_id";
    $backProps["examnyha"]          = "CExamNyha consultation_id";
    $backProps["exampossum"]        = "CExamPossum consultation_id";
    $backProps["prescriptions"]     = "CPrescription object_id";
    $backProps["actes_dentaires"]   = "CActeDentaire consult_id";
    $backProps["echanges_hprimxml"] = "CEchangeHprim object_id";
    $backProps["exchanges_ihe"]     = "CExchangeIHE object_id";
    $backProps["fse_pyxvital"]      = "CPvFSE consult_id";
    
    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    
    $props["sejour_id"]         = "ref class|CSejour";
    $props["plageconsult_id"]   = "ref notNull class|CPlageconsult seekable show|1";
    $props["patient_id"]        = "ref class|CPatient purgeable seekable show|1";
    $props["categorie_id"]      = "ref class|CConsultationCategorie show|1";
    $props["grossesse_id"]      = "ref class|CGrossesse show|0 unlink";
    $props["facture_id"]        = "ref class|CFactureCabinet show|0 nullify";

    $props["motif"]             = "text helped seekable";
    $props["type"]              = "enum list|classique|entree|chimio default|classique";
    $props["heure"]             = "time notNull show|0";
    $props["duree"]             = "num min|1 max|15 notNull default|1 show|0";
    $props["secteur1"]          = "currency min|0 show|0";
    $props["secteur2"]          = "currency show|0";
    $props["chrono"]            = "enum notNull list|16|32|48|64 show|0";
    $props["annule"]            = "bool show|0";
    $props["_etat"]             = "str";

    $props["rques"]             = "text helped seekable";
    $props["examen"]            = "text helped seekable show|0";
    $props["traitement"]        = "text helped seekable";
    $props["histoire_maladie"]  = "text helped seekable";
    $props["brancardage"]       = "text helped seekable";
    $props["conclusion"]        = "text helped seekable";

    $props["facture"]           = "bool default|0 show|0";

    $props["premiere"]            = "bool show|0";
    $props["derniere"]            = "bool show|0";
    $props["adresse"]             = "bool show|0";
    $props["adresse_par_prat_id"] = "ref class|CMedecin";
    $props["tarif"]               = "str show|0";
    $props["arrivee"]             = "dateTime show|0";
    $props["concerne_ALD"]        = "bool";

    $props["patient_date_reglement"]    = "date show|0";
    $props["tiers_date_reglement"]      = "date show|0";
    $props["du_patient"]                = "currency show|0";
    $props["du_tiers"  ]                = "currency show|0";

    $props["type_assurance"] = "enum list|classique|at|maternite|smg";
    $props["date_at"]  = "date";
    $props["fin_at"]   = "dateTime";
    $props["num_at"]   = "num length|8";
    $props["cle_at"]   = "num length|1";

    $props["pec_at"]   = "enum list|soins|arret";
    $props["reprise_at"] = "dateTime";
    $props["at_sans_arret"] = "bool default|0";
    $props["arret_maladie"] = "bool default|0";

    $props["total_amo"]         = "currency show|0";
    $props["total_amc"]         = "currency show|0";
    $props["total_assure"]      = "currency show|0";

    $props["valide"]            = "bool show|0";
    $props["si_desistement"]    = "bool notNull default|0";

    $props["_du_restant_patient"]       = "currency";
    $props["_du_restant_tiers"]         = "currency";
    $props["_reglements_total_patient"] = "currency";
    $props["_reglements_total_tiers"  ] = "currency";
    $props["_etat_reglement_patient"]   = "enum list|reglee|non_reglee";
    $props["_etat_reglement_tiers"  ]   = "enum list|reglee|non_reglee";
    $props["_forfait_se"]               = "bool default|0";
    $props["_forfait_sd"]               = "bool default|0";
    $props["_facturable"]               = "bool default|1";

    $props["_date"]             = "date";
    $props["_datetime"]         = "dateTime show|1";
    $props["_date_min"]         = "date";
    $props["_date_max"]         = "date moreEquals|_date_min";
    $props["_type_affichage"]   = "enum list|complete|totaux";
    $props["_telephone"]        = "bool default|0";
    $props["_coordonnees"]      = "bool default|0";
    $props["_plages_vides"]     = "bool default|1";
    $props["_non_pourvues"]     = "bool default|1";

    $props["_check_adresse"]    = "";
    $props["_somme"]            = "currency";
    $props["_type"]             = "enum list|urg|anesth";

    $props["_prat_id"]               = "ref class|CMediusers";
    $props["_acte_dentaire_id"]      = "ref class|CActeDentaire";
    $props["_praticien_id"]          = "ref class|CMediusers show|1";
    $props["_function_secondary_id"] = "ref class|CFunctions";

    return $props;
  }

  /**
   * Calcule l'état visible d'une consultation
   *
   * @return string
   */
  function getEtat() {
    $etat = array();
    $etat[self::PLANIFIE]       = "Plan.";
    $etat[self::PATIENT_ARRIVE] = CMbDT::transform(null, $this->arrivee, "%Hh%M");
    $etat[self::EN_COURS]       = "En cours";
    $etat[self::TERMINE]        = "Term.";

    if ($this->chrono) {
      $this->_etat = $etat[$this->chrono];
    }

    if ($this->annule) {
      $this->_etat = "Ann.";
    }

    return $this->_etat;
  }

  /**
   * @see parent::getTemplateClasses()
   */
  function getTemplateClasses() {
    $this->loadRefsFwd();

    $tab = array();

    // Stockage des objects liés à l'opération
    $tab['CConsultation'] = $this->_id;
    $tab['CPatient'] = $this->_ref_patient->_id;

    $tab['CConsultAnesth'] = 0;
    $tab['COperation'] = 0;
    $tab['CSejour'] = 0;

    return $tab;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_somme = $this->secteur1 + $this->secteur2;
    if ($this->patient_date_reglement === "0000-00-00") {
      $this->patient_date_reglement = null;
    }

    $this->du_patient = round($this->du_patient, 2);
    $this->du_tiers   = round($this->du_tiers  , 2);

    $this->_hour = intval(substr($this->heure, 0, 2));
    $this->_min  = intval(substr($this->heure, 3, 2));
    $this->_check_adresse = $this->adresse;

    $this->_view = "Consultation " . $this->getEtat();

    // si _coded vaut 1 alors, impossible de modifier la cotation
    $this->_coded = $this->valide;

    // pour récuperer le praticien depuis la plage consult
    $this->loadRefPlageConsult(true);
    $plageconsult = $this->_ref_plageconsult;

    $time = CMbDT::time("+".CMbDT::minutesRelative("00:00:00", $plageconsult->freq)*$this->duree." MINUTES", $this->heure);
    $this->_date_fin = "$plageconsult->date $time";
    
    $this->_duree = CMbDT::minutesRelative("00:00:00", $plageconsult->freq) * $this->duree;
    
    $this->_exam_fields = $this->getExamFields();
  }

  /**
   * @see parent::updatePlainFields()
   */
  function updatePlainFields() {
    if (($this->_hour !== null) && ($this->_min !== null)) {
      $this->heure = sprintf("%02d:%02d:00", $this->_hour, $this->_min);
    }

    // Liaison FSE prioritaire sur l'état
    if ($this->_bind_fse) {
      $this->valide = 0;
    }

    // Cas du paiement d'un séjour
    if ($this->sejour_id !== null && $this->sejour_id && $this->secteur1 !== null && $this->secteur2 !== null) {
      $this->du_tiers = $this->secteur1 + $this->secteur2;
      $this->du_patient = 0;
    }
  }

  /**
   * @see parent::check()
   */
  function check() {
    // Data checking
    $msg = null;
    if (!$this->_id) {
      if (!$this->plageconsult_id) {
        $msg .= "Plage de consultation non valide<br />";
      }
      return $msg . parent::check();
    }

    $this->loadOldObject();
    $this->loadRefsReglements();

    $this->completeField("sejour_id", "plageconsult_id");

    if ($this->sejour_id) {
      $this->loadRefPlageConsult();
      $sejour = $this->loadRefSejour();

      if (
          $sejour->type != "consult" &&
          ($this->_date < CMbDT::date($sejour->entree) || CMbDT::date($this->_date) > $sejour->sortie)
      ) {
        $msg .= "Consultation en dehors du séjour<br />";
        return $msg . parent::check();
      }
    }

    /** @var self $old */
    $old = $this->_old;
    // Dévalidation avec règlement déjà effectué
    if ($this->fieldModified("valide", "0")) {
      // Bien tester sur _old car valide = 0 s'accompagne systématiquement d'un facture_id = 0
      if ($old->loadRefFacture()->countBackRefs("reglements")) {
        $msg .= "Vous ne pouvez plus dévalider le tarif, des règlements de factures ont déjà été effectués";
      }
    }

    if (!($this->_merging || $this->_mergeDeletion) && $old->valide === "1" && $this->valide === "1") {
      // Modification du tarif déjà validé
      if (
          $this->fieldModified("secteur1") ||
          $this->fieldModified("secteur2") ||
          $this->fieldModified("total_assure") ||
          $this->fieldModified("total_amc") ||
          $this->fieldModified("total_amo") ||
          $this->fieldModified("du_patient") ||
          $this->fieldModified("du_tiers")
      ) {
        $msg .= "Vous ne pouvez plus modifier le tarif, il est déjà validé";
      }
    }

    return $msg . parent::check();
  }

  /**
   * @see parent::loadView()
   */
  function loadView() {
    parent::loadView();
    $this->loadRefPatient()->loadRefPhotoIdentite();
    $this->loadRefsFichesExamen();
    $this->loadRefsActesNGAP();
    $this->loadRefCategorie();
    $this->loadRefPlageConsult(1);
    $this->_ref_chir->loadRefFunction();
  }

  /**
   * @see parent::deleteActes()
   */
  function deleteActes() {
    if ($msg = parent::deleteActes()) {
      return $msg;
    }

    $this->secteur1 = "";
    $this->secteur2 = "";
    // $this->valide = 0;  Ne devrait pas être nécessaire
    $this->total_assure = 0.0;
    $this->total_amc = 0.0;
    $this->total_amo = 0.0;
    $this->du_patient = 0.0;
    $this->du_tiers = 0.0;

    return $this->store();
  }

  /**
   * @see parent::bindTarif()
   */
  function bindTarif() {
    $this->_bind_tarif = false;

    // Chargement du tarif
    $tarif = new CTarif();
    $tarif->load($this->_tarif_id);

    // Cas de la cotation poursuivie
    if ($this->tarif == "pursue") {
      $this->secteur1 += $tarif->secteur1;
      $this->secteur2 += $tarif->secteur2;
      $this->tarif     = "composite";
    }
    // Cas de la cotation normale
    else {
      $this->secteur1 = $tarif->secteur1;
      $this->secteur2 = $tarif->secteur2;
      $this->tarif    = $tarif->description;
    }

    $this->du_patient   = $this->secteur1 + $this->secteur2;

    // Mise à jour de codes CCAM prévus, sans information serialisée complémentaire
    foreach ($tarif->_codes_ccam as $_code_ccam) {
      $this->_codes_ccam[] = substr($_code_ccam, 0, 7);
    }
    $this->codes_ccam = implode("|", $this->_codes_ccam);
    if ($msg = $this->store()) {
      return $msg;
    }

    // Precodage des actes NGAP avec information sérialisée complète
    $this->_tokens_ngap = $tarif->codes_ngap;
    if ($msg = $this->precodeActe("_tokens_ngap", "CActeNGAP", $this->getExecutantId())) {
      return $msg;
    }

    $this->codes_ccam = $tarif->codes_ccam;
    // Precodage des actes CCAM avec information sérialisée complète
    if ($msg = $this->precodeActeCCAM()) {
      return $msg;
    }

    if (CModule::getActive("tarmed")) {
      $this->_tokens_tarmed = $tarif->codes_tarmed;
      if ($msg = $this->precodeActe("_tokens_tarmed", "CActeTarmed", $this->getExecutantId())) {
        return $msg;
      }
      $this->_tokens_caisse = $tarif->codes_caisse;
      if ($msg = $this->precodeActe("_tokens_caisse", "CActeCaisse", $this->getExecutantId())) {
        return $msg;
      }
    }

    return null;
  }

  /**
   * Précode les actes CCAM prévus de la consultation
   *
   * @return string Store-like message
   */
  function precodeActeCCAM() {
    $this->loadRefPlageConsult();
    $this->precodeCCAM($this->_ref_chir->_id);
  }

  /**
   * @see parent::doUpdateMontants()
   */
  function doUpdateMontants() {
    // Initialisation des montants
    $secteur1_NGAP    = 0;
    $secteur1_CCAM    = 0;
    $secteur1_TARMED  = 0;
    $secteur1_CAISSE  = 0;
    $secteur2_NGAP    = 0;
    $secteur2_CCAM    = 0;
    $secteur2_TARMED  = 0;
    $secteur2_CAISSE  = 0;
    $count_actes = 0;

    if (CModule::getActive("tarmed") && CAppUI::conf("tarmed CCodeTarmed use_cotation_tarmed")) {
      // Chargement des actes Tarmed
      $totaux_tarmed = $this->loadRefsActesTarmed();
      $count_actes += count($this->_ref_actes_tarmed);
      $secteur1_TARMED += round($totaux_tarmed["base"], 2);
      $secteur2_TARMED += round($totaux_tarmed["dh"], 2);
      
      $totaux_caisse = $this->loadRefsActesCaisse();
      $count_actes += count($this->_ref_actes_caisse);
      $secteur1_CAISSE += round($totaux_caisse["base"] , 2);
      $secteur2_CAISSE += round($totaux_caisse["dh"] , 2);
    }

    // Chargement des actes NGAP
    $this->loadRefsActesNGAP();
    foreach ($this->_ref_actes_ngap as $acteNGAP) {
      $count_actes++;
      $secteur1_NGAP += $acteNGAP->montant_base;
      $secteur2_NGAP += $acteNGAP->montant_depassement;
    }

    // Chargement des actes CCAM
    $this->loadRefsActesCCAM();
    foreach ($this->_ref_actes_ccam as $acteCCAM) {
      $count_actes++;
      $secteur1_CCAM += $acteCCAM->montant_base;
      $secteur2_CCAM += $acteCCAM->montant_depassement;
    }

    // Remplissage des montant de la consultation
    $this->secteur1 = $secteur1_NGAP + $secteur1_CCAM + $secteur1_TARMED + $secteur1_CAISSE;
    $this->secteur2 = $secteur2_NGAP + $secteur2_CCAM + $secteur2_TARMED + $secteur2_CAISSE;

    if ($secteur1_NGAP == 0 && $secteur1_CCAM == 0 && $secteur2_NGAP==0 && $secteur2_CCAM ==0) {
      $this->du_patient = $this->secteur1 + $this->secteur2;
    }

    // Cotation manuelle
    $this->completeField("tarif");
    if (!$this->tarif && $count_actes) {
      $this->tarif = "Cotation manuelle";
    }

    return $this->store();

  }

  /**
   * @see parent::store()
   * @todo Refactoring complet de la fonction store de la consultation
   *
   *   ANALYSE DU CODE
   *  1. Gestion du désistement
   *  2. Premier if : creation d'une consultation à laquelle on doit attacher
   *     un séjour (conf active): comportement DEPART / ARRIVEE
   *  3. Mise en cache du forfait FSE et facturable : uniquement dans le cas d'un séjour
   *  4. On load le séjour de la consultation
   *  5. On initialise le _adjust_sejour à false
   *  6. Dans le cas ou on a un séjour
   *   6.1. S'il est de type consultation, on ajuste le séjour en fonction du comportement DEPART / ARRIVEE
   *   6.2. Si la plage de consultation a été modifiée, adjust_sejour passe à true et on ajuste le séjour
   *        en fonction du comportement DEPART / ARRIVEE (en passant par l'adjustSejour() )
   *   6.3. Si on a un id (à virer) et que le chrono est modifié en PATIENT_ARRIVE,
   *        si on gère les admissions auto (conf) on met une entrée réelle au séjour
   *  7. Si le patient est modifié, qu'on est pas en train de merger et qu'on a un séjour,
   *     on empeche le store
   *  8. On appelle le parent::store()
   *  9. On passe le forfait SE et facturable au séjour
   * 10. On propage la modification du patient de la consultation au séjour
   * 11. Si on a ajusté le séjour et qu'on est dans un séjour de type conclut et que le séjour
   *     n'a plus de consultations, on essaie de le supprimer, sinon on l'annule
   * 12. Gestion du tarif et précodage des actes (bindTarif)
   * 13. Bind FSE

   * ACTIONS
   * - Faire une fonction comportement_DEPART_ARRIVEE()
   * - Merger le 2, le 6.1 et le 6.2 (et le passer en 2 si possible)
   * - Faire une fonction pour le 6.3, le 7, le 10, le 11
   * - Améliorer les fonctions 12 et 13 en incluant le test du behaviour fields
   *
   * COMPORTEMENT DEPART ARRIVEE
   * modif de la date d'une consultation ayant un séjour sur le modèle DEPART / ARRIVEE:
   * 1. Pour le DEPART :
   * -> on décroche la consultation de son ancien séjour
   * -> on ne touche pas à l'ancien séjour si :
   * - il est de type autre que consultation
   * - il a une entrée réelle
   * - il a d'autres consultations
   * -> sinon on l'annule
   *
   *   2. Pour l'ARRIVEE
   * -> si on a un séjour qui englobe : on la colle dedans
   * -> sinon on crée un séjour de consultation
   *
   *   TESTS A EFFECTUER
   *  0. Création d'un pause
   *  0.1. Déplacement d'une pause
   *  1. Création d'une consultation simple C1 (Séjour S1)
   *  2. Création d'une deuxième consultation le même jour / même patient C2 (Séjour S1)
   *  3. Création d'une troisième consultation le même jour / même patient C3 (Séjour S1)
   *  4. Déplacement de la consultation C1 un autre jour (Séjour S2)
   *  5. Changement du nom du patient C2 (pas de modification car une autre consultation)
   *  6. Déplacement de C3 au même jour (Toujours séjour S1)
   *  7. Annulation de C1 (Suppression ou annulation de S1)
   *  8. Déplacement de C2 et C3 à un autre jour (séjour S3 créé, séjour S1 supprimé ou annulé)
   *  9. Arrivée du patient pour C2 (S3 a une entrée réelle)
   * 10. Déplacement de C3 dans un autre jour (S4)
   * 11. Déplacement de C2 dans un autre jour (S5 et S3 reste tel quel)
   */
  function store() {
    $this->completeField('sejour_id', 'heure', 'plageconsult_id', 'si_desistement');

    if ($this->si_desistement === null) {
      $this->si_desistement = 0;
    }

    // Consultation dans un séjour
    $sejour = new CSejour();
    if (
        (!$this->_id && !$this->sejour_id && CAppUI::conf("dPcabinet CConsultation attach_consult_sejour") && $this->patient_id)
        || $this->_force_create_sejour
    ) {
      // Recherche séjour englobant
      $facturable = $this->_facturable;
      if ($facturable === null) {
        $facturable = 1;
      }

      $this->loadRefPlageConsult();

      $function = new CFunctions;

      if ($this->_function_secondary_id) {
        $function->load($this->_function_secondary_id);
      }
      else {
        $user = new CMediusers;
        $user->load($this->_ref_chir->_id);
        $function->load($user->function_id);
      }

      $datetime = $this->_datetime;
      $minutes_before_consult_sejour = CAppUI::conf("dPcabinet CConsultation minutes_before_consult_sejour");
      $where = array();
      $where['annule']     = " = '0'";
      $where['type']       = " != 'seances'";
      $where['patient_id'] = " = '$this->patient_id'";
      if (!CAppUI::conf("dPcabinet CConsultation search_sejour_all_groups")) {
        $where['group_id'] = " = '$function->group_id'";
      }
      $where['facturable'] = " = '$facturable'";
      $datetime_before     = CMbDT::dateTime("+$minutes_before_consult_sejour minute", "$this->_date $this->heure");
      $where[] = "`sejour`.`entree` <= '$datetime_before' AND `sejour`.`sortie` >= '$datetime'";

      if (!$this->_force_create_sejour) {
        $sejour->loadObject($where);
      }

      // Si pas de séjour et config alors le créer en type consultation
      if (!$sejour->_id && CAppUI::conf("dPcabinet CConsultation create_consult_sejour")) {
        $sejour->patient_id = $this->patient_id;
        $sejour->praticien_id = $this->_ref_chir->_id;
        $sejour->group_id = $function->group_id;
        $sejour->type = "consult";
        $sejour->facturable = $facturable;
        $datetime = ($this->_date && $this->heure) ? "$this->_date $this->heure" : null;
        if ($this->chrono == self::PLANIFIE) {
          $sejour->entree_prevue = $datetime;
        }
        else {
          $sejour->entree_reelle = $datetime;
        }
        $sejour->sortie_prevue = "$this->_date 23:59:59";
        if ($msg = $sejour->store()) {
          return $msg;
        }
      }
      $this->sejour_id = $sejour->_id;
    }

    // must be BEFORE loadRefSejour()
    $facturable  = $this->_facturable;
    $forfait_se  = $this->_forfait_se;
    $forfait_sd  = $this->_forfait_sd;

    $this->_adjust_sejour = false;
    $this->loadRefSejour();
    if ($this->sejour_id) {
      $this->loadRefPlageConsult();

      // Si le séjour est de type consult
      if ($this->_ref_sejour->type == 'consult') {
        $this->_ref_sejour->loadRefsConsultations();
        $this->_ref_sejour->_hour_entree_prevue = null;
        $this->_ref_sejour->_min_entree_prevue  = null;
        $this->_ref_sejour->_hour_sortie_prevue = null;
        $this->_ref_sejour->_min_sortie_prevue  = null;

        $date_consult = CMbDT::date($this->_datetime);

        // On déplace l'entrée et la sortie du séjour
        $entree = $this->_datetime;
        $sortie = $date_consult . " 23:59:59";

        // Si on a une entrée réelle et que la date de la consultation est avant l'entrée réelle, on sort du store
        if ($this->_ref_sejour->entree_reelle && $date_consult < CMbDT::date($this->_ref_sejour->entree_reelle)) {
          return CAppUI::tr("CConsultation-denyDayChange");
        }

        // Si on a une sortie réelle et que la date de la consultation est après la sortie réelle, on sort du store
        if ($this->_ref_sejour->sortie_reelle && $date_consult > CMbDT::date($this->_ref_sejour->sortie_reelle)) {
          return CAppUI::tr("CConsultation-denyDayChange-exit");
        }

        // S'il n'y a qu'une seule consultation dans le séjour, et que le praticien de la consultation est modifié
        // (changement de plage), alors on modifie également le praticien du séjour
        if (
            $this->_id && $this->fieldModified("plageconsult_id")
            && count($this->_ref_sejour->_ref_consultations) == 1
            && !$this->_ref_sejour->entree_reelle
        ) {
          $this->_ref_sejour->praticien_id = $this->_ref_plageconsult->chir_id;
        }

        // S'il y a d'autres consultations dans le séjour, on étire l'entrée et la sortie
        // en parcourant la liste des consultations
        foreach ($this->_ref_sejour->_ref_consultations as $_consultation) {
          if ($_consultation->_id != $this->_id) {
            $_consultation->loadRefPlageConsult();
            if ($_consultation->_datetime < $entree) {
              $entree = $_consultation->_datetime;
            }

            if ($_consultation->_datetime > $sortie) {
              $sortie = CMbDT::date($_consultation->_datetime) . " 23:59:59";
            }
          }
        }

        $this->_ref_sejour->entree_prevue = $entree;
        $this->_ref_sejour->sortie_prevue = $sortie;
        $this->_ref_sejour->updateFormFields();
        $this->_ref_sejour->_check_bounds = 0;
        $this->_ref_sejour->store();
      }

      // Changement de journée pour la consult
      if ($this->fieldModified("plageconsult_id")) {
        $this->_adjust_sejour = true;

        // Pas le permettre si admission est déjà faite
        $max_hours = CAppUI::conf("dPcabinet CConsultation hours_after_changing_prat");
        if (
            $this->_ref_sejour->entree_reelle
            && CMbDT::dateTime("+ $max_hours HOUR", $this->_ref_sejour->entree_reelle) < CMbDT::dateTime()
        ) {
          return CAppUI::tr("CConsultation-denyPratChange", $max_hours);
        }

        $this->loadRefPlageConsult();
        $dateTimePlage = $this->_datetime;
        $where = array();
        $where['patient_id']   = " = '$this->patient_id'";
        $where[] = "`sejour`.`entree` <= '$dateTimePlage' AND `sejour`.`sortie` >= '$dateTimePlage'";

        $sejour = new CSejour();
        $sejour->loadObject($where);

        $this->adjustSejour($sejour, $dateTimePlage);
      }

      if ($this->_id && $this->fieldModified("chrono", self::PATIENT_ARRIVE)) {
        $this->completeField("plageconsult_id");
        $this->loadRefPlageConsult();
        $this->_ref_chir->loadRefFunction();
        $function = $this->_ref_chir->_ref_function;
        if ($function->admission_auto) {
          $sejour = new CSejour();
          $sejour->load($this->sejour_id);
          $sejour->entree_reelle = $this->arrivee;
          if ($msg = $sejour->store()) {
            return $msg;
          }
        }
      }
    }

    $patient_modified = $this->fieldModified("patient_id");

    // Si le patient est modifié et qu'il y a plus d'une consult dans le sejour, on empeche le store
    if (!$this->_forwardRefMerging && $this->sejour_id && $patient_modified) {
      $this->loadRefSejour();

      $consultations = $this->_ref_sejour->countBackRefs("consultations");
      if ($consultations > 1) {
        return "D'autres consultations sont prévues dans ce séjour, impossible de changer le patient.";
      }
    }

    // Synchronisation AT
    $this->getType();

    if ($this->_type === "urg" && $this->fieldModified("date_at")) {
      $rpu = $this->_ref_sejour->_ref_rpu;
      if (!$rpu->_date_at) {
        $rpu->_date_at = true;
        $rpu->date_at = $this->date_at;
        if ($msg = $rpu->store()) {
          return $msg;
        }
      }
    }

    // Update de reprise at
    // Par défaut, j+1 par rapport à fin at
    if ($this->fieldModified("fin_at") && $this->fin_at) {
      $this->reprise_at = CMbDT::dateTime("+1 DAY", $this->fin_at);
    }
    
    //Lors de la validation de la consultation
    // Enregistrement de la facture
    if ($this->fieldModified("valide", "1")) {
      $facture = new CFactureCabinet();
      $facture->_consult_id = $this->_id;
      $facture->du_patient  = $this->du_patient;
      $facture->du_tiers    = $this->du_tiers;
      $facture->store();
      if (CModule::getActive("dPfacturation")) {
        $ligne = new CFactureLiaison();
        $ligne->facture_id    = $facture->_id;
        $ligne->facture_class = $facture->_class;
        $ligne->object_id     = $this->_id;
        $ligne->object_class  = 'CConsultation';
        $ligne->store();
      }
      else {
        $this->facture_id = $facture->_id;
      }
    }

    //Lors de dévalidation de la consultation 
    if ($this->fieldModified("valide", "0")) {
      $facture = $this->loadRefFacture();
      $facture->_consult_id = $this->_id;
      $facture->cancelConsult();
    }
    
    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }

    $this->completeField("_line_element_id");

    // Création d'une tâche si la prise de rdv est issue du plan de soin
    if ($this->_line_element_id) {
      $task = new CSejourTask();
      $task->consult_id = $this->_id;
      $task->sejour_id = $this->sejour_id;
      $task->prescription_line_element_id = $this->_line_element_id;
      $task->description = "Consultation prévue le ".$this->_ref_plageconsult->getFormattedValue("date");
      if ($msg = $task->store()) {
        return $msg;
      }
    }

    // On note le résultat de la tâche si la consultation est terminée
    if ($this->chrono == CConsultation::TERMINE) {
      /** @var $task CSejourTask */
      $task = $this->loadRefTask();
      if ($task->_id) {
        $task->resultat = "Consultation terminée";
        $task->realise = 1;
        if ($msg = $task->store()) {
          return $msg;
        }
      }
    }

    if (CAppUI::pref("create_dossier_anesth")) {
      $this->createConsultAnesth();
    }

    // Forfait SE et facturable. A laisser apres le store()
    if ($this->sejour_id && CAppUI::conf("dPcabinet CConsultation attach_consult_sejour")) {
      if ($forfait_se !== null || $facturable !== null || $forfait_sd !== null) {
        $this->_ref_sejour->forfait_se = $forfait_se;
        $this->_ref_sejour->forfait_sd = $forfait_sd;
        $this->_ref_sejour->facturable = $facturable;
        if ($msg = $this->_ref_sejour->store()) {
          return $msg;
        }
        $this->_forfait_se = null;
        $this->_forfait_sd = null;
        $this->_facturable = null;
      }
    }

    // Changement du patient pour la consult
    if ($this->sejour_id && $patient_modified) {
      $this->loadRefSejour();

      // Si patient est différent alors on met a jour le sejour
      if ($this->_ref_sejour->patient_id != $this->patient_id) {
        $this->_ref_sejour->patient_id = $this->patient_id;
        if ($msg = $this->_ref_sejour->store()) {
          return $msg;
        }
      }
    }

    if ($this->_adjust_sejour && ($this->_ref_sejour->type === "consult") && $sejour->_id) {
      $consultations = $this->_ref_sejour->countBackRefs("consultations");
      if ($consultations < 1) {
        if ($msg = $this->_ref_sejour->delete()) {
          $this->_ref_sejour->annule = 1;
          if ($msg = $this->_ref_sejour->store()) {
            return $msg;
          }
        }
      }
    }

    // Gestion du tarif et precodage des actes
    if ($this->_bind_tarif && $this->_id) {
      if ($msg = $this->bindTarif()) {
        return $msg;
      }
    }

    // Bind FSE
    if ($this->_bind_fse && $this->_id) {
      if (CModule::getActive("fse")) {
        $fse = CFseFactory::createFSE();
        if ($fse) {
          $fse->bindFSE($this);
        }
      }
    }

    return null;
  }

  /**
   * Charge la catégorie de la consultation
   *
   * @param bool $cache Utilise le cache
   *
   * @return CConsultationCategorie
   */
  function loadRefCategorie($cache = true) {
    return $this->_ref_categorie = $this->loadFwdRef("categorie_id", $cache);
  }

  /**
   * @see parent::loadComplete()
   */
  function loadComplete() {
    parent::loadComplete();
    $this->_ref_patient->loadRefConstantesMedicales();
    foreach ($this->_ref_actes_ccam as $_acte) {
      $_acte->loadRefExecutant();
    }
    $this->loadRefConsultAnesth();
    foreach ($this->_refs_dossiers_anesth as $_dossier_anesth) {
      $_dossier_anesth->loadRefOperation();
    }
  }

  /**
   * Charge le patient
   *
   * @param bool $cache Use cache
   *
   * @return CPatient
   */
  function loadRefPatient($cache = true) {
    return $this->_ref_patient = $this->loadFwdRef("patient_id", $cache);
  }

  /**
   * Chargement du sejour et du RPU dans le cas d'une urgence
   *
   * @param bool $cache Use cache
   *
   * @return CSejour
   */
  function loadRefSejour($cache = true) {
    /** @var CSejour $sejour */
    $sejour = $this->loadFwdRef("sejour_id", $cache);
    $sejour->loadRefRPU();

    if (CAppUI::conf("dPcabinet CConsultation attach_consult_sejour")) {
      $this->_forfait_se = $sejour->forfait_se;
      $this->_forfait_sd = $sejour->forfait_sd;
      $this->_facturable = $sejour->facturable;
    }

    return $this->_ref_sejour = $sejour;
  }

  /**
   * Charge la grossesse associée au séjour
   *
   * @return CGrossesse
   */
  function loadRefGrossesse() {
    return $this->_ref_grossesse = $this->loadFwdRef("grossesse_id", true);
  }

  /**
   * Charge la facture de cabinet associée à la consultation
   *
   * @return CFactureCabinet
   */
  function loadRefFacture() {
    if ($this->_ref_facture) {
      return $this->_ref_facture;
    }

    if (CModule::getActive("dPfacturation")) {
      $liaison = new CFactureLiaison();
      $liaison->setObject($this);
      $liaison->facture_class = "CFactureCabinet";
      if ($liaison->loadMatchingObject()) {
        return $this->_ref_facture = $liaison->loadRefFacture();
      }
    }
    if (!$this->_ref_facture) {
      return $this->_ref_facture = $this->loadFwdRef("facture_id", true);
    }

    return $this->_ref_facture;
  }

  /**
   * Charge l'établissement indirectement associée à la consultation
   *
   * @todo Prendre en compte le cas de la consultation liée à un séjour dans un établissement
   * @return CGroups
   */
  function loadRefGroup() {
    return $this->_ref_group = $this->loadRefPraticien()->loadRefFunction()->loadRefGroup();
  }

  /**
   * @see parent::getActeExecution()
   */
  function getActeExecution() {
    $this->loadRefPlageConsult();
    return $this->_acte_execution;
  }

  /**
   * @see parent::getExecutantId()
   */
  function getExecutantId($code_activite = null) {
    $this->loadRefPlageConsult();
    return $this->_praticien_id;
  }

  /**
   * Charge la plage de consultation englobante
   *
   * @param boolean $cache [optional] Use cache
   *
   * @return CPlageconsult
   */
  function loadRefPlageConsult($cache = true) {
    if ($this->_ref_plageconsult) {
      return $this->_ref_plageconsult;
    }

    $this->completeField("plageconsult_id");
    /** @var CPlageConsult $plage */
    $plage = $this->loadFwdRef("plageconsult_id", $cache);
    $plage->loadRefsFwd($cache);

    // Distant fields
    $chir = $plage->_ref_remplacant->_id ?
      $plage->_ref_remplacant :
      $plage->_ref_chir;

    $this->_date     = $plage->date;
    $this->_datetime = CMbDT::addDateTime($this->heure, $this->_date);
    $this->_acte_execution = $this->_datetime;
    $this->_is_anesth    = $chir->isAnesth();
    $this->_is_dentiste  = $chir->isDentiste();
    $this->_praticien_id = $chir->_id;

    $this->_ref_chir = $chir;
    return $this->_ref_plageconsult = $plage;
  }

  /**
   * @see parent::loadRefPraticien()
   */
  function loadRefPraticien(){
    $this->loadRefPlageConsult();
    $this->_ref_executant = $this->_ref_plageconsult->_ref_chir;

    return $this->_ref_praticien = $this->_ref_chir;
  }

  /**
   * Détermine le type de la consultation
   *
   * @return string Un des types possibles urg, anesth
   */
  function getType() {
    $praticien = $this->loadRefPraticien();
    $sejour = $this->_ref_sejour;

    if (!$sejour) {
      $sejour = $this->loadRefSejour();
    }

    if (!$sejour->_ref_rpu) {
      $sejour->loadRefRPU();
    }

    // Consultations d'urgences
    if ($praticien->isUrgentiste() && $sejour->_ref_rpu && $sejour->_ref_rpu->_id) {
      $this->_type = "urg";
    }

    // Consultation d'anesthésie
    if ($this->countBackRefs("consult_anesth")) {
      $this->_type = "anesth";
    }
  }

  /**
   * @see parent::preparePossibleActes()
   */
  function preparePossibleActes() {
    $this->loadRefPlageConsult();
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd($cache = true) {
    $this->loadRefPatient($cache);
    $this->_ref_patient->loadRefConstantesMedicales();
    $this->loadRefPlageConsult($cache);
    $this->_view = "Consult. de ".$this->_ref_patient->_view." - ".$this->_ref_plageconsult->_ref_chir->_view;
    $this->_view .= " (".CMbDT::transform(null, $this->_ref_plageconsult->date, "%d/%m/%Y").")";
    $this->loadExtCodesCCAM();
  }

  /**
   * @see parent::loadRefsDocs()
   */
  function loadRefsDocs() {
    parent::loadRefsDocs();

    if (!$this->_docitems_from_dossier_anesth) {
      // On ajoute les documents des dossiers d'anesthésie
      if (!$this->_refs_dossiers_anesth) {
        $this->loadRefConsultAnesth();
      }

      foreach ($this->_refs_dossiers_anesth as $_dossier_anesth) {
        $_dossier_anesth->_docitems_from_consult = true;
        $_dossier_anesth->loadRefsDocs();
        $this->_ref_documents = CMbArray::mergeKeys($this->_ref_documents, $_dossier_anesth->_ref_documents);
      }
    }

    return count($this->_ref_documents);
  }

  /**
   * @see parent::loadRefsFiles()
   */
  function loadRefsFiles() {
    parent::loadRefsFiles();

    if (!$this->_docitems_from_dossier_anesth) {
      // On ajoute les fichiers des dossiers d'anesthésie
      if (!$this->_refs_dossiers_anesth) {
        $this->loadRefConsultAnesth();
      }

      foreach ($this->_refs_dossiers_anesth as $_dossier_anesth) {
        $_dossier_anesth->_docitems_from_consult = true;
        $_dossier_anesth->loadRefsFiles();
        $this->_ref_files = CMbArray::mergeKeys($this->_ref_files, $_dossier_anesth->_ref_files);
      }
    }
    return count($this->_ref_files);
  }

  /**
   * @see parent::countDocItems()
   */
  function countDocItems($permType = null){
    if (!$this->_nb_files_docs) {
      parent::countDocItems($permType);
    }

    if ($this->_nb_files_docs) {
      $this->getEtat();
      $this->_etat .= " ($this->_nb_files_docs)";
    }
  }

  /**
   * @see parent::countDocs()
   */
  function countDocs() {
    $nbDocs = parent::countDocs();

    if (!$this->_docitems_from_dossier_anesth) {
      // Ajout des documents des dossiers d'anesthésie
      if (!$this->_refs_dossiers_anesth) {
        $this->loadRefConsultAnesth();
      }

      foreach ($this->_refs_dossiers_anesth as $_dossier_anesth) {
        $_dossier_anesth->_docitems_from_consult = true;
        $nbDocs += $_dossier_anesth->countDocs();
      }
    }

    return $this->_nb_docs = $nbDocs;
  }

  /**
   * @see parent::countFiles()
   */
  function countFiles(){
    $nbFiles = parent::countFiles();

    if (!$this->_docitems_from_dossier_anesth) {
      // Ajout des fichiers des dossiers d'anesthésie
      if (!$this->_refs_dossiers_anesth) {
        $this->loadRefConsultAnesth();
      }

      foreach ($this->_refs_dossiers_anesth as $_dossier_anesth) {
        $_dossier_anesth->_docitems_from_consult = true;
        $nbFiles += $_dossier_anesth->countFiles();
      }
    }

    return $this->_nb_files = $nbFiles;
  }

  /**
   * Charge un dossier d'anesthésie classique
   *
   * @param ref $dossier_anesth_id Identifiant de dossier à charge explicitement
   *
   * @return CConsultAnesth
   */
  function loadRefConsultAnesth($dossier_anesth_id = null) {
    $this->loadRefsDossiersAnesth();
    if ($dossier_anesth_id !== null) {
      return $this->_ref_consult_anesth = $this->_refs_dossiers_anesth[$dossier_anesth_id];
    }
    return $this->_ref_consult_anesth = $this->loadFirstBackRef("consult_anesth");
  }

  /**
   * Charge tous les dossiers d'anesthésie
   *
   * @return CConsultAnesth[]
   */
  function loadRefsDossiersAnesth() {
    return $this->_refs_dossiers_anesth = $this->loadBackRefs("consult_anesth");
  }

  /**
   * Charge l'audiogramme
   *
   * @return CExamAudio
   */
  function loadRefsExamAudio(){
    return $this->_ref_examaudio = $this->loadUniqueBackRef("examaudio");
  }

  /**
   * Charge l'audiogramme
   *
   * @return CExamAudio
   */
  function loadRefsExamNyha(){
    $this->_ref_examnyha = $this->loadUniqueBackRef("examnyha");
  }

  /**
   * Charge le score possum
   *
   * @return CExamPossum
   */
  function loadRefsExamPossum(){
    $this->_ref_exampossum = $this->loadUniqueBackRef("exampossum");
  }

  /**
   * Charge toutes les fiches d'examens associées
   *
   * @return int Nombre de fiche
   */
  function loadRefsFichesExamen() {
    $this->loadRefsExamAudio();
    $this->loadRefsExamNyha();
    $this->loadRefsExamPossum();
    $this->_count_fiches_examen = 0;
    $this->_count_fiches_examen += $this->_ref_examaudio->_id  ? 1 : 0;
    $this->_count_fiches_examen += $this->_ref_examnyha->_id   ? 1 : 0;
    $this->_count_fiches_examen += $this->_ref_exampossum->_id ? 1 : 0;
    return $this->_count_fiches_examen;
  }

  /**
   * Chargement des prescriptions liées à la consultation
   *
   * @return CPrescription[] Les prescription, classées par type, pas par identifiant
   */
  function loadRefsPrescriptions() {
    $prescriptions = $this->loadBackRefs("prescriptions");

    // Cas du module non installé
    if (!is_array($prescriptions)) {
      return $this->_ref_prescriptions = null;
    }

    $this->_count_prescriptions = count($prescriptions);

    foreach ($prescriptions as $_prescription) {
      $this->_ref_prescriptions[$_prescription->type] = $_prescription;
    }

    return $this->_ref_prescriptions;
  }

  /**
   * Charge l'ensemble des reglements sur la consultation, les classe par émetteur et calcul les dus résiduels
   *
   * @return CReglement[]
   */
  function loadRefsReglements() {
    // Classement reglements patient et tiers 
    $this->_ref_reglements_patient = array();
    $this->_ref_reglements_tiers   = array();
    
    $this->loadRefFacture();
    if ($this->_ref_facture) {
      $this->_ref_reglements = $this->_ref_facture->loadRefsReglements();
      foreach ($this->_ref_reglements as $_reglement) {
        $_reglement->loadRefBanque();
        if ($_reglement->emetteur == "patient") {
          $this->_ref_reglements_patient[$_reglement->_id] = $_reglement;
        }
        if ($_reglement->emetteur == "tiers") {
          $this->_ref_reglements_tiers[$_reglement->_id] = $_reglement;
        }
      }
    }
    
    // Calcul de la somme du restante du patient
    $this->_du_restant_patient = $this->du_patient;
    $this->_reglements_total_patient = 0;
    foreach ($this->_ref_reglements_patient as $_reglement) {
      $this->_du_restant_patient       -= $_reglement->montant;
      $this->_reglements_total_patient += $_reglement->montant;
    }
    $this->_du_restant_patient       = round($this->_du_restant_patient      , 2);
    $this->_reglements_total_patient = round($this->_reglements_total_patient, 2);

    // Calcul de la somme du restante du tiers
    $this->_du_restant_tiers = $this->du_tiers;
    $this->_reglements_total_tiers = 0;
    foreach ($this->_ref_reglements_tiers as $_reglement) {
      $this->_du_restant_tiers       -= $_reglement->montant;
      $this->_reglements_total_tiers += $_reglement->montant;
    }
    $this->_du_restant_tiers       = round($this->_du_restant_tiers      , 2);
    $this->_reglements_total_tiers = round($this->_reglements_total_tiers, 2);
    
    return $this->_ref_reglements;
  }

  /**
   * @see parent::loadRefsBack()
   * @deprecated
   */
  function loadRefsBack() {
    // Backward references
    $this->loadRefsDocItems();
    $this->countDocItems();
    $this->loadRefConsultAnesth();

    $this->loadRefsExamsComp();

    $this->loadRefsFichesExamen();
    $this->loadRefsActesCCAM();
    $this->loadRefsActesNGAP();
    $this->loadRefsReglements();
  }

  /**
   * Charge les examens complémentaires à réaliser
   *
   * @return CExamComp[]
   */
  function loadRefsExamsComp(){
    $order = "examen";
    /** @var CExamComp $examcomps */
    $examcomps = $this->loadBackRefs("examcomp", $order);

    foreach ($examcomps as $_exam) {
      $this->_types_examen[$_exam->realisation][$_exam->_id] = $_exam;
    }

    return $this->_ref_examcomp = $examcomps;
  }

  /**
   * Champs d'examen à afficher
   *
   * @return string[] Noms interne des champs
   */
  function getExamFields() {
    $fields = array(
      "motif",
      "rques",
    );

    if (CAppUI::conf("dPcabinet CConsultation show_histoire_maladie")) {
      $fields[] = "histoire_maladie";
    }
    if (CAppUI::conf("dPcabinet CConsultation show_examen")) {
      $fields[] = "examen";
    }
    if (CAppUI::pref("view_traitement")) {
      $fields[] = "traitement";
    }
    if (CAppUI::conf("dPcabinet CConsultation show_conclusion")) {
      $fields[] = "conclusion";
    }
    
    return $fields;
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    $this->loadRefPlageConsult();
    $this->_ref_plageconsult->loadRefChir();
    return $this->_ref_plageconsult->_ref_chir->getPerm($permType) && parent::getPerm($permType);
    // Délégation sur la plage
    //return $this->loadRefPlageConsult()->getPerm($permType);
  }

  /**
   * @see parent::fillTemplate()
   */
  function fillTemplate(&$template) {
    $this->updateFormFields();
    $this->loadRefsFwd();
    $this->_ref_plageconsult->loadRefsFwd();
    $this->_ref_plageconsult->_ref_chir->fillTemplate($template);
    $this->_ref_patient->fillTemplate($template);
    $this->fillLimitedTemplate($template);
    if (CModule::getActive('dPprescription')) {
      // Chargement du fillTemplate de la prescription
      $this->loadRefsPrescriptions();
      $prescription = isset($this->_ref_prescriptions["externe"]) ?
        $this->_ref_prescriptions["externe"] :
        new CPrescription();
      $prescription->type = "externe";
      $prescription->fillLimitedTemplate($template);
    }

    $sejour = $this->loadRefSejour();

    $sejour->fillLimitedTemplate($template);
    $rpu = $sejour->loadRefRPU();
    if ($rpu && $rpu->_id) {
      $rpu->fillLimitedTemplate($template);
    }

    if (CModule::getActive("dPprescription")) {
      $sejour->loadRefsPrescriptions();
      $prescription = isset($sejour->_ref_prescriptions["pre_admission"]) ?
        $sejour->_ref_prescriptions["pre_admission"] :
        new CPrescription();
      $prescription->type = "pre_admission";
      $prescription->fillLimitedTemplate($template);
      $prescription = isset($sejour->_ref_prescriptions["sejour"]) ?
        $sejour->_ref_prescriptions["sejour"] :
        new CPrescription();
      $prescription->type = "sejour";
      $prescription->fillLimitedTemplate($template);
      $prescription = isset($sejour->_ref_prescriptions["sortie"]) ?
        $sejour->_ref_prescriptions["sortie"] :
        new CPrescription();
      $prescription->type = "sortie";
      $prescription->fillLimitedTemplate($template);
    }
  }

  /**
   * @see parent::fillLimitedTemplate()
   */
  function fillLimitedTemplate(&$template) {
    $chir = $this->_ref_plageconsult->_ref_chir;
    
    // Ajout du praticien pour les destinataires possibles (dans l'envoi d'un email)
    $template->destinataires[] = array(
      "nom"   => "Dr " . $chir->_user_last_name . " " . $chir->_user_first_name,
      "email" => $chir->_user_email,
      "tag"   => "Praticien"
    );
    
    $this->updateFormFields();
    $this->loadRefsFwd();

    $this->notify("BeforeFillLimitedTemplate", $template);

    $template->addDateProperty("Consultation - date"  , $this->_ref_plageconsult->date);
    $template->addLongDateProperty("Consultation - date longue", $this->_ref_plageconsult->date);
    $template->addTimeProperty("Consultation - heure" , $this->heure);
    $locExamFields = array(
      "motif"            => "motif",
      "rques"            => "remarques",
      "examen"           => "examen",
      "traitement"       => "traitement",
      "histoire_maladie" => "histoire maladie",
      "conclusion"       => "conclusion"
    );

    foreach ($this->_exam_fields as $field) {
      $loc_field = $locExamFields[$field];
      $template->addProperty("Consultation - $loc_field", $this->$field);
    }

    if (!in_array("traitement", $this->_exam_fields)) {
      $template->addProperty("Consultation - traitement", $this->traitement);
    }

    $medecin = new CMedecin();
    $medecin->load($this->adresse_par_prat_id);
    $nom = "{$medecin->nom} {$medecin->prenom}";
    $template->addProperty("Consultation - adressé par", $nom);
    $template->addProperty("Consultation - adressé par - adresse", "{$medecin->adresse}\n{$medecin->cp} {$medecin->ville}");

    $template->addProperty("Consultation - Accident du travail"          , $this->getFormattedValue("date_at"));
    $libelle_at = $this->date_at ? "Accident du travail du " . $this->getFormattedValue("date_at") : "";
    $template->addProperty("Consultation - Libellé accident du travail"  , $libelle_at);

    $this->loadRefsFiles();
    $list = CMbArray::pluck($this->_ref_files, "file_name");
    $template->addListProperty("Consultation - Liste des fichiers", $list);

    $template->addProperty("Consultation - Fin arrêt de travail", CMbDT::dateToLocale(CMbDT::date($this->fin_at)));
    $template->addProperty("Consultation - Prise en charge arrêt de travail", $this->getFormattedValue("pec_at"));
    $template->addProperty("Consultation - Reprise de travail", CMbDT::dateToLocale(CMbDT::date($this->reprise_at)));
    $template->addProperty("Consultation - Accident de travail sans arrêt de travail", $this->getFormattedValue("at_sans_arret"));
    $template->addProperty("Consultation - Arrêt maladie", $this->getFormattedValue("arret_maladie"));
    
    $this->loadRefsExamsComp();
    $exam = new CExamComp();
    
    foreach ($exam->_specs["realisation"]->_locales as $key => $locale) {
      $exams = isset($this->_types_examen[$key]) ? $this->_types_examen[$key] : array();
      $template->addListProperty("Consultation - Examens complémentaires - $locale", $exams);
    }
    
    $this->notify("AfterFillLimitedTemplate", $template);
  }

  /**
   * @see parent::canDeleteEx()
   */
  function canDeleteEx() {
    if (!$this->_mergeDeletion) {
      // Date dépassée
      $this->loadRefPlageConsult();
      if ($this->_date < CMbDT::date() && !$this->_ref_module->_can->admin) {
        return "Impossible de supprimer une consultation passée";
      }
    }

    return parent::canDeleteEx();
  }

  /**
   * Ajustement du séjour à l'enregistrement
   *
   * @param CSejour  $sejour        Séjour englobant
   * @param datetime $dateTimePlage Date et heure de la plage à créer
   *
   * @return string|null Store-like message
   */
  private function adjustSejour(CSejour $sejour, $dateTimePlage) {
    if ($sejour->_id == $this->_ref_sejour->_id) {
      return null;
    }

    // Journée dans lequel on déplace à déjà un séjour
    if ($sejour->_id) {
      // Affecte à la consultation le nouveau séjour
      $this->sejour_id = $sejour->_id;
      return null;
    }

    // Journée qui n'a pas de séjour en cible
    $count_consultations = $this->_ref_sejour->countBackRefs("consultations");

    // On déplace les dates du séjour
    if (($count_consultations == 1) && ($this->_ref_sejour->type === "consult")) {
      $this->_ref_sejour->entree_prevue = $dateTimePlage;
      $this->_ref_sejour->sortie_prevue = CMbDT::date($dateTimePlage)." 23:59:59";
      $this->_ref_sejour->_hour_entree_prevue = null;
      $this->_ref_sejour->_hour_sortie_prevue = null;
      if ($msg = $this->_ref_sejour->store()) {
        return $msg;
      }

      return null;
    }

    // On créé le séjour de consultation
    $sejour->patient_id = $this->patient_id;
    $sejour->praticien_id = $this->_ref_chir->_id;
    $sejour->group_id = CGroups::loadCurrent()->_id;
    $sejour->type = "consult";
    $sejour->entree_prevue = $dateTimePlage;
    $sejour->sortie_prevue = CMbDT::date($dateTimePlage)." 23:59:59";

    if ($msg = $sejour->store()) {
      return $msg;
    }

    $this->sejour_id = $sejour->_id;
    return null;
  }

  /**
   * @see parent::docsEditable()
   */
  function docsEditable() {
    if (parent::docsEditable()) {
      return true;
    }

    $fix_edit_doc = CAppUI::conf("dPcabinet CConsultation fix_doc_edit");
    if (!$fix_edit_doc) {
       return true;
    }
    if ($this->annule) {
      return false;
    }
    $this->loadRefPlageConsult();

    return (CMbDT::dateTime("+ 24 HOUR", "{$this->_date} {$this->heure}") > CMbDT::dateTime());
  }

  /**
   * @see parent::completeLabelFields()
   */
  function completeLabelFields(&$fields) {
    $this->loadRefPatient()->completeLabelFields($fields);
  }

  /**
   * @see parent::canEdit()
   */
  function canEdit() {
    if (!$this->sejour_id || CCanDo::admin() || !CAppUI::conf("cabinet CConsultation consult_readonly")) {
      return parent::canEdit();
    }

    // Si sortie réelle, mode lecture seule
    $sejour = $this->loadRefSejour(1);
    if ($sejour->sortie_reelle) {
      return $this->_canEdit = 0;
    }

    // Modification possible seulement pour les utilisateurs de la même fonction
    $praticien = $this->loadRefPraticien();
    return $this->_canEdit = CAppUI::$user->function_id == $praticien->function_id;
  }

  /**
   * @see parent::canRead()
   */
  function canRead() {
    if (!$this->sejour_id || CCanDo::admin()) {
      return parent::canRead();
    }
    // Tout utilisateur peut consulter une consultation de séjour en lecture seule
    return $this->_canRead = 1;
  }

  /**
   * Crée une consultation à une horaire arbitraire et créé les plages correspondantes au besoin
   *
   * @param datetime $datetime     Date et heure
   * @param ref      $praticien_id Praticien
   * @param ref      $patient_id   Patient
   *
   * @return null|string Store-like message
   */
  function createByDatetime($datetime, $praticien_id, $patient_id) {
    $day_now   = CMbDT::transform(null, $datetime, "%Y-%m-%d");
    $time_now  = CMbDT::transform(null, $datetime, "%H:%M:00");
    $hour_now  = CMbDT::transform(null, $datetime, "%H:00:00");
    $hour_next = CMbDT::time("+1 HOUR", $hour_now);

    $plage       = new CPlageconsult();
    $plageBefore = new CPlageconsult();
    $plageAfter  = new CPlageconsult();

    // Cas ou une plage correspond
    $where = array();
    $where["chir_id"] = "= '$praticien_id'";
    $where["date"]    = "= '$day_now'";
    $where["debut"]   = "<= '$time_now'";
    $where["fin"]     = "> '$time_now'";
    $plage->loadObject($where);

    if (!$plage->plageconsult_id) {
      // Cas ou on a des plage en collision
      $where = array();
      $where["chir_id"] = "= '$praticien_id'";
      $where["date"]    = "= '$day_now'";
      $where["debut"]   = "<= '$hour_now'";
      $where["fin"]     = ">= '$hour_now'";
      $plageBefore->loadObject($where);
      $where["debut"]   = "<= '$hour_next'";
      $where["fin"]     = ">= '$hour_next'";
      $plageAfter->loadObject($where);
      if ($plageBefore->_id) {
        $plageBefore->fin = $plageAfter->_id ?
          $plageAfter->debut :
          max($plageBefore->fin, $hour_next);
        $plage = $plageBefore;
      }
      elseif ($plageAfter->_id) {
        $plageAfter->debut = min($plageAfter->debut, $hour_now);
        $plage = $plageAfter;
      }
      else {
        $plage->chir_id = $praticien_id;
        $plage->date    = $day_now;
        $plage->freq    = "00:".CPlageconsult::$minutes_interval.":00";
        $plage->debut   = $hour_now;
        $plage->fin     = $hour_next;
      }

      $plage->updateFormFields();

      if ($msg = $plage->store()) {
        return $msg;
      }
    }

    $this->plageconsult_id = $plage->_id;
    $this->patient_id      = $patient_id;

    // Chargement de la consult avec la plageconsult && le patient
    $this->loadMatchingObject();

    if (!$this->_id) {
      $this->heure   = $time_now;
      $this->arrivee = "$day_now $time_now";
      $this->duree   = 1;
      $this->chrono  = CConsultation::TERMINE;
    }

    return $this->store();
  }

  /**
   * Crée la dossier d'anesthésie associée à la consultation
   *
   * @return null|string Store-like message
   */
  function createConsultAnesth() {
    $this->loadRefPlageConsult();

    if (!$this->_is_anesth || !$this->patient_id || !$this->_id || $this->type == "entree") {
      return null;
    }

    // Création de la consultation d'anesthésie
    $consultAnesth = $this->loadRefConsultAnesth();
    if (!$consultAnesth->_id) {
      $consultAnesth->consultation_id = $this->_id;
      if ($msg = $consultAnesth->store()) {
        return $msg;
      }
    }

    // Remplissage automatique des motifs et remarques
    if ($this->_operation_id) {
      // Association à l'intervention
      $consultAnesth->operation_id = $this->_operation_id;
      $operation = $consultAnesth->loadRefOperation();
      if ($msg = $consultAnesth->store()) {
        return $msg;
      }

      // Remplissage du motif de pré-anesthésie si creation et champ motif vide
      if ($operation->_id) {
        $format_motif = CAppUI::conf('cabinet CConsultAnesth format_auto_motif');
        $format_rques = CAppUI::conf('cabinet CConsultAnesth format_auto_rques');

        if (($format_motif && !$this->motif) || ($format_rques && !$this->rques)) {
          $operation = $consultAnesth->_ref_operation;
          $operation->loadRefPlageOp();
          $sejour = $operation->loadRefSejour();
          $chir   = $operation->loadRefChir();
          $chir->updateFormFields();

          $items = array (
            '%N' => $chir->_user_last_name,
            '%P' => $chir->_user_first_name,
            '%S' => $chir->_shortview,
            '%L' => $operation->libelle,
            '%i' => CMbDT::transform(null, $operation->_datetime_best , CAppUI::conf('time')),
            '%I' => CMbDT::transform(null, $operation->_datetime_best , CAppUI::conf('date')),
            '%E' => CMbDT::transform(null, $sejour->entree_prevue, CAppUI::conf('date')),
            '%e' => CMbDT::transform(null, $sejour->entree_prevue, CAppUI::conf('time')),
            '%T' => strtoupper(substr($sejour->type, 0, 1)),
          );

          if ($format_motif && !$this->motif) {
            $this->motif = str_replace(array_keys($items), $items, $format_motif);
          }

          if ($format_rques && !$this->rques) {
            $this->rques = str_replace(array_keys($items), $items, $format_rques);
          }

          if ($msg = $this->store()) {
            return $msg;
          }
        }
      }
    }

    return null;
  }

  /**
   * Construit le tag d'une consultation en fonction des variables de configuration
   * 
   * @param string $group_id Permet de charger l'id externe d'uns consultation pour un établissement donné si non null
   * 
   * @return string|null Nul si indisponible
   */
  static function getTagConsultation($group_id = null) {
    // Pas de tag consultation
    if (null == $tag_consultation = CAppUI::conf("dPcabinet CConsultation tag")) {
      return null;
    }

    // Permettre des id externes en fonction de l'établissement
    $group = CGroups::loadCurrent();
    if (!$group_id) {
      $group_id = $group->_id;
    }
    
    return str_replace('$g', $group_id, $tag_consultation);
  }

  /**
   * Crée une facture cabinet pour la consultation
   *
   * @param string $type_facture Type de facture voulu
   *
   * @return CFactureCabinet
   */
  function createFactureConsult($type_facture = "maladie") {
    $facture               = new CFactureCabinet();
    $facture->patient_id   = $this->patient_id;
    $facture->praticien_id = $this->_praticien_id;
    $facture->du_patient   = $this->du_patient;
    $facture->du_tiers     = $this->du_tiers;
    $facture->type_facture = $type_facture;
    $facture->ouverture    = CMbDT::date();
    $facture->cloture      = CMbDT::date();
    
    $facture->patient_date_reglement = $this->patient_date_reglement;
    if (!$this->du_patient) {
      $facture->patient_date_reglement = CMbDT::date();
    }
    
    $facture->patient_date_reglement = $this->tiers_date_reglement;
    if (!$this->du_tiers) {
      $facture->tiers_date_reglement = CMbDT::date();
    }
    
    $facture->store();
    
    // Ajout de l'id de la facture dans la consultation
    $this->facture_id = $facture->_id;
    $this->store();
    
    return $facture;
  }
}

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
class CConsultation extends CFacturable implements IPatientRelated, IIndexableObject {
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
  public $element_prescription_id;

  // DB fields
  public $type;
  public $heure;
  public $duree;
  public $secteur1;
  public $secteur2;
  public $secteur3; // Assujetti à la TVA
  public $du_tva;
  public $taux_tva;
  public $chrono;
  public $annule;
  public $motif_annulation;

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
  public $org_at;
  public $feuille_at;
  public $arret_maladie;
  public $concerne_ALD;

  // Derived fields
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

  // seances
  public $_consult_sejour_nb;
  public $_consult_sejour_out_of_nb;

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
  /** @var CFactureCabinet[] */
  public $_ref_factures = array();
  /** @var CPrescription */
  public $_ref_prescription;
  /** @var CConsultationCategorie */
  public $_ref_categorie;
  /** @var CSejourTask */
  public $_ref_task;
  /** @var CElementPrescription */
  public $_ref_element_prescription;
  /** @var CBrancardage */
  public $_ref_brancardage;

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
  /** @var  CFSE */
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
  public $_all_group_money;
  public $_all_group_compta;
  public $_telephone;
  public $_coordonnees;
  public $_plages_vides;
  public $_empty_places;
  public $_non_pourvues;
  public $_print_ipp;

  // Behaviour fields
  public $_adjust_sejour;
  public $_operation_id;
  public $_dossier_anesth_completed_id;
  public $_docitems_from_dossier_anesth;
  public $_locks;
  public $_handler_external_booking;
  public $_list_forms = array();
  public $_skip_count = false;
  public $_sync_consults_from_sejour = false;     // used to allow CSejour::store to avoid consultation's sejour patient check

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
    $backProps["echanges_hprimxml"] = "CEchangeHprim object_id cascade";
    $backProps["exchanges_hl7v2"]   = "CExchangeHL7v2 object_id cascade";
    $backProps["echanges_hl7v3"]    = "CExchangeHL7v3 object_id cascade";
    $backProps["echanges_dmp"]      = "CExchangeDMP object_id cascade";
    $backProps["echanges_mvsante"]  = "CExchangeMVSante object_id cascade";
    $backProps["fse_pyxvital"]      = "CPvFSE consult_id";
    $backProps["task"]              = "CSejourTask consult_id";
    $backProps["identifiants"]      = "CIdSante400 object_id cascade";
    $backProps["contextes_constante"] = "CConstantesMedicales context_id";
    $backProps["arret_travail"]     = "CAvisArretTravail consult_id";
    $backProps["sejours_lies"]      = "CSejour consult_related_id";
    $backProps["intervs_liees"]     = "COperation consult_related_id";
    $backProps["consults_liees"]    = "CConsultation consult_related_id";

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
    $props["element_prescription_id"] = "ref class|CElementPrescription";
    $props["consult_related_id"] = "ref class|CConsultation show|0";
    $props["motif"]             = "text helped seekable";
    $props["type"]              = "enum list|classique|entree|chimio default|classique";
    $props["heure"]             = "time notNull show|0";
    $props["duree"]             = "num min|1 max|255 notNull default|1 show|0";
    $props["secteur1"]          = "currency min|0 show|0";
    $props["secteur2"]          = "currency show|0";
    $props["secteur3"]          = "currency show|0";
    $props["taux_tva"]          = "float";
    $props["du_tva"]            = "currency show|0";
    $props["chrono"]            = "enum notNull list|16|32|48|64 show|0";
    $props["annule"]            = "bool show|0 default|0 notNull";
    $props["motif_annulation"]  = "enum list|not_arrived|by_patient default|not_arrived";
    $props["_etat"]             = "str";

    $props["rques"]             = "text helped seekable";
    $props["examen"]            = "text helped seekable";
    $props["traitement"]        = "text helped seekable";
    $props["histoire_maladie"]  = "text helped seekable";
    $props["brancardage"]       = "text helped seekable";
    $props["conclusion"]        = "text helped seekable";

    $props["facture"]           = "bool default|0 show|0";

    $props["premiere"]            = "bool show|0";
    $props["derniere"]            = "bool show|0";
    $props["adresse"]             = "bool show|0";
    $props["adresse_par_prat_id"] = "ref class|CMedecin";
    $props["arrivee"]             = "dateTime show|0";
    $props["concerne_ALD"]        = "bool";

    $props["patient_date_reglement"]    = "date show|0";
    $props["tiers_date_reglement"]      = "date show|0";
    $props["du_patient"]                = "currency show|0";
    $props["du_tiers"  ]                = "currency show|0";

    $props["type_assurance"] = "enum list|classique|at|maternite|smg";
    $props["date_at"]     = "date";
    $props["fin_at"]      = "dateTime";
    $props["num_at"]      = "num length|8";
    $props["cle_at"]      = "num length|1";
    $props['feuille_at']  = 'bool default|0';
    $props['org_at']      = 'numchar length|9';

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
    $props["_all_group_compta"] = "bool default|1";
    $props["_all_group_money"]  = "bool default|1";
    $props["_telephone"]        = "bool default|0";
    $props["_coordonnees"]      = "bool default|0";
    $props["_plages_vides"]     = "bool default|1";
    $props["_non_pourvues"]     = "bool default|1";
    $props["_print_ipp"]        = "bool default|".CAppUI::conf("dPcabinet CConsultation show_IPP_print_consult");

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
    $etat[self::PATIENT_ARRIVE] = CMbDT::format($this->arrivee, "%Hh%M");
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
   * Calcul de la TVA assujetti au secteur 3
   *
   * @return int
   */
  function calculTVA() {
    return $this->du_tva = round($this->secteur3 * $this->taux_tva/100 , 2);
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->calculTVA();
    $this->_somme = $this->secteur1 + $this->secteur2 + $this->secteur3 + $this->du_tva;
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
      $this->du_tiers = $this->secteur1 + $this->secteur2 + $this->secteur3 + $this->du_tva;
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

    if ($this->_check_bounds) {
      if ($this->sejour_id && !$this->_forwardRefMerging) {
        $this->loadRefPlageConsult();
        $sejour = $this->loadRefSejour();

        if ($sejour->type != "consult" &&
          ($this->_date < CMbDT::date($sejour->entree) || CMbDT::date($this->_date) > $sejour->sortie)
        ) {
          $msg .= "Consultation en dehors du séjour<br />";
          return $msg . parent::check();
        }
      }
    }

    /** @var self $old */
    $old = $this->_old;
    // Dévalidation avec règlement déjà effectué
    if ($this->fieldModified("valide", "0")) {
      // Bien tester sur _old car valide = 0 s'accompagne systématiquement d'un facture_id = 0
      if (count($old->loadRefFacture()->loadRefsReglements())) {
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
    $this->loadRefBrancardage();
    $this->loadRefSejour();
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

    if ($this->tarif == "pursue") {
      // Cas de la cotation poursuivie
      $this->secteur1 += $tarif->secteur1;
      $this->secteur2 += $tarif->secteur2;
      $this->secteur3 += $tarif->secteur3;
      $this->tarif     = "composite";
    }
    else {
      // Cas de la cotation normale
      $this->secteur1 = $tarif->secteur1;
      $this->secteur2 = $tarif->secteur2;
      $this->secteur3 = $tarif->secteur3;
      $this->tarif    = $tarif->description;
    }

    $this->calculTVA();
    $this->du_patient   = $this->secteur1 + $this->secteur2 + $this->secteur3 + $this->du_tva;

    // Mise à jour de codes CCAM prévus, sans information serialisée complémentaire
    foreach ($tarif->_codes_ccam as $_code_ccam) {
      $this->_codes_ccam[] = substr($_code_ccam, 0, 7);
    }
    $this->codes_ccam = implode("|", $this->_codes_ccam);
    $this->exec_tarif = $this->_acte_execution;
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

    $this->loadRefsActes();

    if ($this->concerne_ALD) {
      foreach ($this->_ref_actes_ngap as $_acte_ngap) {
        $_acte_ngap->ald = 1;
        $_acte_ngap->store();
      }

      foreach ($this->_ref_actes_ccam as $_acte_ccam) {
        $_acte_ccam->ald = 1;
        $_acte_ccam->store();
      }
    }

    return null;
  }


  function loadPosition() {
    if (!$this->sejour_id) {
      return;
    }

    $ds = $this->getDS();
    $sql = "SELECT type FROM sejour WHERE sejour_id = '$this->sejour_id'";
    $type = $ds->loadResult($sql);

    // only for seances
    if ($type != "seances") {
      return;
    }

    $sql = "SELECT consultation.plageconsult_id, date, heure, consultation_id
    FROM plageconsult, consultation
    WHERE consultation.plageconsult_id = plageconsult.plageconsult_id
      AND sejour_id = '$this->sejour_id'
      AND annule = '0'
    ORDER BY date, heure";
    $list = $ds->loadList($sql);
    $seance_nb = 1;
    foreach ($list as $_seance) {
      if ($_seance["heure"] == $this->heure && $_seance["plageconsult_id"] == $this->plageconsult_id) {
        $this->_consult_sejour_nb = $seance_nb;
        break;
      }
      $seance_nb++;
    }
    $this->_consult_sejour_out_of_nb = count($list);
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
    $this->loadRefsActesNGAP(1);
    foreach ($this->_ref_actes_ngap as $acteNGAP) {
      $count_actes++;
      $secteur1_NGAP += $acteNGAP->montant_base;
      $secteur2_NGAP += $acteNGAP->montant_depassement;
    }

    // Chargement des actes CCAM
    $this->loadRefsActesCCAM(1);
    foreach ($this->_ref_actes_ccam as $acteCCAM) {
      $count_actes++;
      $secteur1_CCAM += round($acteCCAM->getTarif(), 2);
      $secteur2_CCAM += $acteCCAM->montant_depassement;
    }

    // Remplissage des montant de la consultation
    $this->secteur1 = $secteur1_NGAP + $secteur1_CCAM + $secteur1_TARMED + $secteur1_CAISSE;
    $this->secteur2 = $secteur2_NGAP + $secteur2_CCAM + $secteur2_TARMED + $secteur2_CAISSE;

    if ($secteur1_NGAP == 0 && $secteur1_CCAM == 0 && $secteur2_NGAP==0 && $secteur2_CCAM ==0) {
      $this->du_patient = $this->secteur1 + $this->secteur2 +  $this->secteur3 + $this->du_tva;
    }

    // Cotation manuelle
    $this->completeField("tarif");
    if (!$this->tarif && $count_actes) {
      $this->tarif = "Cotation manuelle";
    }
    elseif (!$count_actes && $this->tarif == "Cotation manuelle") {
      $this->tarif = "";
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
    $this->completeField('sejour_id', 'heure', 'plageconsult_id', 'si_desistement', 'annule');

    if ($this->si_desistement === null) {
      $this->si_desistement = 0;
    }

    $this->annule = $this->annule === null || $this->annule === '' ? 0 : $this->annule;


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

      $function = new CFunctions();

      if ($this->_function_secondary_id) {
        $function->load($this->_function_secondary_id);
      }
      else {
        $user = new CMediusers();
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
      else {
        $sejour->_id = "";
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
    if (!$this->_forwardRefMerging && $this->sejour_id && $patient_modified && !$this->_skip_count && !$this->_sync_consults_from_sejour) {
      $this->loadRefSejour();
      $consultations = $this->_ref_sejour->countBackRefs("consultations");
      if ($consultations > 1) {
        return "Vous ne pouvez changer le patient d'une consultation si celle ci est contenue dans un séjour. Dissociez la consultation ou modifier le patient du séjour.";
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
      //Si le DH est modifié, ceui ci se répercute sur le premier acte coté
      if ($this->fieldModified("secteur2") && (count($this->_tokens_ngap) || count($this->_tokens_ccam)) && count($this->loadRefsActes())) {
        $acte = reset($this->_ref_actes);
        $acte->montant_depassement += ($this->secteur2-$this->_old->secteur2);
        if ($msg = $acte->store()) {
          return $msg;
        }
      }

      $facture = $this->sejour_id ? new CFactureEtablissement() : new CFactureCabinet();
      $facture->group_id    = CGroups::loadCurrent()->_id;
      $facture->_consult_id = $this->_id;
      $facture->du_patient  = $this->du_patient;
      $facture->du_tiers    = $this->du_tiers;
      $facture->du_tva      = $this->du_tva;
      $facture->taux_tva    = $this->taux_tva;

      if (!count($this->loadRefsFraisDivers(1)) && count($this->loadRefsFraisDivers(2)) && $this->du_tva) {
        $facture->du_patient  = $this->du_patient - $this->du_tva - $this->secteur3;
        $facture->du_tva      = 0;
        $facture->taux_tva    = $this->taux_tva;
      }

      if ($msg = $facture->store()) {
        echo $msg;
      }
    }

    //Lors de dévalidation de la consultation 
    if ($this->fieldModified("valide", "0")) {
      $this->loadRefFacture();
      foreach ($this->_ref_factures as $_facture) {
        $_facture->_consult_id = $this->_id;
        $_facture->cancelConsult();
      }
    }

    if ($this->fieldModified("annule", "1")) {
      $this->loadRefConsultAnesth();
      foreach ($this->_refs_dossiers_anesth as $_dossier_anesth) {
        if ($_dossier_anesth->operation_id) {
          $_dossier_anesth->operation_id = '';
          if ($msg = $_dossier_anesth->store()) {
            return $msg;
          }
        }
      }
    }

    if ($this->fieldModified("annule", "0") || ($this->annule == 0 && $this->motif_annulation)) {
      $this->motif_annulation = "";
    }

    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }

    if (CAppUI::pref("create_dossier_anesth")) {
      $this->createConsultAnesth();
    }

    $this->completeField("_line_element_id");

    // Création d'une tâche si la prise de rdv est issue du plan de soin
    if ($this->_line_element_id) {
      $task = new CSejourTask();
      $task->consult_id = $this->_id;
      $task->sejour_id = $this->sejour_id;
      $task->prescription_line_element_id = $this->_line_element_id;
      $task->description = "Consultation prévue le ".$this->_ref_plageconsult->getFormattedValue("date");

      $line_element = new CPrescriptionLineElement();
      $line_element->load($this->_line_element_id);
      $this->motif = ($this->motif ? "$this->motif\n" : "") . $line_element->_view;
      $this->rques = ($this->rques ? "$this->rques\n" : "") .
                     "Prescription d'hospitalisation, prescrit par le Dr ". $line_element->_ref_praticien->_view;

      // Planification manuelle à l'heure de la consultation
      $administration = new CAdministration();
      $administration->administrateur_id = CAppUI::$user->_id;
      $administration->dateTime = $this->_datetime;
      $administration->quantite = $administration->planification = 1;
      $administration->unite_prise = "aucune_prise";
      $administration->setObject($line_element);

      $this->element_prescription_id = $line_element->element_prescription_id;

      if ($msg = $administration->store()) {
        return $msg;
      }

      if ($msg = $task->store()) {
        return $msg;
      }

      if ($msg = parent::store()) {
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
   * Charge la tâche de séjour possiblement associée
   *
   * @return CSejourTask
   */
  function loadRefTask() {
    return $this->_ref_task = $this->loadUniqueBackRef("task");
  }

  /**
   * Charge l'élément de prescription possiblement associé
   *
   * @return CElementPrescription
   */
  function loadRefElementPrescription() {
    return $this->_ref_element_prescription = $this->loadFwdRef("element_prescription_id", true);
  }

  /**
   * @see parent::loadComplete()
   */
  function loadComplete() {
    parent::loadComplete();
    
    if (!$this->_ref_patient) {
      $this->loadRefPatient();
    }
    $this->_ref_patient->loadRefLatestConstantes();

    if (!$this->_ref_actes_ccam) {
      $this->loadRefsActesCCAM();
    }
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
   * @return CFacture
   */
  function loadRefFacture() {
    if (count($this->_ref_factures)) {
      return $this->_ref_facture;
    }

    if (CModule::getActive("dPfacturation")) {
      $this->completeField('patient_id');
      $facture_class = $this->sejour_id ? "CFactureEtablissement" : "CFactureCabinet";
      $facture_table = $this->sejour_id ? "facture_etablissement" : "facture_cabinet";

      $ljoin = array();
      $ljoin["facture_liaison"] = "facture_liaison.facture_id = $facture_table.facture_id";
      $where = array();
      $where["facture_liaison.facture_class"] = " = '$facture_class'";
      $where["facture_liaison.object_id"]     = " = '$this->_id'";
      $where["facture_liaison.object_class"]  = " = '$this->_class'";
      $where["$facture_table.patient_id"]       = " = '$this->patient_id'";
      /* @var CFacture $facture */
      $facture = new $facture_class;
      $this->_ref_factures = $facture->loadList($where, null, null, "facture_id", $ljoin);
      $this->_ref_facture = reset($this->_ref_factures);
    }
    if (!$this->_ref_facture) {
      $this->_ref_facture = new CFactureCabinet();
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
    $this->completeField("plageconsult_id");
    /** @var CPlageConsult $plage */
    $plage = $this->loadFwdRef("plageconsult_id", $cache);

    $time = CMbDT::time("+".CMbDT::minutesRelative("00:00:00", $plage->freq)*$this->duree." MINUTES", $this->heure);
    $this->_date_fin = "$plage->date $time";

    $this->_duree = CMbDT::minutesRelative("00:00:00", $plage->freq) * $this->duree;

    $plage->_ref_chir        = $plage->loadFwdRef("chir_id"       , $cache);
    $plage->_ref_remplacant  = $plage->loadFwdRef("remplacant_id" , $cache);

    // Distant fields
    $chir = $plage->_ref_remplacant->_id ?
      $plage->_ref_remplacant :
      $plage->_ref_chir;

    $this->_date           = $plage->date;
    $this->_datetime       = CMbDT::addDateTime($this->heure, $this->_date);
    $this->_acte_execution = $this->_datetime;
    $this->_is_anesth      = $chir->isAnesth();
    $this->_is_dentiste    = $chir->isDentiste();
    $this->_praticien_id   = $chir->_id;

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
    $this->_ref_patient->loadRefLatestConstantes();
    $this->loadRefPlageConsult($cache);
    $this->_view = "Consult. de ".$this->_ref_patient->_view." - ".$this->_ref_plageconsult->_ref_chir->_view;
    $this->_view .= " (".CMbDT::format($this->_ref_plageconsult->date, "%d/%m/%Y").")";
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
  function countDocItems($permType = null) {
    if (!$this->_nb_files_docs) {
       parent::countDocItems($permType);
    }

    if ($this->_nb_files_docs) {
      $this->getEtat();
      $this->_etat .= " ($this->_nb_files_docs)";
    }

    return $this->_nb_files_docs;
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
   * @param ref $dossier_anesth_id Identifiant de dossier à charger explicitement
   *
   * @return CConsultAnesth
   */
  function loadRefConsultAnesth($dossier_anesth_id = null) {
    $dossiers = $this->loadRefsDossiersAnesth();

    // Cas du choix initial du dossier à utiliser
    if ($dossier_anesth_id !== null && isset($dossiers[$dossier_anesth_id])) {
      return $this->_ref_consult_anesth = $dossiers[$dossier_anesth_id];
    }

    // On retourne le premier ou un dossier vide
    return $this->_ref_consult_anesth = count($dossiers) ? reset($dossiers) : new CConsultAnesth();
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
    $context = array(__METHOD__, func_get_args());
    if (CFunctionCache::exist($context)) {
      return CFunctionCache::get($context);
    }

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

    return CFunctionCache::set($context, $fields);
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    $this->loadRefPlageConsult();
    return $this->_ref_chir->getPerm($permType) && parent::getPerm($permType);
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

    if (!$this->countBackRefs("consult_anesth") && CModule::getActive("dPprescription")) {
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

    $facture = $this->loadRefFacture();
    $facture->fillLimitedTemplate($template);
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

    if (CModule::getActive("forms")) {
      CExObject::addFormsToTemplate($template, $this, "Consultation");
    }

    // Séjour et/ou intervention créés depuis la consultation
    $sejour_relie  = reset($this->loadBackRefs("sejours_lies"));
    $interv_reliee = reset($this->loadBackRefs("intervs_liees"));

    if ($interv_reliee) {
      $sejour_relie = $interv_reliee->loadRefSejour();
    }
    else {
      if (!$sejour_relie) {
        $sejour_relie = new CSejour();
      }
      if (!$interv_reliee) {
        $interv_reliee = new COperation();
      }
    }

    $interv_reliee->loadRefChir();
    $interv_reliee->loadRefPlageOp();
    $interv_reliee->loadRefSalle();
    $sejour_relie->loadRefPraticien();

    // Intervention reliée
    $template->addProperty("Consultation - Opération reliée - Chirurgien", $interv_reliee->_ref_chir->_view);
    $template->addProperty("Consultation - Opération reliée - Libellé"   , $interv_reliee->libelle);
    $template->addProperty("Consultation - Opération reliée - Salle"     , $interv_reliee->_ref_salle->nom);
    $template->addDateProperty("Consultation - Opération reliée - Date"  , $interv_reliee->_datetime_best);

    // Séjour relié
    $template->addDateProperty("Consultation - Séjour relié - Date entrée"         , $sejour_relie->entree);
    $template->addLongDateProperty("Consultation - Séjour relié - Date entrée (longue)", $sejour_relie->entree);
    $template->addTimeProperty("Consultation - Séjour relié - Heure entrée"        , $sejour_relie->entree);
    $template->addDateProperty("Consultation - Séjour relié - Date sortie"         , $sejour_relie->sortie);
    $template->addLongDateProperty("Consultation - Séjour relié - Date sortie (longue)", $sejour_relie->sortie);
    $template->addTimeProperty("Consultation - Séjour relié - Heure sortie"        , $sejour_relie->sortie);

    $template->addDateProperty("Consultation - Séjour relié - Date entrée réelle"  , $sejour_relie->entree_reelle);
    $template->addTimeProperty("Consultation - Séjour relié - Heure entrée réelle" , $sejour_relie->entree_reelle);
    $template->addDateProperty("Consultation - Séjour relié - Date sortie réelle"  , $sejour_relie->sortie_reelle);
    $template->addTimeProperty("Consultation - Séjour relié - Heure sortie réelle" , $sejour_relie->sortie_reelle);
    $template->addProperty("Consultation - Séjour relié - Praticien"               , "Dr ".$sejour_relie->_ref_praticien->_view);
    $template->addProperty("Consultation - Séjour relié - Libelle"                 , $sejour_relie->getFormattedValue("libelle"));

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
   * @param string $datetime     Date et heure
   * @param ref    $praticien_id Praticien
   * @param ref    $patient_id   Patient
   *
   * @return null|string Store-like message
   */
  function createByDatetime($datetime, $praticien_id, $patient_id) {
    $day_now   = CMbDT::format($datetime, "%Y-%m-%d");
    $time_now  = CMbDT::format($datetime, "%H:%M:00");
    $hour_now  = CMbDT::format($datetime, "%H:00:00");
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
      $this->chrono  = CConsultation::PLANIFIE;
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
    $this->_count["consult_anesth"] = null;
    $consultAnesth = $this->loadRefConsultAnesth();
    $operation = new COperation();
    if (!$consultAnesth->_id || $this->_operation_id) {
      if (!$consultAnesth->_id) {
        $consultAnesth->consultation_id = $this->_id;
      }
      if ($this->_operation_id) {
        // Association à l'intervention
        $consultAnesth->operation_id = $this->_operation_id;
        $operation = $consultAnesth->loadRefOperation();
      }
      if ($msg = $consultAnesth->store()) {
        return $msg;
      }
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
          '%i' => CMbDT::format($operation->_datetime_best , CAppUI::conf('time')),
          '%I' => CMbDT::format($operation->_datetime_best , CAppUI::conf('date')),
          '%E' => CMbDT::format($sejour->entree_prevue, CAppUI::conf('date')),
          '%e' => CMbDT::format($sejour->entree_prevue, CAppUI::conf('time')),
          '%T' => strtoupper(substr($sejour->type, 0, 1)),
        );

        if ($format_motif && !$this->motif) {
          $this->motif = str_replace(array_keys($items), $items, $format_motif);
        }

        if ($format_rques && !$this->rques) {
          $this->rques = str_replace(array_keys($items), $items, $format_rques);
        }

        if ($msg = parent::store()) {
          return $msg;
        }
      }
    }

    return null;
  }

  /**
   * Charge les praticiens susceptibles d'être concernés par les consultation
   * en fonction de les préférences utilisateurs
   *
   * @param int    $permType    Type de permission
   * @param ref    $function_id Fonction spécifique
   * @param string $name        Nom spécifique
   * @param bool   $secondary   Chercher parmi les fonctions secondaires
   * @param bool   $actif       Seulement les actifs
   *
   * @return CMediusers[]
   */
  static function loadPraticiens($permType = PERM_READ, $function_id = null, $name = null, $secondary = false, $actif = true) {
    $user = new CMediusers();
    return $user->loadProfessionnelDeSanteByPref($permType, $function_id, $name, $secondary, $actif);
  }

  /**
   * Charge les praticiens à la compta desquels l'utilisateur courant a accès
   *
   * @param ref $prat_id Si définit, retourne un tableau avec seulement ce praticien
   *
   * @todo Définir verbalement la stratégie
   * @return CMediusers[]
   */
  static function loadPraticiensCompta($prat_id = null) {
    // Cas du praticien unique
    if ($prat_id) {
      $prat = new CMediusers();
      $prat->load($prat_id);
      $prat->loadRefFunction();
      return array($prat->_id => $prat);
    }

    // Cas standard
    $user = CMediusers::get();
    $is_admin      = in_array(CUser::$types[$user->_user_type], array("Administrator"));
    $is_secretaire = in_array(CUser::$types[$user->_user_type], array("Secrétaire"));
    $is_directeur  = in_array(CUser::$types[$user->_user_type], array("Directeur"));

    $function = $user->loadRefFunction();
    $praticiens = array();

    // Liste des praticiens du cabinet
    if ($is_admin || $is_secretaire || $is_directeur || $function->compta_partagee) {
      $function_id = null;
      if (!CAppUI::conf("cabinet Comptabilite show_compta_tiers") && $user->_user_username != "admin") {
        $function_id = $user->function_id;
      }

      if ($is_admin) {
        $praticiens = CConsultation::loadPraticiens(PERM_EDIT, $function_id);
      }
      else {
        $praticiens = CConsultation::loadPraticiens(PERM_EDIT, $user->function_id);

        // On ajoute les praticiens qui ont délégués leurs compta
        $where = array();
        $where[] = "users_mediboard.compta_deleguee = '1' ||  users_mediboard.user_id ".
          CSQLDataSource::prepareIn(array_keys($praticiens));
        // Filters on users values
        $where["users_mediboard.actif"] = "= '1'";
        $where["functions_mediboard.group_id"] = "= '".CGroups::loadCurrent()->_id."'";

        $ljoin["users"] = "users.user_id = users_mediboard.user_id";
        $ljoin["functions_mediboard"] = "functions_mediboard.function_id = users_mediboard.function_id";

        $order = "users.user_last_name, users.user_first_name";

        $mediuser = new CMediusers();
        /** @var CMediusers[] $mediusers */
        $mediusers = $mediuser->loadListWithPerms(PERM_EDIT, $where, $order, null, null, $ljoin);

        // Associate already loaded function
        foreach ($mediusers as $_mediuser) {
          $_mediuser->loadRefFunction();
        }
        $praticiens = $mediusers;
      }
    }
    elseif ($user->isPraticien() && !$user->compta_deleguee) {
      return array($user->_id => $user);
    }

    return $praticiens;
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
   * @see parent::getDynamicTag
   */
  function getDynamicTag() {
    return $this->conf("tag");
  }

  /**
   * Crée une facture cabinet pour la consultation
   *
   * @param string $type_facture Type de facture voulu
   *
   * @return CFactureCabinet|CFactureEtablissement
   */
  function createFactureConsult($type_facture = "maladie") {
    $facture               = $this->sejour_id ? new CFactureEtablissement() : new CFactureCabinet();
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
    return $facture;
  }

  /**
   * Loads the related patient, wether it is a far or a close reference
   *
   * @return CPatient
   */
  function loadRelPatient() {
    return $this->loadRefPatient();
  }

  /**
   * Charge le dossier d'anesthésie de la plage d'op la plus ancienne
   *
   * @return CConsultAnesth
   */
  function loadRefFirstDossierAnesth() {
    // Chargement des plages de chaques dossiers
    foreach ($this->_refs_dossiers_anesth as $_dossier) {
      $_dossier->loadRefOperation()->loadRefPlageOp();
    }
    $plages = CMbArray::pluck($this->_refs_dossiers_anesth, "_ref_operation", "_ref_plageop", "date");
    array_multisort($plages, SORT_ASC, $this->_refs_dossiers_anesth);
    return $this->_ref_consult_anesth = reset($this->_refs_dossiers_anesth);
  }

  /**
   * Get the patient_id of CMbobject
   *
   * @return CPatient
   */
  function getIndexablePatient () {
    return $this->loadRelPatient();
  }
  /**
   * Loads the related fields for indexing datum (patient_id et date)
   *
   * @return array
   */
  function getIndexableData () {
    $this->getIndexablePraticien();
    $array["id"]          = $this->_id;
    $array["author_id"]   = $this->_praticien_id;
    $array["prat_id"]     = $this->_ref_praticien->_id;
    $array["title"]       = $this->type;
    $array["body"]        = $this->getIndexableBody("");
    $array["date"]        = str_replace("-", "/", $this->loadRefPlageConsult()->date);
    $array["function_id"] = $this->_ref_praticien->function_id;
    $array["group_id"]    = $this->_ref_praticien->loadRefFunction()->group_id;
    $array["patient_id"]  = $this->getIndexablePatient()->_id;
    if ($this->loadRefSejour()) {
      $array["object_ref_id"]  = $this->_ref_sejour->_id;
      $array["object_ref_class"]  = $this->_ref_sejour->_class;
    }
    else {
      $array["object_ref_id"]  = $this->_id;
      $array["object_ref_class"]  = $this->_class;
    }


    return $array;
  }

  /**
   * count the number of consultations asking to be
   *
   * @param array()   $chir_ids list of chir ids
   * @param Date|null $day      date targeted, default = today
   *
   * @return int number of result
   */
  static function countDesistementsForDay($chir_ids, $day = null) {
    $date = CMbDT::date($day);
    $consultation = new self();
    $ds = $consultation->getDS();
    $where = array(
      "plageconsult.date"           => " > '$date'",
      "consultation.si_desistement" => "= '1'",
      "consultation.annule"         => "= '0'",
    );
    $where[] = "plageconsult.chir_id ".$ds->prepareIn($chir_ids)." OR plageconsult.remplacant_id ".$ds->prepareIn($chir_ids);
    $ljoin = array(
      "plageconsult" => "plageconsult.plageconsult_id = consultation.plageconsult_id",
    );
    return $consultation->countList($where, null, $ljoin);
  }


  /**
   * load "adresse par prat"
   *
   * @return CMedecin|null
   */
  function loadRefAdresseParPraticien() {
    return $this->_ref_adresse_par_prat = $this->loadFwdRef("adresse_par_prat_id", true);
  }

  /**
   * Redesign the content of the body you will index
   *
   * @param string $content The content you want to redesign
   *
   * @return string
   */
  function getIndexableBody ($content) {
    $fields = $this->getTextcontent();
    foreach ($fields as $_field) {
      $content .= " ".  $this->$_field;
    }

    return $content;
  }
  /**
   * Get the praticien_id of CMbobject
   *
   * @return CMediusers
   */
  function getIndexablePraticien () {
    return $this->loadRefPraticien();
  }

  static function  makeTabsCount($consult, $dossier_medical, $consultAnesth, $sejour, $list_etat_dents) {
    $tabs_count = array(
      "AntTrait"            => 0,
      "Constantes"          => 0,
      "prescription_sejour" => 0,
      "facteursRisque"      => 0,
      "Examens"             => 0,
      "Exams"               => 0,
      "ExamsComp"           => 0,
      "Intub"               => 0,
      "InfoAnesth"          => 0,
      "dossier_traitement"  => 0,
      "dossier_suivi"       => 0,
      "Actes"               => 0,
      "fdrConsult"          => 0,
      "reglement"           => 0
    );

    if (CModule::getActive("dPprescription")) {
      CPrescription::$_load_lite = true;
    }
    foreach ($tabs_count as $_tab => $_count) {
      $count = 0;
      switch ($_tab) {
        case "AntTrait":
          $prescription = $dossier_medical->loadRefPrescription();
          $count_meds = 0;
          if (CModule::getActive("dPprescription")) {
            $count_meds = $prescription->countBackRefs("prescription_line_medicament");
          }
          $dossier_medical->countTraitements();
          $dossier_medical->countAntecedents();
          $tabs_count[$_tab] = $dossier_medical->_count_antecedents + $dossier_medical->_count_traitements + $count_meds + count($dossier_medical->_ext_codes_cim);
          break;
        case "Constantes":
          if ($sejour->_ref_rpu && $sejour->_ref_rpu->_id) {
            $tabs_count[$_tab] = $sejour->countBackRefs("contextes_constante");
          }
          else {
            $tabs_count[$_tab] = $consult->countBackRefs("contextes_constante");
          }
          break;
        case "prescription_sejour":
          $_sejour = $sejour;
          if ($consultAnesth->_id && $consultAnesth->operation_id) {
            $_sejour = $consultAnesth->loadRefOperation()->loadRefSejour();
          }

          if ($_sejour->_id) {
            $_sejour->loadRefsPrescriptions();
            foreach ($_sejour->_ref_prescriptions as $key => $_prescription) {
              if (!$_prescription->_id) {
                unset($_sejour->_ref_prescriptions[$key]);
                continue;
              }

              $_sejour->_ref_prescriptions[$_prescription->_id] = $_prescription;
              unset($_sejour->_ref_prescriptions[$key]);
            }

            if (count($_sejour->_ref_prescriptions)) {
              $prescription = new CPrescription();
              $prescription->massCountMedsElements($_sejour->_ref_prescriptions);
              foreach ($_sejour->_ref_prescriptions as $_prescription) {
                $count += array_sum($_prescription->_counts_by_chapitre);
              }
            }
          }

          $tabs_count[$_tab] = $count;
          break;
        case "facteursRisque":
          if (!$consultAnesth) {
            break;
          }
          if ($dossier_medical->_id) {
            $fields = array(
              "risque_antibioprophylaxie", "risque_MCJ_chirurgie", "risque_MCJ_patient",
              "risque_prophylaxie", "risque_thrombo_chirurgie", "risque_thrombo_patient"
            );

            foreach ($fields as $_field) {
              if ($dossier_medical->$_field != "NR") {
                $count++;
              }
            }

            if ($dossier_medical->facteurs_risque) {
              $count++;
            }
          }
          $tabs_count[$_tab] = $count;
          break;
        case "Examens":
          if ($consultAnesth->_id) {
            break;
          }
          $fields = array("motif", "rques", "examen", "histoire_maladie", "conclusion");
          foreach ($fields as $_field) {
            if ($consult->$_field) {
              $count++;
            }
          }
          $count += $consult->countBackRefs("examaudio");
          $count += $consult->countBackRefs("examnyha");
          $count += $consult->countBackRefs("exampossum");
          $tabs_count[$_tab] = $count;
          break;
        case "Exams":
          if (!$consultAnesth->_id) {
            break;
          }
          $fields = array("examenCardio", "examenPulmo", "examenDigest", "examenAutre");
          foreach ($fields as $_field) {
            if ($consultAnesth->$_field) {
              $count++;
            }
          }
          if ($consult->examen != "") {
            $count++;
          }
          $count += $consult->countBackRefs("examaudio");
          $count += $consult->countBackRefs("examnyha");
          $count += $consult->countBackRefs("exampossum");
          $tabs_count[$_tab] = $count;
          break;
        case "ExamsComp":
          if (!$consultAnesth->_id) {
            break;
          }
          $count += $consult->countBackRefs("examcomp");
          if ($consultAnesth->result_ecg) {
            $count++;
          }
          if ($consultAnesth->result_rp) {
            $count++;
          }
          $tabs_count[$_tab] = $count;
          break;
        case "Intub":
          if (!$consultAnesth->_id) {
            break;
          }
          $fields = array(
            "mallampati", "bouche", "distThyro", "mob_cervicale", "etatBucco", "conclusion",
            "plus_de_55_ans", "edentation", "barbe", "imc_sup_26", "ronflements", "piercing"
          );
          foreach ($fields as $_field) {
            if ($consultAnesth->$_field) {
              $count++;
            }
          }
          $count += count(array_filter($list_etat_dents));
          $tabs_count[$_tab] = $count;
          break;
        case "InfoAnesth":
          if (!$consultAnesth->_id) {
            break;
          }
          $op = $consultAnesth->loadRefOperation();

          $fields_anesth = array("prepa_preop", "premedication", "apfel_femme", "apfel_non_fumeur", "apfel_atcd_nvp", "apfel_morphine");
          $fields_op = array("passage_uscpo", "type_anesth", "ASA", "position");

          foreach ($fields_anesth as $_field) {
            if ($consultAnesth->$_field) {
              $count++;
            }
          }
          if ($op->_id) {
            foreach ($fields_op as $_field) {
              if ($op->$_field) {
                $count++;
              }
            }
          }

          if ($consult->rques) {
            $count++;
          }

          $count += $consultAnesth->countBackRefs("techniques");

          $tabs_count[$_tab] = $count;
          break;
        case "dossier_traitement":
          break;
        case "dossier_suivi":
          break;
        case "Actes":
          $consult->countActes();
          $tabs_count[$_tab] = $consult->_count_actes;

          if ($sejour->_id) {
            if ($sejour->DP) {
              $tabs_count[$_tab]++;
            }
            if ($_sejour->DR) {
              $tabs_count[$_tab]++;
            }
            $sejour->loadDiagnosticsAssocies();
            $tabs_count[$_tab] += count($sejour->_diagnostics_associes);
          }
          break;
        case "fdrConsult":
          $consult->_docitems_from_dossier_anesth = false;
          $consult->countDocs();
          $consult->countFiles();
          $consult->loadRefsPrescriptions();
          $tabs_count[$_tab] = $consult->_nb_docs + $consult->_nb_files;
          if (isset($consult->_ref_prescriptions["externe"])) {
            $tabs_count[$_tab]++;
          }
          if ($sejour->_id) {
            $sejour->countDocs();
            $sejour->countFiles();
            $tabs_count[$_tab] += $sejour->_nb_docs + $sejour->_nb_files;
          }
          break;
        case "reglement":
          $consult->loadRefFacture()->loadRefsReglements();
          $tabs_count[$_tab] = count($consult->_ref_facture->_ref_reglements);
      }
    }
    if (CModule::getActive("dPprescription")) {
      CPrescription::$_load_lite = false;
    }

    return $tabs_count;
  }

  function loadRefBrancardage() {
    if (!CModule::getActive("brancardage")) {
      return null;
    }

    $this->loadRefPlageConsult();
    $ljoin = array();
    $ljoin["brancardage_item"] = "brancardage_item.brancardage_id = brancardage.brancardage_id";
    $where = array();
    $where["brancardage.sejour_id"] = " = '$this->sejour_id'";
    $where["brancardage.date"] = " = '$this->_date'";
    $where["brancardage_item.destination_class"] = " = 'CService'";

    $brancardage = new CBrancardage();
    $brancardage->loadObject($where, "brancardage_id DESC", null, $ljoin);
    $brancardage->loadRefItems();

    return $this->_ref_brancardage = $brancardage;
  }

  function loadAllDocs($tri = "date", $with_cancelled = false) {
    $this->mapDocs($this, $with_cancelled, $tri);

    if (count($this->_all_docs)) {
      ksort($this->_all_docs);
    }
  }
}

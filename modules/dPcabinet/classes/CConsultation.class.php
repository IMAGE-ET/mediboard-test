<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

class CConsultation extends CCodable {
  const PLANIFIE       = 16;
  const PATIENT_ARRIVE = 32;
  const EN_COURS       = 48;
  const TERMINE        = 64;

  // DB Table key
  var $consultation_id = null;

  // DB References
  var $plageconsult_id = null;
  var $patient_id      = null;
  var $sejour_id       = null;
  var $grossesse_id    = null;
  var $factureconsult_id  = null;

  // DB fields
  var $type            = null;
  var $heure           = null;
  var $duree           = null;
  var $secteur1        = null;
  var $secteur2        = null;
  var $chrono          = null;
  var $annule          = null;

  var $patient_date_reglement = null;
  var $tiers_date_reglement   = null;

  var $motif            = null;
  var $rques            = null;
  var $examen           = null;
  var $histoire_maladie = null;
  var $brancardage      = null;
  var $conclusion       = null;

  var $traitement          = null;
  var $premiere            = null;
  var $derniere            = null;
  var $adresse             = null; // Le patient a-t'il été adressé ?
  var $adresse_par_prat_id = null;
  var $tarif               = null;

  var $arrivee         = null;
  var $categorie_id    = null;
  var $valide          = null; // Cotation validée
  var $si_desistement  = null;

  var $total_assure    = null;
  var $total_amc       = null;
  var $total_amo       = null;

  var $du_patient       = null; // somme que le patient doit régler
  var $du_tiers         = null;
  var $date_at          = null;
  var $fin_at           = null;
  var $pec_at           = null;
  var $reprise_at       = null;
  var $at_sans_arret    = null;
  var $arret_maladie    = null;
  var $concerne_ALD     = null;

  // Form fields
  var $_etat           = null;
  var $_hour           = null;
  var $_min            = null;
  var $_check_adresse  = null;
  var $_somme          = null;
  var $_types_examen   = null;
  var $_precode_acte   = null;
  var $_exam_fields    = null;
  var $_acte_dentaire_id = null;
  var $_function_secondary_id = null;
  var $_semaine_grossesse = null;
  var $_type           = null;  // Type de la consultation

  // Fwd References
  var $_ref_patient      = null; // Declared in CCodable
  var $_ref_sejour       = null; // Declared in CCodable
  var $_ref_plageconsult = null;
  var $_ref_adresse_par_prat = null;

  // FSE
  var $_bind_fse       = null;
  var $_ids_fse        = null;
  var $_ext_fses       = null;
  var $_current_fse    = null;
  var $_fse_intermax   = null;

  // Tarif
  var $_bind_tarif     = null;
  var $_tarif_id       = null;

  // Back References
  var $_ref_consult_anesth     = null;
  var $_ref_examaudio          = null;
  var $_ref_examcomp           = null;
  var $_ref_examnyha           = null;
  var $_ref_exampossum         = null;
  var $_count_fiches_examen    = null;
  var $_ref_reglements         = null;
  var $_ref_reglements_patient = null;
  var $_ref_reglements_tiers   = null;
  var $_ref_grossesse          = null;
  var $_ref_facture            = null;
  var $_ref_prescription       = null;
  var $_ref_categorie          = null;

  // Distant fields
  var $_ref_chir                 = null;
  var $_date                     = null;
  var $_datetime                 = null;
  var $_date_fin                 = null;
  var $_is_anesth                = null;
  var $_du_patient_restant       = null;
  var $_reglements_total_patient = null;
  var $_du_tiers_restant         = null;
  var $_reglements_total_tiers   = null;
  var $_forfait_se               = null;
  var $_forfait_sd               = null;
  var $_facturable               = null;

  // Filter Fields
  var $_date_min               = null;
  var $_date_max               = null;
  var $_prat_id                = null;
  var $_etat_reglement_patient = null;
  var $_etat_reglement_tiers   = null;
  var $_type_affichage         = null;
  var $_telephone              = null;
  var $_coordonnees            = null;
  var $_plages_vides           = null;
  var $_empty_places           = null;
  var $_non_pourvues           = null;

  // Behaviour fields
  var $_operation_id = null;

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

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["consult_anesth"]    = "CConsultAnesth consultation_id";
    $backProps["examaudio"]         = "CExamAudio consultation_id";
    $backProps["examcomp"]          = "CExamComp consultation_id";
    $backProps["examnyha"]          = "CExamNyha consultation_id";
    $backProps["exampossum"]        = "CExamPossum consultation_id";
    $backProps["prescriptions"]     = "CPrescription object_id";
    $backProps["reglements"]        = "CReglement object_id";
    $backProps["actes_dentaires"]   = "CActeDentaire consult_id";
    $backProps["echanges_hprimxml"] = "CEchangeHprim object_id";
    return $backProps;
  }

  function getProps() {
    $props = parent::getProps();
    $props["sejour_id"]         = "ref class|CSejour";
    $props["plageconsult_id"]   = "ref notNull class|CPlageconsult seekable show|1";
    $props["patient_id"]        = "ref class|CPatient purgeable seekable show|1";
    $props["categorie_id"]      = "ref class|CConsultationCategorie show|1";
    $props["grossesse_id"]      = "ref class|CGrossesse show|0 unlink";
    $props["factureconsult_id"] = "ref class|CFactureConsult show|0";
    $props["_praticien_id"]     ="ref class|CMediusers show|1"; //is put here for view
    $props["_function_secondary_id"] = "ref class|CFunctions";
    $props["motif"]             = "text helped seekable";
    $props["type"]              = "enum list|classique|entree|chimio default|classique";
    $props["heure"]             = "time notNull show|0";
    $props["duree"]             = "num min|1 max|9 notNull default|1 show|0";
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

    $props["date_at"]  = "date";
    $props["fin_at"]   = "dateTime";
    $props["pec_at"]   = "enum list|soins|arret";
    $props["reprise_at"] = "dateTime";
    $props["at_sans_arret"] = "bool default|0";
    $props["arret_maladie"] = "bool default|0";

    $props["total_amo"]         = "currency show|0";
    $props["total_amc"]         = "currency show|0";
    $props["total_assure"]      = "currency show|0";

    $props["valide"]            = "bool show|0";
    $props["si_desistement"]    = "bool notNull default|0";

    $props["_du_patient_restant"]       = "currency";
    $props["_du_tiers_restant"]         = "currency";
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
    $props["_prat_id"]          = "ref class|CMediusers";
    $props["_acte_dentaire_id"] = "ref class|CActeDentaire";
    $props["_ref_grossesse"]    = "ref class|CGrossesse";
    return $props;
  }

  function getEtat() {
    $etat = array();
    $etat[self::PLANIFIE]       = "Plan.";
    $etat[self::PATIENT_ARRIVE] = mbTransformTime(null, $this->arrivee, "%Hh%M");
    $etat[self::EN_COURS]       = "En cours";
    $etat[self::TERMINE]        = "Term.";
    if ($this->chrono)
      $this->_etat = $etat[$this->chrono];
    if ($this->annule) {
      $this->_etat = "Ann.";
    }
  }

  function getTemplateClasses(){
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
    $this->getEtat();
    $this->_view = "Consultation ".$this->_etat;

    // si _coded vaut 1 alors, impossible de modifier la cotation
    $this->_coded = $this->valide;

    // pour récuperer le praticien depuis la plage consult
    $this->loadRefPlageConsult(true);
    $plageconsult = $this->_ref_plageconsult;
    $this->_date_fin = "$plageconsult->date ". mbTime("+".mbMinutesRelative("00:00:00", $plageconsult->freq)*$this->duree." MINUTES", $this->heure);

    $this->_exam_fields = $this->getExamFields();
  }

  function updatePlainFields() {
    if (($this->_hour !== null) && ($this->_min !== null)) {
      $this->heure = $this->_hour.":".$this->_min.":00";
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

    // Dévalidation avec règlement déjà effectué
    if ($this->fieldModified("valide", "0")) {
      if (count($this->_ref_reglements)) {
        $msg .= "Vous ne pouvez plus dévalider le tarif, des règlements ont déjà été effectués";
      }
    }

    /*
    if ($this->_old->valide === "0") {
      // Règlement sans validation
      if ($this->fieldModified("tiers_date_reglement")
       || ($this->fieldModified("patient_date_reglement") && $this->patient_date_reglement !== "")) {
        $msg .= "Vous ne pouvez pas effectuer le règlement si le tarif n'a pas été validé";
      }
    }
    */
    if ($this->_old->valide === "1" && $this->valide === "1") {
      // Modification du tarif déjà validé
      if ($this->fieldModified("secteur1")
       || $this->fieldModified("secteur2")
       || $this->fieldModified("total_assure")
       || $this->fieldModified("total_amc")
       || $this->fieldModified("total_amo")
       || $this->fieldModified("du_patient")
       || $this->fieldModified("du_tiers")) {
        //$msg .= $this->du_patient." vs. ".$this->_old->du_patient." (".$this->fieldModified("du_patient").")";
        $msg .= "Vous ne pouvez plus modifier le tarif, il est déjà validé";
      }
    }

    return $msg . parent::check();
  }

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
   * Chargement des identifiants des FSE associées
   * @return void
   */
  /*function loadIdsFSE() {
    $id_fse = new CIdSante400();
    $id_fse->setObject($this);
    $id_fse->tag = "LogicMax FSENumero";
    $id_fse = $id_fse->loadMatchingList();
    $this->_ids_fse = CMbArray::pluck($id_fse, "id400");

    // Chargement des FSE externes
    $fse = @new CLmFSE();
    if (!isset($fse->_spec->ds)) {
      return;
    }
    $where = array();
    $where["S_FSE_NUMERO_FSE"] = CSQLDataSource::prepareIn($this->_ids_fse);
    $this->_ext_fses = $fse->loadList($where);

    // Last FSE
    $this->_current_fse = null;
    foreach ($this->_ext_fses as $_ext_fse) {
      if (!$_ext_fse->_annulee) {
        $this->_current_fse = $_ext_fse;
      }
    }
  }*/

  /**
   * deleteActes() Redefinition
   * @return string Store-like message
   */
  function deleteActes() {
    if ($msg = parent::deleteActes()) {
      return $msg;
    }

    $this->secteur1 = "";
    $this->secteur2 = "";
//    $this->valide = 0; // Ne devrait pas être nécessaire
    $this->total_assure = 0.0;
    $this->total_amc = 0.0;
    $this->total_amo = 0.0;
    $this->du_patient = 0.0;
    $this->du_tiers = 0.0;

    if ($msg = $this->store()) {
      return $msg;
    }
  }

  function bindTarif(){
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
    if ($msg = $this->precodeNGAP()) {
      return $msg;
    }

    $this->codes_ccam = $tarif->codes_ccam;
    // Precodage des actes CCAM avec information sérialisée complète
    if ($msg = $this->precodeCCAM()) {
      return $msg;
    }

    if (CModule::getActive("tarmed")) {
      $this->_tokens_tarmed = $tarif->codes_tarmed;
      if ($msg = $this->precodeTARMED()) {
        return $msg;
      }
      $this->_tokens_caisse = $tarif->codes_caisse;
      if ($msg = $this->precodeCAISSE()) {
        return $msg;
      }
    }
  }

  /**
   * Create a LogicMaxFSE from the consult
   * Conterpart to Bind FSE
   * @return void
   */
  /*function makeFSE() {
    $this->_fse_intermax = array();

    // Ajout des actes NGAP
    $this->loadRefsActesNGAP();
    if ($this->_ref_actes_ngap) {
      foreach ($this->_ref_actes_ngap as $acte_ngap) {
        $acteNumber = count($this->_fse_intermax)+1;
        $this->_fse_intermax["ACTE_$acteNumber"] = array(
          "PRE_ACTE_TYPE"   => 0,
          "PRE_DEPASSEMENT" => $acte_ngap->montant_depassement,
          "PRE_CODE"        => $acte_ngap->code,
          "PRE_COEFFICIENT" => $acte_ngap->demi ? $acte_ngap->coefficient/2 : $acte_ngap->coefficient,
          "PRE_QUANTITE"    => $acte_ngap->quantite,
          "PRE_DEMI"        => $acte_ngap->demi,
        );
      }
    }
    // Ajout des actes CCAM
    $this->loadRefsActesCCAM();
    if ($this->_ref_actes_ccam) {
      foreach ($this->_ref_actes_ccam as $acte_ccam) {
        $acteNumber = count($this->_fse_intermax)+1;
        $ACTE = array(
          "PRE_ACTE_TYPE"     => 1,
          "PRE_DEPASSEMENT"   => $acte_ccam->montant_depassement,
          "PRE_CODE_CCAM"     => $acte_ccam->code_acte,
          "PRE_CODE_ACTIVITE" => $acte_ccam->code_activite,
          "PRE_CODE_PHASE"    => $acte_ccam->code_phase,
          "PRE_ASSOCIATION"   => $acte_ccam->code_association,
          "PRE_RMB_EXCEP"     => $acte_ccam->_rembex ? "O" : "N",
          );

        // Ajout des modificateurs
        for ($i = 1; $i <= 4; $i++) {
          $ACTE["PRE_MODIF_$i"] = @$acte_ccam->_modificateurs[$i-1];
        }

        $this->_fse_intermax["ACTE_$acteNumber"] = $ACTE;
      }
    }

    // Section FSE
    $this->_fse_intermax["FSE"] = array();

    if ($this->date_at) {
      $this->_fse_intermax["FSE"]["FSE_NATURE_ASSURANCE"] = "AT";
      $this->_fse_intermax["FSE"]["FSE_DATE_AT"] = mbDateToLocale($this->date_at);
    }

    if ($this->concerne_ALD) {
      $this->_fse_intermax["FSE"]["FSE_ALD"] = "1";
    }

    if (!count($this->_fse_intermax["FSE"])) {
      unset($this->_fse_intermax["FSE"]);
    }
  }*/

  /**
   * Bind a FSE to current consult
   * @return string Store-like message
   */
  function bindFSE() {
    // Prevents recursion
    $this->_bind_fse = false;

    if (null == $intermax = CValue::postOrSessionAbs("intermax")) {
      return;
    }

    // Make id externe
    $fse = $intermax["FSE"];
    $fseNumero = $fse["FSE_NUMERO_FSE"];
    $id_fse = new CIdSante400();
    $id_fse->object_class = $this->_class;
    $id_fse->id400 = $fseNumero;
    $id_fse->tag = "LogicMax FSENumero";
    $id_fse->loadMatchingObject();

    // Autre association ?
    if ($id_fse->object_id && $id_fse->object_id != $this->_id) {
      $id_fse->loadTargetObject();
      $consOther =& $id_fse->_ref_object;
      $consOther->loadRefsFwd();
      return sprintf ("FSE déjà associée à la consultation du patient %s - %s le %s",
        $consOther->_ref_patient->_view,
        $consOther->_ref_chir->_view,
        mbDateToLocale($consOther->_date));
    }

    $id_fse->object_id = $this->_id;
    $id_fse->last_update = mbDateTime();

    if ($msg = $id_fse->store()) {
      return $msg;
    }

    // Ajout des actes CCAM et NGAP récupérés
    for ($iActe = 1; $fseActe = @$intermax["ACTE_$iActe"]; $iActe++) {
      switch ($typeActe = $fseActe["PRE_ACTE_TYPE"]) {
        case "0":
        $acte = new CActeNGAP();
        $acte->setObject($this);
        $acte->executant_id  = $this->getExecutantId();
        $acte->code        = $fseActe["PRE_CODE"];
        $acte->quantite    = $fseActe["PRE_QUANTITE"];
        $acte->coefficient = $fseActe["PRE_COEFFICIENT"];
        $acte->demi        = $fseActe["PRE_DEMI"];
        $acte->getLibelle();

        // Coefficient facial doublé
        if ($acte->demi) {
          $acte->coefficient *= 2;
        }

        break;

        case "1":
        $acte = new CActeCCAM();
        $acte->setObject($this);
        $acte->_adapt_object = true;
        $acte->executant_id  = $this->getExecutantId($acte->code_acte);
        $acte->code_acte     = $fseActe["PRE_CODE_CCAM"];
        $acte->code_activite = $fseActe["PRE_CODE_ACTIVITE"];
        $acte->code_phase    = $fseActe["PRE_CODE_PHASE"];
        $acte->execution     = $this->_acte_execution;
        if ($fseActe["PRE_RMB_EXCEP"]) {
          $acte->rembourse     = "1";
        }
        $acte->modificateurs = null;

        for ($iModif = 1; $iModif <= 4; $iModif++) {
          $acte->modificateurs .= $fseActe["PRE_MODIF_$iModif"];
        }

        $acte->code_association = $fseActe["PRE_ASSOCIATION"];

        break;

        default:
        return "Acte LogicMax de type inconnu (Numero = '$typeActe')";
      }

      $acte->montant_base = $fseActe["PRE_BASE"];
      $acte->montant_depassement = $fseActe["PRE_MONTANT"] - $fseActe["PRE_BASE"];

      if ($msg = $acte->store()) {
        return $msg;
      }
    }

    // Nom par défaut si non défini
    $consult = new CConsultation();
    $consult->load($this->_id);

    // Sauvegarde des tarifs de la consultation
    $consult->total_assure = $fse["FSE_TOTAL_ASSURE"];
    $consult->total_amo    = $fse["FSE_TOTAL_AMO"];
    $consult->total_amc    = $fse["FSE_TOTAL_AMC"];
    $consult->date_at      = mbDateFromLocale($fse["FSE_DATE_AT"]);

    $consult->du_patient = $consult->total_assure;
    $consult->du_tiers   = $consult->total_amo + $consult->total_amc;

    if (!in_array($fse["FSE_TIERS_PAYANT"], array("2", "3"))) {
      $consult->du_patient += $consult->total_amo;
      $consult->du_tiers   -= $consult->total_amo;
    }

    if (!in_array($fse["FSE_TIERS_PAYANT"], array("3", "4"))) {
      $consult->du_patient += $consult->total_amc;
      $consult->du_tiers   -= $consult->total_amc;
    }

    $consult->valide = '1';
    if (!$consult->tarif) {
      $consult->tarif = "FSE LogicMax";
    }
    
    $consult->loadRefFacture();
    mbLog($consult->_ref_facture);
    $consult->_ref_facture->du_patient = $consult->du_patient;
    $consult->_ref_facture->du_tiers = $consult->du_tiers;
    $consult->_ref_facture->store();
    mbLog($consult->_ref_facture);
    return $consult->store();
  }

  function precodeCCAM() {
    $this->loadRefPlageConsult();
    // Explode des codes_ccam du tarif
    $listCodesCCAM = explode("|", $this->codes_ccam);
    foreach ($listCodesCCAM as $key => $code) {
      $acte = new CActeCCAM();
      $acte->_adapt_object = true;

      $acte->_preserve_montant = true;
      $acte->setFullCode($code);

      // si le code ccam est composé de 3 elements, on le precode
      if ($acte->code_activite != "" && $acte->code_phase != "") {
        // Permet de sauvegarder le montant de base de l'acte CCAM
        $acte->_calcul_montant_base = 1;

        // Mise a jour de codes_ccam suivant les _tokens_ccam du tarif
        $acte->object_id = $this->_id;
        $acte->object_class = $this->_class;
        $acte->executant_id = $this->_ref_chir->_id;
        $acte->execution = $this->_datetime;
        if ($msg = $acte->store()) {
          return $msg;
        }
      }
    }
  }

  function precodeNGAP() {
    $listCodesNGAP = explode("|",$this->_tokens_ngap);
    foreach ($listCodesNGAP as $key => $code_ngap) {
      if ($code_ngap) {
        $acte = new CActeNGAP();
        $acte->_preserve_montant = true;
        $acte->setFullCode($code_ngap);

        $acte->object_id = $this->_id;
        $acte->object_class = $this->_class;
        $acte->executant_id = $this->getExecutantId();
        if (!$acte->countMatchingList()) {
          if ($msg = $acte->store()) {
            return $msg;
          }
        }
      }
    }
  }

  function precodeTARMED() {
    $listCodesTarmed = explode("|",$this->_tokens_tarmed);
    foreach ($listCodesTarmed as $key => $code_tarmed) {
      if ($code_tarmed) {
        $acte = new CActeTarmed();
        $acte->_preserve_montant = true;
        $acte->setFullCode($code_tarmed);

        $acte->object_id = $this->_id;
        $acte->object_class = $this->_class;
        $acte->executant_id = $this->getExecutantId();
        if (!$acte->countMatchingList()) {
          if ($msg = $acte->store()) {
            return $msg;
          }
        }
      }
    }
  }

  function precodeCAISSE() {
    $listCodesCaisse = explode("|",$this->_tokens_caisse);
    foreach ($listCodesCaisse as $key => $code_caisse) {
      if ($code_caisse) {
        $acte = new CActeCaisse();
        $acte->_preserve_montant = true;
        $acte->setFullCode($code_caisse);

        $acte->object_id = $this->_id;
        $acte->object_class = $this->_class;
        $acte->executant_id = $this->getExecutantId();
        if (!$acte->countMatchingList()) {
          if ($msg = $acte->store()) {
            return $msg;
          }
        }
      }
    }
  }

  function doUpdateMontants(){
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

    if (CModule::getActive("tarmed") && CAppUI::conf("tarmed CCodeTarmed use_cotation_tarmed") ) {
      // Chargement des actes Tarmed
      $this->loadRefsActesTarmed();
      foreach ($this->_ref_actes_tarmed as $actetarmed) {
        $count_actes++;
        $secteur1_TARMED += round($actetarmed->montant_base , 2);
        $secteur2_TARMED += round($actetarmed->montant_depassement, 2);
      }
      $this->loadRefsActesCaisse();
      foreach ($this->_ref_actes_caisse as $actecaisse) {
        $count_actes++;
        $secteur1_CAISSE += round($actecaisse->montant_base , 2);
        $secteur2_CAISSE += round($actecaisse->montant_depassement, 2);
      }
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
   * @todo: refactoring complet de la fonction store de la consultation

ANALYSE DU CODE
 1. Gestion du désistement
 2. Premier if : creation d'une consultation à laquelle on doit attacher
    un séjour (conf active): comportement DEPART / ARRIVEE
 3. Mise en cache du forfait FSE et facturable : uniquement dans le cas d'un séjour
 4. On load le séjour de la consultation
 5. On initialise le _adjust_sejour à false
 6. Dans le cas ou on a un séjour
  6.1. S'il est de type consultation, on ajuste le séjour en fonction du
       comportement DEPART / ARRIVEE
  6.2. Si la plage de consultation a été modifiée, adjust_sejour passe à
       true et on ajuste le séjour en fonction du comportement DEPART / ARRIVEE
       (en passant par l'adjustSejour() )
  6.3. Si on a un id (à virer) et que le chrono est modifié en PATIENT_ARRIVE,
       si on gère les admissions auto (conf) on met une entrée réelle au séjour
 7. Si le patient est modifié, qu'on est pas en train de merger et qu'on a un séjour,
    on empeche le store
 8. On appelle le parent::store()
 9. On passe le forfait SE et facturable au séjour
10. On propage la modification du patient de la consultation au séjour
11. Si on a ajusté le séjour et qu'on est dans un séjour de type conclut et que le séjour
    n'a plus de consultations, on essaie de le supprimer, sinon on l'annule
12. Gestion du tarif et précodage des actes (bindTarif)
13. Bind FSE

ACTIONS :
- Faire une fonction comportement_DEPART_ARRIVEE()
- Merger le 2, le 6.1 et le 6.2 (et le passer en 2 si possible)
- Faire une fonction pour le 6.3, le 7, le 10, le 11
- Améliorer les fonctions 12 et 13 en incluant le test du behaviour fields

COMPORTEMENT DEPART ARRIVEE
modif de la date d'une consultation ayant un séjour sur le modèle DEPART / ARRIVEE:
1. Pour le DEPART :
-> on décroche la consultation de son ancien séjour
-> on ne touche pas à l'ancien séjour si :
- il est de type autre que consultation
- il a une entrée réelle
- il a d'autres consultations
-> sinon on l'annule

2. Pour l'ARRIVEE
-> si on a un séjour qui englobe : on la colle dedans
-> sinon on crée un séjour de consultation

TESTS A EFFECTUER
 0. Création d'un pause
 0.1. Déplacement d'une pause
 1. Création d'une consultation simple C1 (Séjour S1)
 2. Création d'une deuxième consultation le même jour / même patient C2 (Séjour S1)
 3. Création d'une troisième consultation le même jour / même patient C3 (Séjour S1)
 4. Déplacement de la consultation C1 un autre jour (Séjour S2)
 5. Changement du nom du patient C2 (pas de modification car une autre consultation)
 6. Déplacement de C3 au même jour (Toujours séjour S1)
 7. Annulation de C1 (Suppression ou annulation de S1)
 8. Déplacement de C2 et C3 à un autre jour (séjour S3 créé, séjour S1 supprimé ou annulé)
 9. Arrivée du patient pour C2 (S3 a une entrée réelle)
10. Déplacement de C3 dans un autre jour (S4)
11. Déplacement de C2 dans un autre jour (S5 et S3 reste tel quel)
   */
  function store() {
    $this->completeField('sejour_id', 'heure', 'plageconsult_id', 'si_desistement');

    if ($this->si_desistement === null) {
      $this->si_desistement = 0;
    }

    // Consultation dans un séjour
    if (!$this->_id && !$this->sejour_id &&
        CAppUI::conf("dPcabinet CConsultation attach_consult_sejour") && $this->patient_id) {
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
      $where['patient_id'] = " = '$this->patient_id'";
      if (!CAppUI::conf("dPcabinet CConsultation search_sejour_all_groups")) {
        $where['group_id']   = " = '$function->group_id'";
      }
      $where['facturable']     = " = '$facturable'";
      $datetime_before     = mbDateTime("+$minutes_before_consult_sejour minute", "$this->_date $this->heure");
      $where[] = "`sejour`.`entree` <= '$datetime_before' AND `sejour`.`sortie` >= '$datetime'";

      $sejour = new CSejour();
      $sejour->loadObject($where);

      // Si pas de séjour et config alors le créer en type consultation
      if (!$sejour->_id && CAppUI::conf("dPcabinet CConsultation create_consult_sejour")) {
        $sejour->patient_id = $this->patient_id;
        $sejour->praticien_id = $this->_ref_chir->_id;
        $sejour->group_id = $function->group_id;
        $sejour->type = "consult";
        $sejour->facturable = $facturable;
        $datetime = ($this->_date && $this->heure) ? "$this->_date $this->heure" : NULL;
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

    $this->loadRefSejour();
    $this->_adjust_sejour = false;

    if ($this->sejour_id) {
      $this->loadRefPlageConsult();

      // Si le séjour est de type consult
      if ($this->_ref_sejour->type == 'consult') {
        $this->_ref_sejour->loadRefsConsultations();
        $nb_consults_dans_sejour = count($this->_ref_sejour->_ref_consultations);
        $this->_ref_sejour->_hour_entree_prevue = null;
        $this->_ref_sejour->_min_entree_prevue  = null;
        $this->_ref_sejour->_hour_sortie_prevue = null;
        $this->_ref_sejour->_min_sortie_prevue  = null;

        $date_consult = mbDate($this->_datetime);

        // On déplace l'entrée et la sortie du séjour
        $entree = $this->_datetime;
        $sortie = $date_consult . " 23:59:59";

        // Si on a une entrée réelle et que la date de la consultation est avant l'entrée réelle, on sort du store
        if ($this->_ref_sejour->entree_reelle && $date_consult < mbDate($this->_ref_sejour->entree_reelle)) {
          return CAppUI::tr("CConsultation-denyDayChange");
        }

        // Si on a une sortie réelle et que la date de la consultation est après la sortie réelle, on sort du store
        if ($this->_ref_sejour->sortie_reelle && $date_consult > mbDate($this->_ref_sejour->sortie_reelle)) {
          return CAppUI::tr("CConsultation-denyDayChange-exit");
        }

        // S'il n'y a qu'une seule consultation dans le séjour, et que le praticien de la consultation est modifié
        // (changement de plage), alors on modifie également le praticien du séjour
        if ($this->_id && $this->fieldModified("plageconsult_id") &&
            count($this->_ref_sejour->_ref_consultations) == 1 &&
            !$this->_ref_sejour->entree_reelle) {
          $this->_ref_sejour->praticien_id = $this->_ref_plageconsult->chir_id;
        }

        // S'il y a d'autres consultations dans le séjour, on étire l'entrée et la sortie
        // en parcourant la liste des consultations
        foreach ($this->_ref_sejour->_ref_consultations as $_consultation) {
          if ($_consultation->_id != $this->_id) {
            $_consultation->loadRefPlageConsult();

            if ($_consultation->_datetime < $entree)
              $entree = $_consultation->_datetime;

            if ($_consultation->_datetime > $sortie)
               $sortie = mbDate($_consultation->_datetime) . " 23:59:59";
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
        if ($this->_ref_sejour->entree_reelle &&
           (mbDateTime("+ $max_hours HOUR", $this->_ref_sejour->entree_reelle) < mbDateTime())) {
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
      $this->reprise_at = mbDateTime("+1 DAY", $this->fin_at);
    }

    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }

    $this->createConsultAnesth();

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
    if ($this->_bind_tarif && $this->_id){
      if ($msg = $this->bindTarif()) {
        return $msg;
      }
    }

    // Bind FSE
    if ($this->_bind_fse && $this->_id) {
      return $this->bindFSE();
    }
  }

  function loadRefCategorie($cache = 0) {
    $this->_ref_categorie = $this->loadFwdRef("categorie_id", $cache);
  }

  function loadComplete() {
    parent::loadComplete();
    $this->_ref_patient->loadRefConstantesMedicales();
    foreach ($this->_ref_actes_ccam as &$acte_ccam) {
      $acte_ccam->loadRefsFwd();
    }
  }

  function loadRefPatient($cache = 1) {
    return $this->_ref_patient = $this->loadFwdRef("patient_id", $cache);
  }

  /**
   * Chargement du sejour et du RPU dans le cas d'une urgence
   *
   * @var CSejour
   */
  function loadRefSejour($cache = 1) {
    $this->_ref_sejour = $this->loadFwdRef("sejour_id", $cache);
    $this->_ref_sejour->loadRefRPU();

    if (CAppUI::conf("dPcabinet CConsultation attach_consult_sejour")) {
      $this->_forfait_se = $this->_ref_sejour->forfait_se;
      $this->_forfait_sd = $this->_ref_sejour->forfait_sd;
      $this->_facturable = $this->_ref_sejour->facturable;
    }

    return $this->_ref_sejour;
  }

  function loadRefGrossesse() {
    return $this->_ref_grossesse = $this->loadFwdRef("grossesse_id", true);
  }

  function loadRefFacture() {
    return $this->_ref_facture = $this->loadFwdRef("factureconsult_id", true);
  }

  function getActeExecution() {
    $this->loadRefPlageConsult();
  }

  /**
   * @param boolean $cache [optional]
   * @return CPlageconsult
   */
  function loadRefPlageConsult($cache = 1) {
    if ($this->_ref_plageconsult) {
      return $this->_ref_plageconsult;
    }

    $this->_ref_plageconsult = $this->loadFwdRef("plageconsult_id", $cache);
    $this->_ref_plageconsult->loadRefsFwd($cache);

    // Distant fields
    $this->_ref_chir = $this->_ref_plageconsult->_ref_remplacant->_id ?
      $this->_ref_plageconsult->_ref_remplacant :
      $this->_ref_plageconsult->_ref_chir;

    $this->_date     = $this->_ref_plageconsult->date;
    $this->_datetime = mbAddDateTime($this->heure,$this->_date);
    $this->_acte_execution = $this->_datetime;
    $this->_is_anesth    = $this->_ref_chir->isAnesth();
    $this->_is_dentiste  = $this->_ref_chir->isDentiste();
    $this->_praticien_id = $this->_ref_chir->_id;

    return $this->_ref_plageconsult;
  }

  function loadRefPraticien(){
    $this->loadRefPlageConsult();
    $this->_ref_executant = $this->_ref_plageconsult->_ref_chir;
    return $this->_ref_praticien = $this->_ref_chir;
  }

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
    if ($praticien->isAnesth()) {
      $this->_type = "anesth";
    }
  }

  function preparePossibleActes() {
    $this->loadRefPlageConsult();
  }

  function loadRefsFwd($cache = 1) {
    $this->loadRefPatient($cache);
    $this->_ref_patient->loadRefConstantesMedicales();
    $this->loadRefPlageConsult($cache);
    $this->_view = "Consult. de ".$this->_ref_patient->_view." - ".$this->_ref_plageconsult->_ref_chir->_view;
    $this->_view .= " (".mbTransformTime(null, $this->_ref_plageconsult->date, "%d/%m/%Y").")";
    $this->loadExtCodesCCAM();
  }

  function loadRefsDocs() {
    parent::loadRefsDocs();

    // On ajoute les documents de la consultation d'anesthésie
    $this->loadRefConsultAnesth();
    $consult_anesth =& $this->_ref_consult_anesth;
    if ($consult_anesth->_id) {
      $consult_anesth->loadRefsDocs();
      $this->_ref_documents = CMbArray::mergeKeys($this->_ref_documents, $consult_anesth->_ref_documents);
    }

    return count($this->_ref_documents);
  }

  function getExecutantId($code_activite = null) {
    $this->loadRefPlageConsult();
    return $this->_praticien_id;
  }

  function countDocItems($permType = null){
    if (!$this->_nb_files_docs) {
      parent::countDocItems($permType);
    }

    if ($this->_nb_files_docs) {
      $this->getEtat();
      $this->_etat .= " ($this->_nb_files_docs)";
    }
  }

  function countDocs(){
    $nbDocs = parent::countDocs();

    // Ajout des documents de la consultation d'anesthésie
     $this->loadRefConsultAnesth();
    if ($this->_ref_consult_anesth->_id) {
      $nbDocs += $this->_ref_consult_anesth->countDocs();
    }

    return $nbDocs;
  }

  function loadRefConsultAnesth() {
    return $this->_ref_consult_anesth = $this->loadUniqueBackRef("consult_anesth");
  }

  function loadRefsExamAudio(){
    // @todo Ne pas utiliser la backref => ne fonctionne pas
    //$this->_ref_examaudio = $this->loadUniqueBackRef("examaudio");

    $this->_ref_examaudio = new CExamAudio;
    $where = array();
    $where["consultation_id"] = "= '$this->consultation_id'";
    $this->_ref_examaudio->loadObject($where);
  }

  function loadRefsExamNyha(){
    $this->_ref_examnyha = $this->loadUniqueBackRef("examnyha");
  }

  function loadRefsExamPossum(){
    $this->_ref_exampossum = $this->loadUniqueBackRef("exampossum");
  }

  function loadRefsFichesExamen() {
    $this->loadRefsExamAudio();
    $this->loadRefsExamNyha();
    $this->loadRefsExamPossum();
    $this->_count_fiches_examen = 0;
    $this->_count_fiches_examen += $this->_ref_examaudio ->_id ? 1 : 0;
    $this->_count_fiches_examen += $this->_ref_examnyha  ->_id ? 1 : 0;
    $this->_count_fiches_examen += $this->_ref_exampossum->_id ? 1 : 0;
  }

  // Chargement des prescriptions liées à la consultation
  function loadRefsPrescriptions() {
    $prescriptions = $this->loadBackRefs("prescriptions");
    // Cas du module non installé
    if (!is_array($prescriptions)) {
      $this->_ref_prescriptions = null;
      return;
    }
    $this->_count_prescriptions = count($prescriptions);

    foreach ($prescriptions as &$prescription) {
      $this->_ref_prescriptions[$prescription->type] = $prescription;
    }
  }

  function loadRefsReglements() {
    $this->_ref_reglements = $this->factureconsult_id ?
      $this->loadRefFacture()->loadRefsReglements() :
      $this->loadBackRefs('reglements', 'date');
      
    $this->_ref_reglements_patient = array();
    $this->_ref_reglements_tiers   = array();

    foreach ($this->_ref_reglements as $_reglement) {
      $_reglement->loadRefBanque(1);
      if ($_reglement->emetteur == "patient") {
        $this->_ref_reglements_patient[$_reglement->_id] = $_reglement;
      }
      else {
        $this->_ref_reglements_tiers[$_reglement->_id] = $_reglement;
      }
    }

    // Calcul de la somme du restante du patient
    $this->_du_patient_restant = $this->du_patient;
    $this->_reglements_total_patient = 0;
    foreach ($this->_ref_reglements_patient as $_reglement) {
      $this->_du_patient_restant -= $_reglement->montant;
      $this->_reglements_total_patient += $_reglement->montant;
    }
    $this->_du_patient_restant       = round($this->_du_patient_restant, 2);
    $this->_reglements_total_patient = round($this->_reglements_total_patient, 2);

    // Calcul de la somme du restante du tiers
    $this->_du_tiers_restant = $this->du_tiers;
    $this->_reglements_total_tiers = 0;
    foreach ($this->_ref_reglements_tiers as $_reglement) {
      $this->_du_tiers_restant -= $_reglement->montant;
      $this->_reglements_total_tiers += $_reglement->montant;
    }
    $this->_du_tiers_restant       = round($this->_du_tiers_restant, 2);
    $this->_reglements_total_tiers = round($this->_reglements_total_tiers, 2);
  }

  function loadRefsBack() {
    // Backward references
    $this->loadRefsDocItems();
    $this->countDocItems();
    $this->loadRefConsultAnesth();

    $this->loadExamsComp();

    $this->loadRefsFichesExamen();
    $this->loadRefsActesCCAM();
    $this->loadRefsActesNGAP();
    $this->loadRefsReglements();
  }

  function loadExamsComp(){
    $this->_ref_examcomp = new CExamComp;
    $where = array();
    $where["consultation_id"] = "= '$this->consultation_id'";
    $order = "examen";
    $this->_ref_examcomp = $this->_ref_examcomp->loadList($where,$order);

    foreach ($this->_ref_examcomp as $keyExam => &$currExam) {
      $this->_types_examen[$currExam->realisation][$keyExam] = $currExam;
    }
  }

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

  function getPerm($permType) {
    // Délégation sur la plage
    return $this->loadRefPlageConsult()->getPerm($permType);
  }

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
      $prescription = isset($this->_ref_prescriptions["externe"]) ? $this->_ref_prescriptions["externe"] : new CPrescription();
      $prescription->type = "externe";
      $prescription->fillLimitedTemplate($template);
    }

    $sejour = $this->loadRefSejour();

    if ($sejour->_id) {
      $sejour->fillLimitedTemplate($template);
      $rpu = $sejour->loadRefRPU();
      if ($rpu && $rpu->_id) {
        $rpu->fillLimitedTemplate($template);
      }
    }

  }

  function fillLimitedTemplate(&$template) {
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

    $template->addProperty("Consultation - Fin arrêt de travail", mbDateToLocale(mbDate($this->fin_at)));
    $template->addProperty("Consultation - Prise en charge arrêt de travail", $this->getFormattedValue("pec_at"));
    $template->addProperty("Consultation - Reprise de travail", mbDateToLocale(mbDate($this->reprise_at)));
    $template->addProperty("Consultation - Accident de travail sans arrêt de travail", $this->getFormattedValue("at_sans_arret"));
    $template->addProperty("Consultation - Arrêt maladie", $this->getFormattedValue("arret_maladie"));

    $this->notify("AfterFillLimitedTemplate", $template);
  }

  function canDeleteEx() {
    // Date dépassée
    $this->loadRefPlageConsult();
    if ($this->_date < mbDate() && !$this->_ref_module->_can->admin) {
      return "Impossible de supprimer une consultation passée";
    }

    return parent::canDeleteEx();
  }

  private function adjustSejour(CSejour $sejour, $dateTimePlage) {
    if ($sejour->_id == $this->_ref_sejour->_id) {
      return;
    }

    // Journée dans lequel on déplace à déjà un séjour
    if ($sejour->_id) {
      // Affecte à la consultation le nouveau séjour
      $this->sejour_id = $sejour->_id;
      return;
    }

    // Journée qui n'a pas de séjour en cible
    $count_consultations = $this->_ref_sejour->countBackRefs("consultations");

    // On déplace les dates du séjour
    if (($count_consultations == 1) && ($this->_ref_sejour->type === "consult")) {
      $this->_ref_sejour->entree_prevue = $dateTimePlage;
      $this->_ref_sejour->sortie_prevue = mbDate($dateTimePlage)." 23:59:59";
      $this->_ref_sejour->_hour_entree_prevue = null;
      $this->_ref_sejour->_hour_sortie_prevue = null;
      if ($msg = $this->_ref_sejour->store()) {
        return $msg;
      }

      return;
    }

    // On créé le séjour de consultation
    $sejour->patient_id = $this->patient_id;
    $sejour->praticien_id = $this->_ref_chir->_id;
    $sejour->group_id = CGroups::loadCurrent()->_id;
    $sejour->type = "consult";
    $sejour->entree_prevue = $dateTimePlage;
    $sejour->sortie_prevue = mbDate($dateTimePlage)." 23:59:59";
    if ($msg = $sejour->store()) {
      return $msg;
    }
    $this->sejour_id = $sejour->_id;
  }

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

    return (mbDateTime("+ 24 HOUR", "{$this->_date} {$this->heure}") > mbDateTime());
  }

  function completeLabelFields(&$fields) {
    $this->loadRefPatient()->completeLabelFields($fields);
  }

  function canEdit() {
    if (!$this->sejour_id || CCanDo::admin() || !CAppUI::conf("dPcabinet CConsultation consult_readonly")) {
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

  function canRead() {
    if (!$this->sejour_id || CCanDo::admin()) {
      return parent::canRead();
    }
    // Tout utilisateur peut consulter une consultation de séjour en lecture seule
    return $this->_canRead = 1;
  }

  function createByDatetime($datetime, $praticien_id, $patient_id) {
    $day_now   = mbTransformTime(null, $datetime, "%Y-%m-%d");
    $time_now  = mbTransformTime(null, $datetime, "%H:%M:00");
    $hour_now  = mbTransformTime(null, $datetime, "%H:00:00");
    $hour_next = mbTime("+1 HOUR", $hour_now);

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
      if ($plageBefore->plageconsult_id) {
        if ($plageAfter->plageconsult_id) {
          $plageBefore->fin = $plageAfter->debut;
        }
        else {
          $plageBefore->fin = max($plageBefore->fin, $hour_next);
        }
        $plage = $plageBefore;
      }
      elseif ($plageAfter->plageconsult_id) {
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

    $this->plageconsult_id = $plage->plageconsult_id;
    $this->patient_id      = $patient_id;

    // Chargement de la consult avec la plageconsult && le patient
    $this->loadMatchingObject();

    if (!$this->_id) {
      $this->heure   = $time_now;
      $this->arrivee = "$day_now $time_now";
      $this->duree   = 1;
      $this->chrono  = CConsultation::TERMINE;
    }

    if ($msg = $this->store()) {
      return $msg;
    }
  }

  function createConsultAnesth(){
    $this->loadRefPlageConsult();

    if (!$this->_is_anesth || !$this->patient_id || !$this->_id) {
      return;
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
            '%I' => mbTransformTime(null, $operation->_datetime , CAppUI::conf('date')),
            '%E' => mbTransformTime(null, $sejour->entree_prevue, CAppUI::conf('date')),
            '%e' => mbTransformTime(null, $sejour->entree_prevue, CAppUI::conf('time')),
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
  }
}

?>
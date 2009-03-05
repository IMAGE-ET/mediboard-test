<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

CAppUI::requireModuleClass("dPccam", "codable");

class CConsultation extends CCodable {
  const PLANIFIE = 16;
  const PATIENT_ARRIVE = 32;
  const EN_COURS = 48;
  const TERMINE = 64;
 
  // DB Table key
  var $consultation_id = null;

  // DB References
  var $plageconsult_id = null;
  var $patient_id      = null;
  var $sejour_id       = null;
  
  // DB fields
  var $heure           = null;
  var $duree           = null;
  var $secteur1        = null;
  var $secteur2        = null;
  var $chrono          = null;
  var $annule          = null;
  
  
  var $patient_date_reglement = null;
  var $tiers_date_reglement   = null;
  
  var $motif           = null;
  var $rques           = null;
  var $examen          = null;
  var $traitement      = null;
  var $premiere        = null;
  var $adresse         = null; // Le patient a-t'il été adressé ?
  var $tarif           = null;
  
  var $arrivee         = null;
  var $categorie_id    = null;
  var $valide          = null; // Cotation validée
 
  var $total_assure    = null;
  var $total_amc       = null; 
  var $total_amo       = null;

  var $du_patient      = null; // somme que le patient doit régler
  var $du_tiers        = null;
  var $accident_travail = null;
  
  // Form fields
  var $_etat           = null;
  var $_hour           = null;
  var $_min            = null;
  var $_check_premiere = null;
  var $_check_adresse  = null;
  var $_somme          = null;
  var $_types_examen   = null;
  var $_precode_acte   = null;
  
  // Fwd References
  var $_ref_patient      = null; // Declared in CCodable
  var $_ref_plageconsult = null;
  var $_ref_sejour       = null; // Declared in CCodable
  
  
  // FSE
  var $_bind_fse       = null;
  var $_ids_fse        = null;
  var $_ext_fses       = null;
  var $_current_fse    = null;
  var $_fse_intermax   = null;

  // Tarif
  var $_bind_tarif     = null;
  var $_tarif_id       = null;
  var $_delete_actes   = null;
  
  // Back References
  var $_ref_consult_anesth     = null;
  var $_ref_examaudio          = null;
  var $_ref_examcomp           = null;
  var $_ref_examnyha           = null;
  var $_ref_exampossum         = null;
  var $_ref_examigs            = null;
  var $_count_fiches_examen    = null;
  var $_ref_reglements         = null;
  var $_ref_reglements_patient = null;
  var $_ref_reglements_tiers   = null;

  var $_ref_prescription            = null; 
  var $_ref_prescription_traitement = null;
  var $_ref_categorie               = null;
  
  // Distant fields
  var $_ref_chir                 = null;
  var $_date                     = null;
  var $_datetime                 = null;
  var $_is_anesth                = null; 
  var $_du_patient_restant       = null;
  var $_reglements_total_patient = null;
  var $_du_tiers_restant         = null;
  var $_reglements_total_tiers   = null;
  
  // Filter Fields
  var $_date_min	 	   = null;
  var $_date_max 		   = null;
  var $_prat_id 		   = null;
  var $_etat_reglement_patient = null;
  var $_etat_reglement_tiers   = null;
  var $_type_affichage     = null;
  var $_coordonnees        = null;
  var $_plages_vides       = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'consultation';
    $spec->key   = 'consultation_id';
    $spec->measureable = true;
    return $spec;
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["consult_anesth"] = "CConsultAnesth consultation_id";
    $backRefs["examaudio"]      = "CExamAudio consultation_id";
    $backRefs["examcomp"]       = "CExamComp consultation_id";
    $backRefs["examnyha"]       = "CExamNyha consultation_id";
    $backRefs["exampossum"]     = "CExamPossum consultation_id";
    $backRefs["examigs"]        = "CExamIgs consultation_id";
    $backRefs["prescriptions"]  = "CPrescription object_id";
    $backRefs["reglements"]     = "CReglement consultation_id";
    return $backRefs;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["plageconsult_id"]   = "ref notNull class|CPlageconsult";
    $specs["patient_id"]        = "ref class|CPatient";
    $specs["heure"]             = "time notNull";
    $specs["duree"]             = "numchar maxLength|1";
    $specs["secteur1"]          = "currency min|0";
    $specs["secteur2"]          = "currency";
    $specs["chrono"]            = "enum notNull list|16|32|48|64";
    $specs["annule"]            = "bool";
    
    $specs["motif"]             = "text helped";
    $specs["rques"]             = "text helped";
    $specs["examen"]            = "text helped";
    $specs["traitement"]        = "text helped";
    $specs["premiere"]          = "bool";
    $specs["adresse"]           = "bool";
    $specs["tarif"]             = "str";
    $specs["arrivee"]           = "dateTime";
    
    $specs["patient_date_reglement"] = "date";
    $specs["tiers_date_reglement"]   = "date";
    $specs["du_patient"]          = "currency";
    $specs["du_tiers"  ]            = "currency";
    $specs["_du_patient_restant"] = "currency";
    $specs["_reglements_total_patient"] = "currency";
    $specs["_reglements_total_tiers"  ] = "currency";
    $specs["_etat_reglement_patient"] = "enum list|reglee|non_reglee";
    $specs["_etat_reglement_tiers"  ] = "enum list|reglee|non_reglee";
    
    $specs["categorie_id"]      = "ref class|CConsultationCategorie";
    $specs["_date"]             = "date";
    $specs["_datetime"]         = "dateTime";
    $specs["_date_min"]         = "date";
    $specs["_date_max"] 	      = "date moreEquals|_date_min";
    $specs["_type_affichage"]   = "enum list|complete|totaux";
    $specs["_coordonnees"]      = "bool default|0";
    $specs["_plages_vides"]     = "bool default|1";
    $specs["_prat_id"]          = "";
    $specs["_check_premiere"]   = "";
    $specs["_check_adresse"]    = "";
    $specs["_somme"]            = "currency";
    $specs["valide"]            = "bool";
    $specs["total_amo"]         = "currency";
    $specs["total_amc"]         = "currency";
    $specs["total_assure"]      = "currency";
    
    $specs["sejour_id"]         = "ref class|CSejour";
    $specs["accident_travail"]  = "date";
    return $specs;
  }
  
  function getSeeks() {
    return array(
      "plageconsult_id" => "ref|CPlageconsult",
      "patient_id"      => "ref|CPatient",
      "motif"           => "like",
      "rques"           => "like",
      "examen"          => "like",
      "traitement"      => "like"
    ); 
  }
  
  function getHelpedFields(){
    return array(
      "motif"         => null,
      "rques"         => null,
      "examen"        => null,
      "traitement"    => null,
    );
  }
  
  function getEtat() {
    $etat = array();
    $etat[self::PLANIFIE]       = "Plan.";
    $etat[self::PATIENT_ARRIVE] = mbTransformTime(null, $this->arrivee, "%Hh%M");
    $etat[self::EN_COURS]       = "En cours";
    $etat[self::TERMINE]        = "Term.";
    if($this->chrono)
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
    if($this->patient_date_reglement == "0000-00-00") {
      $this->patient_date_reglement = null;
    }
    $this->du_patient = round($this->du_patient, 2);
    $this->du_tiers   = round($this->du_tiers  , 2);
    $this->_hour = intval(substr($this->heure, 0, 2));
    $this->_min  = intval(substr($this->heure, 3, 2));
    $this->_check_premiere = $this->premiere;
    $this->_check_adresse = $this->adresse;
    $this->getEtat();
    $this->_view = "Consultation ".$this->_etat;
    
    // si _coded vaut 1 alors, impossible de modifier la consultation
    $this->_coded = $this->valide;
    
    $this->loadRefsReglements();
  }
   
  function updateDBFields() {
    if (($this->_hour !== null) && ($this->_min !== null)) {
      $this->heure = $this->_hour.":".$this->_min.":00";
    }
    
    // Liaison FSE prioritaire sur l'état
    if ($this->_bind_fse) {
      $this->valide = 0;
    }
    
    // Cas du paiement d'un séjour
  	if ($this->sejour_id !== null && $this->sejour_id && $this->secteur1 !== null && $this->secteur2 !== null){
  		$this->du_tiers = $this->secteur1 + $this->secteur2;
  		$this->du_patient = 0;
  	}
  }

  function check() {
    // Data checking
    $msg = null;
    if(!$this->_id) {
      if (!$this->plageconsult_id) {
        $msg .= "Plage de consultation non valide<br />";
      }
      return $msg . parent::check();
    }
    
    $this->loadOldObject();
    $this->loadRefsReglements();
    
    // Dévalidation avec règlement déjà effectué
    if ($this->fieldModified("valide", "0")) {
      if (count($this->_ref_reglements)){
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
  	$this->loadRefPlageConsult(1);
    $this->loadRefsFichesExamen(); 
    $this->loadRefsFwd(1);
    $this->loadRefsActesCCAM();
  }

  /**
   * Chargement des identifiants des FSE associées
   * @return void
   */
  function loadIdsFSE() {
    $id_fse = new CIdSante400();
    $id_fse->setObject($this);
    $id_fse->tag = "LogicMax FSENumero";
    $id_fse = $id_fse->loadMatchingList();
    $this->_ids_fse = CMbArray::pluck($id_fse, "id400");
    
    // Chargement des FSE externes
    $fse = @new CLmFSE();
    if(!isset($fse->_spec->ds)){
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
  }
  
  /**
   * Détruit les actes CCAM et NGAP
   */  
  function deleteActes(){
    $this->_delete_actes = false;

    // Suppression des anciens actes CCAM
    $this->loadRefsActesCCAM();
    foreach ($this->_ref_actes_ccam as $acte) {
      if ($msg = $acte->delete()) {
        return $msg;
      }
    }
    $this->codes_ccam = "";
    
    // Suppression des anciens actes NGAP
    $this->loadRefsActesNGAP();
    foreach ($this->_ref_actes_ngap as $acte) { 
      if ($msg = $acte->delete()) {
        return $msg;
      }
    }
    $this->_tokens_ngap = "";
    
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
 
    // Copie des elements du tarif dans la consultation
    $this->secteur1     = $tarif->secteur1;
    $this->secteur2     = $tarif->secteur2;
    $this->du_patient     = $tarif->secteur1 + $tarif->secteur2;
    $this->tarif        = $tarif->description;
    
    // codes_ccam complet avec infos sur l'activite phase
    $codes_ccam_full = $tarif->codes_ccam;
    
    // creation du codes ccam sans les info supplementaires
    $_codes_ccam = array();
    $codes_ccam_tarif = explode("|", $tarif->codes_ccam);
    foreach($codes_ccam_tarif as $_code_ccam){
      $_codes_ccam[] = substr($_code_ccam, 0, 7);
    }
    $codes_ccam_lite = implode("|", $_codes_ccam);

    $this->codes_ccam = $codes_ccam_lite;
    if ($msg = $this->store()) {
      return $msg;
    }

    // Precodage des actes NGAP
    $this->_tokens_ngap = $tarif->codes_ngap;
    if ($msg = $this->precodeNGAP()){
      return $msg;
    }  

    $this->codes_ccam = $codes_ccam_full;
    // Precodage des actes CCAM
    if ($msg = $this->precodeCCAM()){
      return $msg;
    }  
  }
    
  /**
   * Create a LogicMaxFSE from the consult
   * Conterpart to Bind FSE
   * @return void
   */
  function makeFSE() {
    $this->_fse_intermax = array();
    
    // Ajout des actes NGAP
    $this->loadRefsActesNGAP();
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
    
    // Ajout des actes CCAM
    $this->loadRefsActesCCAM();
    foreach ($this->_ref_actes_ccam as $acte_ccam) {
	    $acteNumber = count($this->_fse_intermax)+1;
	    $ACTE = array(
        "PRE_ACTE_TYPE"     => 1,
	      "PRE_DEPASSEMENT"   => $acte_ccam->montant_depassement,
	      "PRE_CODE_CCAM"     => $acte_ccam->code_acte,
	      "PRE_CODE_ACTIVITE" => $acte_ccam->code_activite,
	      "PRE_CODE_PHASE"    => $acte_ccam->code_phase,
	      "PRE_ASSOCIATION"   => $acte_ccam->code_association,
	    );
	    
	    // Ajout des modificateurs
	    for ($i = 1; $i <= 4; $i++) {
	      $ACTE["PRE_MODIF_$i"] = @$acte_ccam->_modificateurs[$i-1];
	    }

	    $this->_fse_intermax["ACTE_$acteNumber"] = $ACTE;
    }

    // Accident de travail
    if ($this->accident_travail) {
		  $this->_fse_intermax["FSE"] = array(
		    "FSE_NATURE_ASSURANCE" => "AT",
		    "FSE_DATE_AT" => mbDateToLocale($this->accident_travail),
		  );
    }
  }
  
  /**
   * Bind a FSE to current consult
   * @return string Store-like message
   */
  function bindFSE() {
    // Prevents recursion
    $this->_bind_fse = false;
    
    if (null == $intermax = mbGetAbsValueFromPostOrSession("intermax")) {
      return;
    }
    
    // Make id externe
    $fse = $intermax["FSE"];
    $fseNumero = $fse["FSE_NUMERO_FSE"];
    $id_fse = new CIdSante400();
    $id_fse->object_class = $this->_class_name;
    $id_fse->id400 = $fseNumero;
    $id_fse->tag = "LogicMax FSENumero";
    $id_fse->loadMatchingObject();
    
    // Autre association ?
    if ($id_fse->object_id && $id_fse->object_id != $this->_id) {
      $id_fse->loadTargetObject();
      $consOther =& $id_fse->_ref_object;
      $consOther->loadRefsFwd();
      return sprintf ("FSE déjà associée à la consultation du patient %s par le Dr %s le %s",
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
    $consult->accident_travail = mbDateFromLocale($fse["FSE_DATE_AT"]);
    
    $consult->du_patient = $consult->total_assure;

    if (!in_array($fse["FSE_TIERS_PAYANT"], array("2", "3"))) {
      $consult->du_patient += $consult->total_amo;
    }
    
    if (!in_array($fse["FSE_TIERS_PAYANT"], array("3", "4"))) {
      $consult->du_patient += $consult->total_amc;
    }
    
    $consult->valide = '1';
    if (!$consult->tarif) {
      $consult->tarif = "FSE LogicMax";
    }
    
    return $consult->store();
  }

  function precodeCCAM(){
    $this->loadRefPlageConsult();
    // Explode des codes_ccam du tarif
    $listCodesCCAM = explode("|", $this->codes_ccam);
    foreach($listCodesCCAM as $key => $code){
      $acte = new CActeCCAM();
      $acte->_adapt_object = true;
        
      $acte->_preserve_montant = true;
      $acte->setCodeComplet($code);
      
      // si le code ccam est composé de 3 elements, on le precode
      if($acte->code_activite != "" && $acte->code_phase != ""){
      	// Permet de sauvegarder le montant de base de l'acte CCAM
      	$acte->_calcul_montant_base = 1;
      	
        // Mise a jour de codes_ccam suivant les _tokens_ccam du tarif
        $acte->object_id = $this->_id;
        $acte->object_class = $this->_class_name;
        $acte->executant_id = $this->_ref_chir->_id;
        $acte->execution = mbDateTime();
        if($msg = $acte->store()){
          return $msg;
        }
      }
    }
  }
  
  function precodeNGAP(){
    $listCodesNGAP = explode("|",$this->_tokens_ngap);
    foreach($listCodesNGAP as $key => $code_ngap){
      if($code_ngap) {
	      $detailCodeNGAP = explode("-", $code_ngap);
	      $acte = new CActeNGAP();
        $acte->executant_id = $this->getExecutantId();
	      $acte->_preserve_montant = true;
	      $acte->quantite            = $detailCodeNGAP[0];
	      $acte->code                = $detailCodeNGAP[1];
	      $acte->coefficient         = $detailCodeNGAP[2];
	      if(count($detailCodeNGAP) >= 4){
	        $acte->montant_base        = $detailCodeNGAP[3];
	      }
	      if(count($detailCodeNGAP) >= 5){
	        $acte->montant_depassement = str_replace("*","-",$detailCodeNGAP[4]);
	      }
	      if(count($detailCodeNGAP) >= 6){
	      	$acte->demi = $detailCodeNGAP[5];
	      }
        if(count($detailCodeNGAP) >= 7){
	      	$acte->complement = $detailCodeNGAP[6];
	      }
	      $acte->object_id = $this->_id;
	      $acte->object_class = $this->_class_name;
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
    $secteur1_NGAP = 0;
    $secteur1_CCAM = 0;
    $secteur2_NGAP = 0;
    $secteur2_CCAM = 0;
    
    // Chargement des actes NGAP
    $this->loadRefsActesNGAP();
    foreach ($this->_ref_actes_ngap as $acteNGAP) { 
      $secteur1_NGAP += $acteNGAP->montant_base;
      $secteur2_NGAP += $acteNGAP->montant_depassement;
    }
   
    // Chargement des actes CCAM
    $this->loadRefsActesCCAM();
    foreach ($this->_ref_actes_ccam as $acteCCAM) { 
      $secteur1_CCAM += $acteCCAM->montant_base;
      $secteur2_CCAM += $acteCCAM->montant_depassement;
    }
    
    // Remplissage des montant de la consultation
    $this->secteur1 = $secteur1_NGAP + $secteur1_CCAM;
    $this->secteur2 = $secteur2_NGAP + $secteur2_CCAM;
    
    return $this->store();
    
  }
  
  function store() {
    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }
    
    if ($this->_delete_actes && $this->_id){
      if($msg = $this->deleteActes()){
        return $msg;    
      }
    }
    
    // Gestion du tarif et precodage des actes
    if ($this->_bind_tarif && $this->_id){
      if($msg = $this->bindTarif()){
        return $msg;
      }
    }
        
    // Bind FSE
    if ($this->_bind_fse && $this->_id) {
      return $this->bindFSE();
    }
  }
  
  function loadRefCategorie($cache = 0) {
    $this->_ref_categorie = new CConsultationCategorie();
    if($cache) {
      $this->_ref_categorie = $this->_ref_categorie->getCached($this->categorie_id);
    } else {
      $this->_ref_categorie->load($this->categorie_id);
    }
  }
  
  function loadComplete() {
    parent::loadComplete();
    $this->_ref_patient->loadRefConstantesMedicales();
    foreach ($this->_ref_actes_ccam as &$acte_ccam) {
      $acte_ccam->loadRefsFwd();
    }
  }
  
  function loadRefPatient($cache = 0) {
    if ($this->_ref_patient) {
      return;
    }
    
    $this->_ref_patient = new CPatient;
    if($cache) {
      $this->_ref_patient = $this->_ref_patient->getCached($this->patient_id);
    } else {
      $this->_ref_patient->load($this->patient_id);
    }
  }
  
  // Chargement du sejour et du RPU dans le cas d'une urgence
  function loadRefSejour($cache = 0){
    if ($this->_ref_sejour) {
      return;
    }
    
    $this->_ref_sejour = new CSejour();
    if($cache) {
      $this->_ref_sejour = $this->_ref_sejour->getCached($this->sejour_id);
    } else {
      $this->_ref_sejour->load($this->sejour_id);
    }
    $this->_ref_sejour->loadRefRPU();
  }
  
  function getActeExecution() {
    $this->loadRefPlageConsult();
  }
  
  function loadRefPlageConsult($cache = 0) {
    if ($this->_ref_plageconsult) {
      return; 
    }

    $this->_ref_plageconsult = new CPlageconsult;
    if($cache) {
      $this->_ref_plageconsult = $this->_ref_plageconsult->getCached($this->plageconsult_id);
    } else {
      $this->_ref_plageconsult->load($this->plageconsult_id);
    }
    $this->_ref_plageconsult->loadRefsFwd($cache);
    
    // Distant fields
    $this->_ref_chir =& $this->_ref_plageconsult->_ref_chir;
    $this->_date = $this->_ref_plageconsult->date;
    $this->_datetime = mbAddDateTime($this->heure,$this->_date);
    $this->_acte_execution = $this->_datetime;
    $this->_is_anesth = $this->_ref_chir->isFromType(array("Anesthésiste"));
    $this->_praticien_id = $this->_ref_plageconsult->_ref_chir->_id;
  }
  
  function loadRefPraticien(){
  	$this->loadRefPlageConsult(1);
    $this->_ref_praticien =& $this->_ref_chir;
  }
  
  function preparePossibleActes() {
  	$this->loadRefPlageConsult();
  }
  
  function loadRefsFwd($cache = 0) {
    $this->loadRefPatient($cache);
    $this->_ref_patient->loadRefConstantesMedicales(1);
    $this->loadRefPlageConsult($cache);
    $this->_view = "Consult. de ".$this->_ref_patient->_view." par le Dr ".$this->_ref_plageconsult->_ref_chir->_view;
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
  
  function getNumDocsAndFiles($permType = null){
  	if (!$this->_nb_files_docs) {
      parent::getNumDocsAndFiles($permType);
    }
    
    if ($this->_nb_files_docs) {
      $this->getEtat();
      $this->_etat .= " ($this->_nb_files_docs Doc)";
    }
  }
  
  function getNumDocs(){
  	$this->loadRefConsultAnesth();
    if($this->_ref_consult_anesth->consultation_anesth_id) {
      $select = "count(`compte_rendu_id`) AS `total`";  
      $table  = "compte_rendu";
      $where  = array();
      $where[] = "(`object_class` = 'CConsultation' && `object_id` = '$this->consultation_id')
               || (`object_class` = 'CConsultAnesth' && `object_id` = '".$this->_ref_consult_anesth->consultation_anesth_id."')";
      $sql = new CRequest();
      $sql->addTable($table);
      $sql->addSelect($select);
      $sql->addWhere($where);
      $nbDocs = $this->_spec->ds->loadResult($sql->getRequest());
    }else{
      $nbDocs = parent::getNumDocs();
    }
    return $nbDocs;
  }

  
  function loadRefConsultAnesth() {
  	$this->_ref_consult_anesth = $this->loadUniqueBackRef("consult_anesth");
  }
  
  function loadRefsExamAudio(){
  	// Ne pas utiliser la backref => ne fonctionne pas 
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
  
  function loadRefsExamIgs(){
    $this->_ref_examigs = $this->loadUniqueBackRef("examigs");
  }
  
  function loadRefsFichesExamen() {
    $this->loadRefsExamAudio();
    $this->loadRefsExamNyha();
    $this->loadRefsExamPossum();
    $this->loadRefsExamIgs();
    $this->_count_fiches_examen = 0;
    $this->_count_fiches_examen += $this->_ref_examaudio ->_id ? 1 : 0; 
    $this->_count_fiches_examen += $this->_ref_examnyha  ->_id ? 1 : 0; 
    $this->_count_fiches_examen += $this->_ref_exampossum->_id ? 1 : 0; 
    $this->_count_fiches_examen += $this->_ref_examigs   ->_id ? 1 : 0; 
  }
  
  // Chargement des prescriptions liées à la consultation
  function loadRefsPrescriptions() {
  	$prescriptions = $this->loadBackRefs("prescriptions");
    // Cas du module non installé
    if(!is_array($prescriptions)){
      $this->_ref_prescriptions = null;
      return;
  	}
  	$this->_count_prescriptions = count($prescriptions);
  	
    foreach($prescriptions as &$prescription){
    	$this->_ref_prescriptions[$prescription->type] = $prescription;
    }
  }

  function loadRefsReglements() {
    $this->_ref_reglements = $this->loadBackRefs('reglements', 'date');
    $this->_ref_reglements_patient = array();
    $this->_ref_reglements_tiers   = array();
    
    foreach ($this->_ref_reglements as $curr_reglement) {
      if($curr_reglement->emetteur == "patient") {
        $this->_ref_reglements_patient[$curr_reglement->_id] = $curr_reglement;
      } else {
        $this->_ref_reglements_tiers[$curr_reglement->_id] = $curr_reglement;
      }
      $curr_reglement->loadRefsFwd(1);
    }
    
    // Calcul de la somme du restante du patient
    $this->_du_patient_restant = $this->du_patient;
    $this->_reglements_total_patient = 0;
    foreach ($this->_ref_reglements_patient as $curr_reglement) {
      $this->_du_patient_restant -= $curr_reglement->montant;
      $this->_reglements_total_patient += $curr_reglement->montant;
    }
    $this->_du_patient_restant       = round($this->_du_patient_restant, 2);
    $this->_reglements_total_patient = round($this->_reglements_total_patient, 2);
    
    // Calcul de la somme du restante du tiers
    $this->_du_tiers_restant = $this->du_tiers;
    $this->_reglements_total_tiers = 0;
    foreach ($this->_ref_reglements_tiers as $curr_reglement) {
      $this->_du_tiers_restant -= $curr_reglement->montant;
      $this->_reglements_total_tiers += $curr_reglement->montant;
    }
    $this->_du_tiers_restant       = round($this->_du_tiers_restant, 2);
    $this->_reglements_total_tiers = round($this->_reglements_total_tiers, 2);
  }
  
  function loadRefPrescriptionTraitement(){
    $prescription = new CPrescription();
    $prescription->type = "traitement";
    $prescription->object_id = $this->_id;
    $prescription->object_class = $this->_class_name;
    $prescription->loadMatchingObject();
  	$this->_ref_prescription_traitement = $prescription;
  }
  
 
  
  function loadRefsBack() {
    // Backward references
    $this->loadRefsFilesAndDocs();
    $this->getNumDocsAndFiles();
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
    
    foreach($this->_ref_examcomp as $keyExam => &$currExam){
      $this->_types_examen[$currExam->realisation][$keyExam] = $currExam;
    }
    
  }
  
  function getPerm($permType) {
    if (!$this->_ref_plageconsult) {
      $this->loadRefPlageConsult();
    }
    return $this->_ref_plageconsult->getPerm($permType);
  }
  
  function fillTemplate(&$template) {
  	$this->loadRefsFwd();
    $this->_ref_plageconsult->loadRefsFwd();
    $this->_ref_plageconsult->_ref_chir->fillTemplate($template);
    $this->_ref_patient->fillTemplate($template);
    $this->fillLimitedTemplate($template);
  }
  
  function fillLimitedTemplate(&$template) {
    $this->loadRefsFwd();
    $template->addDateProperty("Consultation - date"  , $this->_ref_plageconsult->date);
    $template->addTimeProperty("Consultation - heure" , $this->heure);
    $template->addProperty("Consultation - motif"     , $this->motif);
    $template->addProperty("Consultation - remarques" , $this->rques);
    $template->addProperty("Consultation - examen"    , $this->examen);
    $template->addProperty("Consultation - traitement", $this->traitement);
  }
    
  function canDeleteEx() {
    // Date dépassée
    $this->loadRefPlageConsult();
    if ($this->_ref_plageconsult->date < mbDate()) {
      return "Imposible de supprimer une consultation passée";
    }
    return parent::canDeleteEx();
  }
}

?>
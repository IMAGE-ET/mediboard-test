<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;
require_once($AppUI->getModuleClass("dPccam", "codableCCAM"));

class CConsultation extends CCodableCCAM {
  const PLANIFIE = 16;
  const PATIENT_ARRIVE = 32;
  const EN_COURS = 48;
  const TERMINE = 64;
 
  // DB Table key
  var $consultation_id = null;

  // DB References
  var $plageconsult_id = null;
  var $patient_id      = null;

  // DB fields
  var $heure         = null;
  var $duree         = null;
  var $secteur1      = null;
  var $secteur2      = null;
  var $chrono        = null;
  var $annule        = null;
  
  var $date_paiement = null;
  var $motif         = null;
  var $rques         = null;
  var $examen        = null;
  var $traitement    = null;
  var $premiere      = null;
  var $adresse       = null; // Le patient a-t'il été adressé ?
  var $tarif         = null;
  var $mode_reglement= null;
  var $arrivee       = null;
  var $banque_id     = null;
  var $categorie_id  = null;
  var $valide        = null; // facture validée ?
 
  var $total_assure      = null;
  var $total_amc         = null; 
  var $total_amo         = null;
  var $facture_acquittee = null; // true si total_facture == a_regler
  var $a_regler          = null;     // somme que le patient doit régler a la fn
  var $patient_regle     = null;     // paye devient patient_regle
  var $sejour_id         = null;
  
  
  // Form fields
  var $_etat           = null;
  var $_hour           = null;
  var $_min            = null;
  var $_check_premiere = null;
  var $_check_adresse  = null;
  var $_somme          = null;
  var $_types_examen   = null;
  var $_precode_acte   = null;
  var $_store_ngap     = null;
  // Fwd References
  var $_ref_patient      = null;
  var $_ref_plageconsult = null;
  
  // FSE
  var $_bind_fse = null;
  var $_ids_fse = null;
  var $_ext_fses = null;
  var $_current_fse = null;
  var $_fse_intermax = null;

  // Tarif
  var $_bind_tarif = null;
  var $_tarif_id = null;
  var $_delete_actes = null;
  
  // Back References
  var $_ref_consult_anesth = null;
  var $_ref_examaudio      = null;
  var $_ref_examcomp       = null;
  var $_ref_examnyha       = null;
  var $_ref_exampossum     = null;
  var $_ref_actes_ngap     = null;
  
  var $_ref_banque         = null;
  var $_ref_categorie      = null;

   // Distant fields
   var $_ref_chir  = null;
   var $_date      = null;
   var $_is_anesth = null; 
   var $_codes_ngap = null;
   var $_tokens_ngap = null; // Might be a DB field $codes_ngap as for CCAM
   
   // Filter Fields
   var $_date_min	 	= null;
   var $_date_max 		= null;
   var $_prat_id 		= null;
   var $_etat_paiement  = null;
   var $_type_affichage = null;

  function CConsultation() {
    $this->CMbObject("consultation", "consultation_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["consult_anesth"] = "CConsultAnesth consultation_id";
    $backRefs["examaudio"] = "CExamAudio consultation_id";
    $backRefs["examcomp"] = "CExamComp consultation_id";
    $backRefs["examnyha"] = "CExamNyha consultation_id";
    $backRefs["exampossum"] = "CExamPossum consultation_id";
    $backRefs["actes_ngap"] = "CActeNGAP consultation_id";
    return $backRefs;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["plageconsult_id"]   = "notNull ref class|CPlageconsult";
    $specs["patient_id"]        = "ref class|CPatient";
    $specs["heure"]             = "notNull time";
    $specs["duree"]             = "numchar maxLength|1";
    $specs["secteur1"]          = "currency min|0";
    $specs["secteur2"]          = "currency";
    $specs["chrono"]            = "notNull enum list|16|32|48|64";
    $specs["annule"]            = "bool";
    $specs["date_paiement"]     = "date";
    $specs["motif"]             = "text";
    $specs["rques"]             = "text";
    $specs["examen"]            = "text";
    $specs["traitement"]        = "text";
    $specs["premiere"]          = "bool";
    $specs["adresse"]           = "bool";
    $specs["tarif"]             = "str";
    $specs["arrivee"]           = "dateTime";
    $specs["mode_reglement"]    = "enum list|cheque|CB|especes|tiers|autre default|cheque";
    $specs["banque_id"]         = "ref class|CBanque";
    $specs["categorie_id"]      = "ref class|CConsultationCategorie";
    $specs["_date_min"]         = "date";
    $specs["_date_max"] 	      = "date moreEquals|_date_min";
    $specs["_etat_paiement"]    = "enum list|paye|impaye default|paye";
    $specs["_type_affichage"]   = "enum list|complete|totaux";
    $specs["_prat_id"]          = "text";
    $specs["_somme"]            = "currency";
    $specs["valide"]            = "bool";
    $specs["total_amo"]         = "currency";
    $specs["total_amc"]         = "currency";
    $specs["total_assure"]      = "currency";
    $specs["facture_acquittee"] = "bool";
    $specs["a_regler"]          = "currency";
    $specs["patient_regle"]     = "bool";
    $specs["sejour_id"]         = "ref class|CSejour";
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
    $etat[self::PATIENT_ARRIVE] = mbTranformTime(null, $this->arrivee, "%Hh%M");
    $etat[self::EN_COURS]       = "En cours";
    $etat[self::TERMINE]        = "Term.";
    if($this->chrono)
      $this->_etat = $etat[$this->chrono];
    if ($this->annule) {
      $this->_etat = "Ann.";
    }
  }
  
  function updateFormFields() {
    parent::updateFormFields();
  	$this->_somme = $this->secteur1 + $this->secteur2;
    if($this->date_paiement == "0000-00-00")
      $this->date_paiement = null;
    $this->_hour = intval(substr($this->heure, 0, 2));
    $this->_min  = intval(substr($this->heure, 3, 2));
    $this->_check_premiere = $this->premiere;
    $this->_check_adresse = $this->adresse;
    $this->getEtat();
    $this->_view = "Consultation ".$this->_etat;
    
    // si _coded vaut 1 alors, impossible de modifier la consultation
    $this->_coded = $this->valide;
  }
   
  function updateDBFields() {
  	if (($this->_hour !== null) && ($this->_min !== null)) {
      $this->heure = $this->_hour.":".$this->_min.":00";
    }
    
    if ($this->date_paiement == "0000-00-00") {
      $this->date_paiement = null;
    }

    // Liaison FSE prioritaire sur l'état    
    if ($this->_bind_fse) {
      $this->valide = 0;
    }
    
    
    // Gestion du tarif
    // Suppression de l'acquittement si on supprime le reglement patient
    if($this->patient_regle !== null && $this->patient_regle == 0){
      $this->facture_acquittee = 0;
    }
    
    // Acquittement à 1 si secteur1 + secteur2 = a_regler
    if($this->secteur1 !== null && $this->secteur2 !== null && $this->a_regler !== null){
      if(($this->secteur1 + $this->secteur2 == $this->a_regler) && ($this->patient_regle == 1)){
        $this->facture_acquittee = 1;
      }
    }
    
    // Si rien a regler
    if($this->a_regler !== null && $this->a_regler == 0 && $this->valide){
      $this->mode_reglement = "tiers";
      $this->patient_regle = 1;
      $this->facture_acquittee = 0;
    }
  }

  function loadRefsActesNGAP() {
    if (null === $this->_ref_actes_ngap = $this->loadBackRefs("actes_ngap")) {
      return;
    }
    
    $this->_codes_ngap = array();
    foreach ($this->_ref_actes_ngap as $_actes_ngap){
      if($_actes_ngap->montant_depassement < 0){
        $_montant_depassement_temp = str_replace("-", "*", $_actes_ngap->montant_depassement);
      } else {
        $_montant_depassement_temp = $_actes_ngap->montant_depassement;
      }
      $this->_codes_ngap[] = $_actes_ngap->quantite."-".$_actes_ngap->code."-".$_actes_ngap->coefficient."-".$_actes_ngap->montant_base."-".$_montant_depassement_temp; 
    }

    $this->_tokens_ngap = join($this->_codes_ngap, "|");
  }
  
  function check() {
    // Data checking
    $msg = null;
    if(!$this->consultation_id) {
      if (!$this->plageconsult_id) {
        $msg .= "Plage de consultation non valide<br />";
      }
      if ($this->patient_id === 0) {
        $msg .= "Patient non valide<br />";
      }
    }
    return $msg . parent::check();
  }
  
  
  function loadView() {
  	$this->loadRefPlageConsult();
    $this->loadRefsExamAudio(); 
    $this->loadRefsFwd();
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
    if(!$fse->_spec->ds){
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
    $this->valide = "";
    $this->total_assure = 0.0;
    $this->total_amc = 0.0;
    $this->total_amo = 0.0;
    $this->a_regler = 0.0;
    
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
    $this->a_regler     = $tarif->secteur1 + $tarif->secteur2;
    $this->tarif        = $tarif->description;
    $this->codes_ccam   = $tarif->codes_ccam;
    $this->_tokens_ngap = $tarif->codes_ngap;
    
    if ($msg = $this->store()) {
      return $msg;
    }

    // Precodage des actes NGAP
    if ($msg = $this->precodeNGAP()){
      return $msg;
    }  
   
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
	      "PRE_COEFFICIENT" => $acte_ngap->coefficient,
	      "PRE_QUANTITE"    => $acte_ngap->quantite,
	    );
    }
    
    // Ajout des actes CCAM
    $this->loadRefsActesCCAM();
    foreach ($this->_ref_actes_ccam as $acte_ccam) {
	    $acteNumber = count($this->_fse_intermax)+1;
	    $ACTE = array(
          "PRE_ACTE_TYPE"   => 1,
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
      return sprintf ("FSE déjà associée à la consultation du patient %s par le Dr. %s le %s",
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
        $acte->code        = $fseActe["PRE_CODE"];
        $acte->quantite    = $fseActe["PRE_QUANTITE"];
        $acte->coefficient = $fseActe["PRE_COEFFICIENT"];
        $acte->consultation_id = $this->_id;
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
    
    $consult->a_regler = $consult->total_assure;
    
    if($fse["FSE_TIERS_PAYANT"] == 0){
      $consult->a_regler += $consult->total_amo;
    }
    if($fse["FSE_TIERS_PAYANT_COMP"] == 0){
      $consult->a_regler += $consult->total_amc;
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
	      $acte->consultation_id = $this->_id;
	      if (!$acte->countMatchingList()) {
	        $acte->store();
	      }
	    }
    } 
  }
  
  function updateMontants(){
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
  
  function loadRefCategorie() {
    $this->_ref_categorie = new CConsultationCategorie();
    $this->_ref_categorie->load($this->categorie_id);
  }
  
  function loadComplete() {
    parent::loadComplete();
    foreach ($this->_ref_actes_ccam as &$acte_ccam) {
      $acte_ccam->loadRefsFwd();
    }
  }
  
  
  function loadRefPatient() {
    $this->_ref_patient = new CPatient;
    $this->_ref_patient->load($this->patient_id);
  }
  
  
  // Chargement du sejour et du RPU dans le cas d'une urgence
  function loadRefSejour(){
    $this->_ref_sejour = new CSejour();
    $this->_ref_sejour->load($this->sejour_id);
    $this->_ref_sejour->loadRefRPU();
  }
  
  
  function loadRefPlageConsult() {
    if ($this->_ref_plageconsult) {
      return; 
    }

    $this->_ref_plageconsult = new CPlageconsult;
    $this->_ref_plageconsult->load($this->plageconsult_id);
    $this->_ref_plageconsult->loadRefsFwd();
    
    // Foreign fields
    $this->_ref_chir =& $this->_ref_plageconsult->_ref_chir;
    $this->_date = $this->_ref_plageconsult->date;
    $this->_acte_execution = mbAddDateTime($this->heure,$this->_date);
    $this->_is_anesth = $this->_ref_chir->isFromType(array("Anesthésiste"));
    $this->_praticien_id = $this->_ref_plageconsult->_ref_chir->_id;
  }
  
  function loadRefPraticien(){
  	$this->loadRefPlageConsult();
  }
  
  function preparePossibleActes() {
  	$this->loadRefPlageConsult();
  }
  
  function loadRefsFwd() {
    $this->loadRefPatient();
    $this->loadRefPlageConsult();
    $this->_view = "Consult. de ".$this->_ref_patient->_view." par le Dr. ".$this->_ref_plageconsult->_ref_chir->_view;
    $this->_view .= " (".mbTranformTime(null, $this->_ref_plageconsult->date, "%d/%m/%Y").")";
    $this->loadRefsCodesCCAM();
  }

  function loadRefsDocs() {
  	$this->loadRefConsultAnesth();
    if($this->_ref_consult_anesth->consultation_anesth_id) {
      $this->_ref_documents = new CCompteRendu();
      $where = array();
      $where[] = "(`object_class` = 'CConsultation' && `object_id` = '$this->consultation_id')
               || (`object_class` = 'CConsultAnesth' && `object_id` = '".$this->_ref_consult_anesth->consultation_anesth_id."')";
      $order = "nom";
      $this->_ref_documents = $this->_ref_documents->loadList($where, $order);
      $docs_valid = count($this->_ref_documents);
    }else{
      $docs_valid = parent::loadRefsDocs();
    }
    return $docs_valid;
  }

  
  function getExecutantId($code_activite) {
  	$this->loadRefPlageConsult();
    return $this->_praticien_id;
  }
  
  
  function getNumDocsAndFiles(){
  	if(!$this->_nb_files_docs){
      parent::getNumDocsAndFiles();
    }
    if($this->_nb_files_docs) {
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
  
  function loadRefBanque(){
  	$this->_ref_banque = new CBanque();
  	$this->_ref_banque->load($this->banque_id);	
  }
  
  function loadRefConsultAnesth() {
   
  	$this->_ref_consult_anesth = new CConsultAnesth;
    $where = array();
    $where["consultation_id"] = "= '$this->consultation_id'";
    $this->_ref_consult_anesth->loadObject($where);
  }
  
  function loadRefsExamAudio(){
  	 $this->_ref_examaudio = new CExamAudio;
    $where = array();
    $where["consultation_id"] = "= '$this->consultation_id'";
    $this->_ref_examaudio->loadObject($where);
  }
  
  function loadRefsExamNyha(){
    $this->_ref_examnyha = new CExamNyha;
    $where = array();
    $where["consultation_id"] = "= '$this->consultation_id'";
    $this->_ref_examnyha->loadObject($where);
  }
  function loadRefsExamPossum(){
    $this->_ref_exampossum = new CExamPossum;
    $where = array();
    $where["consultation_id"] = "= '$this->consultation_id'";
    $this->_ref_exampossum->loadObject($where);
  }
  
  function loadRefsBack() {
    // Backward references
    $this->loadRefsFilesAndDocs();
    $this->getNumDocsAndFiles();
    $this->loadRefConsultAnesth();
    
    $this->loadRefsExamAudio();
    $this->loadExamsComp();
    $this->loadRefsExamNyha();
    $this->loadRefsExamPossum();
    $this->loadRefsActesCCAM();
    $this->loadRefsActesNGAP();
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
    $template->addProperty("Consultation - date"      , mbTranformTime("+0 DAY", $this->_ref_plageconsult->date, "%d / %m / %Y") );
    $template->addProperty("Consultation - heure"     , $this->heure);
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
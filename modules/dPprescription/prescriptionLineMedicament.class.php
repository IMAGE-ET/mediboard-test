<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Romain Ollivier
 */

/**
 * The CPrescription class
 */
class CPrescriptionLineMedicament extends CPrescriptionLine {
  // DB Table key
  var $prescription_line_medicament_id = null;
  
  // DB Fields
  var $code_cip         = null;
  var $no_poso          = null;
  var $commentaire      = null;

  var $valide_pharma    = null; 
  var $accord_praticien = null;
  // Substitution sous forme d'historique
  var $substitution_line_id = null;
  // Alternative entre plusieurs lignes
  var $substitute_for      = null; 
  var $substitution_active = null; 
  var $voie = null;
                
  //static $perfusables = array ("Voie intraveineuse","Voie intramusculaire");
  
  static $voies = array("Voie systémique"                 => array("injectable" => false, "perfusable" => false), 
                        "Voie endocervicale"              => array("injectable" => false, "perfusable" => false), 
                        "Voie péridurale"                 => array("injectable" => false, "perfusable" => false),
                        "Voie extra-amniotique"           => array("injectable" => false, "perfusable" => false),
                        "Voie gastro-entérale"            => array("injectable" => false, "perfusable" => false),
                        "Hémodialyse"                     => array("injectable" => false, "perfusable" => false),
                        "Voie intra-amniotique"           => array("injectable" => false, "perfusable" => false), 
                        "Voie intra-artérielle"           => array("injectable" => false, "perfusable" => false),
                        "Voie intra-articulaire"          => array("injectable" => false, "perfusable" => false),
                        "Voie intrabursale"               => array("injectable" => false, "perfusable" => false),
                        "Voie intracardiaque"             => array("injectable" => false, "perfusable" => false),
                        "Voie intracaverneuse"            => array("injectable" => false, "perfusable" => false),
                        "Voie intracervicale"             => array("injectable" => false, "perfusable" => false),
                        "Voie intracoronaire"             => array("injectable" => false, "perfusable" => false),
                        "Voie intradermique"              => array("injectable" => false, "perfusable" => false),
                        "Voie intradiscale"               => array("injectable" => false, "perfusable" => false),
                        "Voie intralymphatique"           => array("injectable" => false, "perfusable" => false),
                        "Voie intramusculaire"            => array("injectable" => true, "perfusable" => true),
                        "Voie intra-oculaire"             => array("injectable" => false, "perfusable" => false),
                        "Voie intrapéritonéale"           => array("injectable" => false, "perfusable" => false),
                        "Voie intrapleurale"              => array("injectable" => false, "perfusable" => false),
                        "Voie intrasternale"              => array("injectable" => false, "perfusable" => false),
                        "Voie intrarachidienne"           => array("injectable" => false, "perfusable" => false),
                        "Voie intraveineuse"              => array("injectable" => true, "perfusable" => true),
                        "Voie intravésicale"              => array("injectable" => false, "perfusable" => false),
                        "Voie nasale"                     => array("injectable" => false, "perfusable" => false),
                        "Voie orale"                      => array("injectable" => false, "perfusable" => false),
                        "Voie buccale"                    => array("injectable" => false, "perfusable" => false),
                        "Voie péri-articulaire"           => array("injectable" => false, "perfusable" => false),
                        "Voie périneurale"                => array("injectable" => false, "perfusable" => false),
                        "Voie rectale"                    => array("injectable" => false, "perfusable" => false),
                        "Voie sous-conjonctivale"         => array("injectable" => false, "perfusable" => false),
                        "Voie sous-cutanée"               => array("injectable" => false, "perfusable" => false),
                        "Voie transdermique"              => array("injectable" => false, "perfusable" => false),
                        "Voie intravasculaire"            => array("injectable" => false, "perfusable" => false),
                        "Voie parentérale"                => array("injectable" => false, "perfusable" => false),
                        "Voie intrabuccale"               => array("injectable" => false, "perfusable" => false),
                        "Voie intrapéricardique"          => array("injectable" => false, "perfusable" => false),
                        "Voie inhalée"                    => array("injectable" => false, "perfusable" => false),
                        "Voie sublinguale"                => array("injectable" => false, "perfusable" => false),
                        "Voie endobuccale"                => array("injectable" => false, "perfusable" => false),
                        "Voie sous-arachnoïdienne"        => array("injectable" => false, "perfusable" => false),
                        "Voie endotrachéopulmonaire"      => array("injectable" => false, "perfusable" => false),
                        "Voie endonasale"                 => array("injectable" => false, "perfusable" => false),
                        "Voie intravitréenne"             => array("injectable" => false, "perfusable" => false),
                        "Voie intra-artérielle hépatique" => array("injectable" => false, "perfusable" => false),
                        "Voie topique"                    => array("injectable" => false, "perfusable" => false),
                        "Voie auriculaire"                => array("injectable" => false, "perfusable" => false),
                        "Voie intra-osseuse"              => array("injectable" => false, "perfusable" => false),
                        "Voie cutanée"                    => array("injectable" => false, "perfusable" => false),
                        "Voie dentaire"                   => array("injectable" => false, "perfusable" => false),
                        "Voie endosinusale"               => array("injectable" => false, "perfusable" => false),
                        "Voie endotrachéobronchique"      => array("injectable" => false, "perfusable" => false),
                        "Voie gingivale"                  => array("injectable" => false, "perfusable" => false),
                        "Voie intralésionelle"            => array("injectable" => false, "perfusable" => false),
                        "Voie intra-utérine"              => array("injectable" => false, "perfusable" => false),
                        "Voie respiratoire"               => array("injectable" => false, "perfusable" => false),
                        "Voie urétrale"                   => array("injectable" => false, "perfusable" => false),
                        "Voie vaginale"                   => array("injectable" => false, "perfusable" => false),
                        "Voie ophtalmique"                => array("injectable" => false, "perfusable" => false),
                        "Voie intrathécale"               => array("injectable" => false, "perfusable" => false),
                        "Voie intraventriculaire"         => array("injectable" => false, "perfusable" => false),
                        "Voie intracavitaire"             => array("injectable" => false, "perfusable" => false));
	
  // Form Field
  var $_unites_prise    = null;
  var $_specif_prise    = null;
  var $_traitement      = null;
  var $_count_substitution_lines = null;
  var $_ucd_view        = null;
  var $_is_perfusable   = null;
  var $_is_injectable   = null;
  
  // Object References
  var $_ref_prescription = null;
  var $_ref_produit      = null;
  var $_ref_posologie    = null;
  var $_ref_prescription_traitement = null;
  var $_ref_substitution_lines = null;
  
  // Alertes
  var $_ref_alertes      = null;
  var $_ref_alertes_text = null;
  var $_nb_alertes       = null;

  // Behaviour field
  var $_delete_prises = null;
  
  // Logs
  var $_ref_log_validation_pharma = null;
  
  // Can fields
  var $_can_select_equivalent              = null;
  var $_can_view_historique                = null;
  var $_can_view_form_ald                  = null;
  var $_can_view_form_conditionnel = null;
  var $_can_vw_form_traitement             = null;
  var $_can_view_signature_praticien       = null;
  var $_can_view_form_signature_praticien  = null;
  var $_can_view_form_signature_infirmiere = null;
  var $_can_vw_livret_therapeutique        = null;
  var $_can_vw_hospi                       = null;
  var $_can_vw_generique                   = null;
  var $_can_modify_poso                    = null;
  var $_can_delete_line                    = null;
  var $_can_vw_form_add_line_contigue      = null;
  var $_can_modify_dates                   = null;
  var $_can_modify_comment                 = null;
  var $_quantites                          = null;
  
  var $_unite_administration               = null;
  var $_unite_dispensation                 = null;
  var $_ratio_administration_dispensation  = null;
  var $_quantite_administration = null;
  var $_quantite_dispensation = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prescription_line_medicament';
    $spec->key   = 'prescription_line_medicament_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["code_cip"]             = "notNull numchar length|7";
    $specs["no_poso"]              = "num max|128";
    $specs["commentaire"]          = "str";
    $specs["valide_pharma"]        = "bool";
    $specs["accord_praticien"]     = "bool";
    $specs["substitution_line_id"] = "ref class|CPrescriptionLineMedicament";
    $specs["substitute_for"]       = "ref class|CPrescriptionLineMedicament cascade";
    $specs["substitution_active"]  = "bool";
    $specs["_unite_prise"]         = "str";
    $specs["_traitement"]          = "bool";
    $specs["voie"]                 = "notNull str";
    return $specs;
  }
  
  function loadView() {
    $this->loadRefsPrises();
    $this->loadRefsTransmissions();
  }
  
  /*
   * Déclaration des backRefs
   */
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["prev_hist_line"] = "CPrescriptionLineMedicament substitution_line_id";
    $backRefs["substitutions"]  = "CPrescriptionLineMedicament substitute_for";
    return $backRefs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();   
 
    $this->_nb_alertes = 0;
    $this->_view = $this->_ref_produit->libelle;
    $this->_commercial_view = $this->_ref_produit->nom_commercial;
    $this->_ucd_view = substr($this->_ref_produit->libelle, 0, strrpos($this->_ref_produit->libelle, ' ')+1);
    
    $this->_duree_prise = "";
    
    if ($this->fin){
    	$this->_duree_prise .= "Jusqu'au ".mbTransformTime(null, $this->fin, "%d/%m/%Y");
    }
    else {
	    if ($this->debut){
	      $this->_duree_prise .= "à partir du ".mbTransformTime(null, $this->debut, "%d/%m/%Y");
	    }
	    if ($this->duree && $this->unite_duree){
	    	$this->_duree_prise .= " pendant ".$this->duree." ".CAppUI::tr("CPrescriptionLineMedicament.unite_duree.".$this->unite_duree);
	    }
    }
    if($this->_ref_prescription->type === "traitement"){
    	$this->_traitement = "1";
    }

    // Calcul de la fin reelle de la ligne
    $time_fin = ($this->time_fin) ? $this->time_fin : "23:59:00";
    if(!$this->_traitement){
      $this->_fin_reelle = $this->_fin ? "$this->_fin $time_fin" : "";    	
    }
    if($this->date_arret){
    	$this->_fin_reelle = $this->date_arret;
      $this->_fin_reelle .= $this->time_arret ? " $this->time_arret" : " 23:59:00";
    }
    
    if($this->_protocole){
      $this->countSubstitionsLines();
    }
    $this->isPerfusable();
    $this->isInjectable();
  }
  
  /*
   * Calcul des droits
   */
  function getAdvancedPerms($is_praticien = 0, $prescription_type = "", $mode_protocole = 0, $mode_pharma = 0) {
  	
  	/*
  	 * Une infirmiere peut remplir entierement une ligne si elle l'a créée.
  	 * Une fois que la ligne est validé par la praticien ou par le pharmacien, l'infirmiere ne peut plus y toucher
  	 */
  	
		global $AppUI, $can;
		
		$perm_infirmiere = $this->creator_id == $AppUI->user_id && 
                       !$this->signee && 
                       !$this->valide_infirmiere && 
                       !$this->valide_pharma;
    
		
 
    // Cas d'une ligne de protocole  
    if($this->_protocole){
      $protocole =& $this->_ref_prescription;
      if($protocole->praticien_id){
        $protocole->loadRefPraticien();
        $perm_edit = $protocole->_ref_praticien->canEdit();    
      } elseif($protocole->function_id){
        $protocole->loadRefFunction();
        $perm_edit = $protocole->_ref_function->canEdit();
      } elseif($protocole->group_id){
        $protocole->loadRefGroup();
        $perm_edit = $protocole->_ref_group->canEdit();
      }
    } else {
      $perm_edit = ($can->admin && !$mode_pharma) || ((!$this->signee || $mode_pharma) && 
                   !$this->valide_pharma && 
                   ($this->praticien_id == $AppUI->user_id  || $perm_infirmiere || $is_praticien || $mode_pharma));
    }
    
    $this->_perm_edit = $perm_edit;
    
    
    // Modification des dates et des commentaires
    if($perm_edit){
    	$this->_can_modify_dates = 1;
    	$this->_can_modify_comment = 1;
    }
    // Select equivalent
    if($perm_edit && !$this->_protocole){
    	$this->_can_select_equivalent = 1;
    }
		// Affichage de l'icone d'historique
		if (!$this->_protocole && $this->_count_parent_line){
			$this->_can_view_historique = 1;
		}
    // View ALD
    if($perm_edit && !$this->_protocole){
    	$this->_can_view_form_ald = 1;
    }
    // View Conditionnel
    if($perm_edit && !($this->_protocole && $this->substitute_for)){
    	$this->_can_view_form_conditionnel = 1;
    }
    // View formulaire traitement
    if($perm_edit && !$mode_pharma && !$this->_protocole){
    	$this->_can_vw_form_traitement = 1;
    }
    // View signature praticien
    if(!$this->_protocole){
    	$this->_can_view_signature_praticien = 1;
    }
    // Affichage du formulaire de signature praticien
    if(!$this->_protocole && $is_praticien && ($this->praticien_id == $AppUI->user_id)){
    	$this->_can_view_form_signature_praticien = 1;
    }
    // Affichage du formulaire de signature infirmiere
    if(!$this->_protocole && !$is_praticien && !$this->signee && $this->creator_id == $AppUI->user_id && !$this->valide_pharma && $this->_ref_prescription->type !== "externe"){
    	$this->_can_view_form_signature_infirmiere = 1;
    }
    // Affichage de l'icone Livret Therapeutique
    if(!$this->_ref_produit->inLivret && ($prescription_type === "sejour" || $this->_protocole)){
      $this->_can_vw_livret_therapeutique = 1;
    }
    // Affichage de l'icone Produit Hospitalier
    if(!$this->_ref_produit->hospitalier && ($prescription_type === "sortie" || $this->_protocole)){
      $this->_can_vw_hospi = 1;
    }
    // Affichage de l'icone generique
    if($this->_ref_produit->_generique){
      $this->_can_vw_generique = 1;
    }
    // Modification de la posologie
    if($perm_edit){
    	$this->_can_modify_poso = 1;
    	// On ne peut modifier une ligne de traitement personnel seulement en pre_admission
      if($this->_traitement && ($prescription_type !== "pre_admission")){
      	$this->_can_modify_poso = 0;
      }
    }
    // Suppression de la ligne
    if ($perm_edit || $this->_protocole){
      $this->_can_delete_line = 1;
  	}
  	// Affichage du bouton "Modifier une ligne"
  	if(!$this->_protocole && $this->_ref_prescription->type !== "externe"){
  		$this->_can_vw_form_add_line_contigue = 1;
  	}
	}
  
  /*
   * Store-like function, suppression des prises de la ligne
   */
  function deletePrises(){
  	$this->_delete_prises = 0;
  	// Chargement des prises 
    $this->loadRefsPrises();
    // Parcours des suppression des prises
    foreach($this->_ref_prises as &$_prise){
      if($msg = $_prise->delete()){
      	return $msg;
      }
    }
  }
  
  function store(){
    // Sauvegarde de la voie lors de la creation de la ligne
    if(!$this->_id && !$this->voie){
      $this->loadRefProduit();
      $this->voie = $this->_ref_produit->voies[0];
    }
    
    $get_guid = $this->_id ? false : true;
    
  	if($msg = parent::store()){
  		return $msg;
  	}
    
  	// On met en session le dernier guid créé
    if($get_guid){
      $_SESSION["dPprescription"]["full_line_guid"] = $this->_guid;
    }
    
  	if($this->_delete_prises){
  		if($msg = $this->deletePrises()){
  			return $msg;
  		}
  	}
  }
    
  /*
   * Calcul des quantite de medicaments à fournir pour les dates indiquées
   */
  function calculQuantiteLine($date_min, $date_max){	
  	$borne_min = ($this->_debut_reel > $date_min) ? $this->_debut_reel : $date_min;
  	$borne_max = ($this->_fin_reelle < $date_max) ? $this->_fin_reelle : $date_max;
  	if(!$this->_ref_prises){
  		$this->loadRefsPrises();
  	}
  	foreach($this->_ref_prises as &$_prise){
  	  $_prise->calculQuantitePrise($borne_min, $borne_max);
  	}
  	if(count($this->_ref_prises) < 1){
  		$this->_quantites = array();
  	}
  }

  function loadRefsFwd() {
  	parent::loadRefsFwd();
    $this->loadRefProduit();
    $this->loadPosologie();

    if ($this->_ref_produit->libelle_presentation){
      $this->_unites_prise[] = $this->_ref_produit->libelle_presentation;
    }

    foreach($this->_ref_produit->_ref_posologies as $_poso){
      $unite = $_poso->_code_unite_prise["LIBELLE_UNITE_DE_PRISE_PLURIEL"];
      if($_poso->p_kg) {
        // On ajoute la poso avec les /kg
        $this->_unites_prise[] = "$unite/kg";
      }
    	$this->_unites_prise[] = $unite;
    }
    
    if (is_array($this->_unites_prise)){
      $this->_unites_prise = array_unique($this->_unites_prise);
    }
  }
  
  function isPerfusable(){
    foreach($this->_ref_produit->voies as $_voie){
      if(self::$voies[$_voie]["perfusable"]){
        $this->_is_perfusable = true;
        break;  
      }
    }
  }

  
  function isInjectable(){
    if(self::$voies[$this->voie]["injectable"]){
      $this->_is_injectable = true;  
    }
  }
  
  
  /*
   * Chargement du produit
   */
  function loadRefProduit(){
  	$this->_ref_produit = CBcbProduit::get($this->code_cip);
  }
  
  /*
   * Chargement de la posologie
   */
  function loadPosologie() {
    $posologie = new CBcbPosologie();
    if($this->_ref_produit->code_cip && $this->no_poso) {
      $posologie->load($this->_ref_produit->code_cip, $this->no_poso);
    }
    $this->_unite_prise = $posologie->_code_unite_prise["LIBELLE_UNITE_DE_PRISE"];
    $this->_specif_prise = $posologie->_code_prise1;
    $this->_ref_posologie = $posologie;
  }
  
  
  /*
   * Chargement de la ligne suivante (dans le cas d'une subsitution)
   */
  function loadRefNextHistLine(){
    $this->_ref_next_hist_line = new $this->_class_name;
    if($this->subsitution_line_id){
      $this->_ref_next_hist_line->_id = $this->subsitution_line_id;
      $this->_ref_next_hist_line->loadMatchingObject();
    }  
  }
  
  
  /*
   * Calcul permettant de savoir si la ligne possède un historique (substitution)
   */
  function countPrevHistLine(){
    $line = new $this->_class_name;
    $line->subsitution_line_id = $this->_id;
    $this->_count_prev_hist_line = $line->countMatchingList(); 
  }
  
  /*
   * Chargement de la ligne precedent la ligne courante
   */
  function loadRefPrevHistLine(){
  	$this->_ref_prev_hist_line = $this->loadUniqueBackRef("prev_hist_line");
  }

  /*
   * Chargement récursif des parents d'une ligne (substitution) permet d'afficher l'historique d'une ligne
   */
  function loadRefsPrevLines($lines = array()) {
    if(!array_key_exists($this->_id, $lines)){
      $lines[$this->_id] = $this;
    }
    // Chargement de la parent_line
    $this->loadRefPrevHistLine();
    if($this->_ref_prev_hist_line->_id){
      $lines[$this->_ref_prev_hist_line->_id] = $this->_ref_prev_hist_line;
      return $this->_ref_prev_hist_line->loadRefsPrevLines($lines);
    } else {
      return $lines;
    }
  }
  
  /*
   * Chargement des lignes de substitution possibles
   */
  function loadRefsSubstitutionLines(){
    $this->_ref_substitution_lines = $this->loadBackRefs("substitutions"); 
  }
  
  /*
   * Permet de connaitre le nombre de lignes de substitutions possibles
   */
  function countSubstitionsLines(){
    $this->_count_substitution_lines = $this->countBackRefs("substitutions");
  }
  
  function delete(){
    // Chargement de la substitution_line de l'objet à supprimer
    $line = new $this->_class_name;
    $line->substitution_line_id = $this->_id;
    $line->loadMatchingObject();
    if($line->_id){
      if($msg = $line->delete()){
        return $msg;
      }
    }
    // Suppression de la ligne
    if($msg = parent::delete()){
      return $msg;
    }
  }
   
  /*
   * Chargement du log de validation par le pharmacien
   */
  function loadRefLogValidationPharma(){
    $this->_ref_log_validation_pharma = $this->loadLastLogForField("valide_pharma");
  }
}

?>
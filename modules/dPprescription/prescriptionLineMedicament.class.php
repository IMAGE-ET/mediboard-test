<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPrescriptionLineMedicament extends CPrescriptionLine {
  // DB Table key
  var $prescription_line_medicament_id = null;
  
  // DB Fields
  var $code_cip         = null;  // Code d'Identification de Pr�sentation
  var $code_ucd         = null;  // Unit� Commune de Dispensation
  var $code_cis         = null;  // Code d'Identication de Specialite
  
  var $no_poso          = null;

  var $valide_pharma    = null; 
  var $accord_praticien = null;
  var $voie             = null;
  
  // Substitution sous forme d'historique
  var $substitution_line_id = null;
  
  // Alternative entre plusieurs lignes
  var $substitute_for_id    = null; //$substitute_for_id => id de la ligne substitu�e
  var $substitute_for_class = null;
  
  var $substitution_active  = null;    
  var $substitution_plan_soin = null;
  var $traitement_personnel = null;
  
  var $_most_used_poso = null;
  
  static $corresp_voies = array("Voie parent�rale" => array("Voie intraveineuse", "Voie intramusculaire"));
                                                            
  static $voies = array("Voie syst�mique"                 => array("injectable" => false, "perfusable" => false), 
                        "Voie endocervicale"              => array("injectable" => false, "perfusable" => false), 
                        "Voie p�ridurale"                 => array("injectable" => false, "perfusable" => false),
                        "Voie extra-amniotique"           => array("injectable" => false, "perfusable" => false),
                        "Voie gastro-ent�rale"            => array("injectable" => false, "perfusable" => false),
                        "Voie intravasculaire en h�modialyse" => array("injectable" => false, "perfusable" => false),
                        "H�modialyse"                     => array("injectable" => false, "perfusable" => false),
                        "Voie intra-amniotique"           => array("injectable" => false, "perfusable" => false), 
                        "Voie intra-art�rielle"           => array("injectable" => false, "perfusable" => false),
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
                        "Voie intrap�riton�ale"           => array("injectable" => false, "perfusable" => false),
                        "Voie intrapleurale"              => array("injectable" => false, "perfusable" => false),
                        "Voie intrasternale"              => array("injectable" => false, "perfusable" => false),
                        "Voie intrarachidienne"           => array("injectable" => false, "perfusable" => false),
                        "Voie intraveineuse"              => array("injectable" => true, "perfusable" => true),
                        "Voie intrav�sicale"              => array("injectable" => false, "perfusable" => false),
                        "Voie nasale"                     => array("injectable" => false, "perfusable" => false),
                        "Voie orale"                      => array("injectable" => false, "perfusable" => false),
                        "Voie buccale"                    => array("injectable" => false, "perfusable" => false),
                        "Voie p�ri-articulaire"           => array("injectable" => false, "perfusable" => false),
                        "Voie p�rineurale"                => array("injectable" => false, "perfusable" => false),
                        "Voie rectale"                    => array("injectable" => false, "perfusable" => false),
                        "Voie sous-conjonctivale"         => array("injectable" => false, "perfusable" => false),
                        "Voie sous-cutan�e"               => array("injectable" => false, "perfusable" => false),
                        "Voie transdermique"              => array("injectable" => false, "perfusable" => false),
                        "Voie intravasculaire"            => array("injectable" => false, "perfusable" => false),
                        "Voie parent�rale"                => array("injectable" => true, "perfusable" => true),
                        "Voie intrabuccale"               => array("injectable" => false, "perfusable" => false),
                        "Voie intrap�ricardique"          => array("injectable" => false, "perfusable" => false),
                        "Voie inhal�e"                    => array("injectable" => false, "perfusable" => false),
                        "Voie sublinguale"                => array("injectable" => false, "perfusable" => false),
                        "Voie endobuccale"                => array("injectable" => false, "perfusable" => false),
                        "Voie sous-arachno�dienne"        => array("injectable" => false, "perfusable" => false),
                        "Voie endotrach�opulmonaire"      => array("injectable" => false, "perfusable" => false),
                        "Voie endonasale"                 => array("injectable" => false, "perfusable" => false),
                        "Voie intravitr�enne"             => array("injectable" => false, "perfusable" => false),
                        "Voie intra-art�rielle h�patique" => array("injectable" => false, "perfusable" => false),
                        "Voie topique"                    => array("injectable" => false, "perfusable" => false),
                        "Voie auriculaire"                => array("injectable" => false, "perfusable" => false),
                        "Voie intra-osseuse"              => array("injectable" => false, "perfusable" => false),
                        "Voie cutan�e"                    => array("injectable" => false, "perfusable" => false),
                        "Voie dentaire"                   => array("injectable" => false, "perfusable" => false),
                        "Voie endosinusale"               => array("injectable" => false, "perfusable" => false),
                        "Voie endotrach�obronchique"      => array("injectable" => false, "perfusable" => false),
                        "Voie gingivale"                  => array("injectable" => false, "perfusable" => false),
                        "Voie intral�sionelle"            => array("injectable" => false, "perfusable" => false),
                        "Voie intra-ut�rine"              => array("injectable" => false, "perfusable" => false),
                        "Voie respiratoire"               => array("injectable" => false, "perfusable" => false),
                        "Voie ur�trale"                   => array("injectable" => false, "perfusable" => false),
                        "Voie vaginale"                   => array("injectable" => false, "perfusable" => false),
                        "Voie ophtalmique"                => array("injectable" => false, "perfusable" => false),
                        "Voie intrath�cale"               => array("injectable" => false, "perfusable" => false),
                        "Voie intraventriculaire"         => array("injectable" => false, "perfusable" => false),
                        "Voie intracavitaire"             => array("injectable" => false, "perfusable" => false));
	
  // Form Field
  var $_unites_prise    = null;
  var $_specif_prise    = null;
  var $_count_substitution_lines = null;
  var $_ucd_view        = null;
  var $_is_perfusable   = null;
  var $_is_injectable   = null;
  var $_forme_galenique = null;
  
  // Object References
  var $_ref_prescription = null;
  var $_ref_produit      = null;
  var $_ref_posologie    = null;
  var $_ref_substitution_lines = null;
  var $_ref_substitute_for = null; // ligne (med ou perf) que la ligne peut substituer
  
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
  var $_can_view_form_ald                  = null;
  var $_can_view_form_conditionnel         = null;
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
  var $_quantite_administration            = null;
  var $_quantite_dispensation              = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prescription_line_medicament';
    $spec->key   = 'prescription_line_medicament_id';
    $spec->measureable = true;
    return $spec;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["code_cip"]               = "numchar notNull length|7";
    $specs["code_ucd"]               = "numchar length|7";
    $specs["code_cis"]               = "numchar length|8";
    $specs["no_poso"]                = "num max|128";
    $specs["valide_pharma"]          = "bool";
    $specs["accord_praticien"]       = "bool";
    $specs["substitution_line_id"]   = "ref class|CPrescriptionLineMedicament";
    $specs["substitute_for_id"]      = "ref class|CMbObject meta|substitute_for_class cascade";
    $specs["substitute_for_class"]   = "enum list|CPrescriptionLineMedicament|CPerfusion default|CPrescriptionLineMedicament";
    $specs["substitution_active"]    = "bool";
    $specs["_unite_prise"]           = "str";
    $specs["voie"]                   = "str";
    $specs["substitution_plan_soin"] = "bool";
    $specs["traitement_personnel"]   = "bool";
    return $specs;
  }
  
  function loadView() {
    $this->loadRefsPrises();
    $this->loadRefsTransmissions();
  }
  
  /*
   * D�claration des backRefs
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["prev_hist_line"] = "CPrescriptionLineMedicament substitution_line_id";
    $backProps["substitutions_medicament"] = "CPrescriptionLineMedicament substitute_for_id";
    $backProps["substitutions_perfusion"]  = "CPerfusion substitute_for_id";
    $backProps["parent_line"]    = "CPrescriptionLineMedicament child_id";  
    $backProps["transmissions"]   = "CTransmissionMedicale object_id";
    $backProps["administration"]  = "CAdministration object_id";
    $backProps["prise_posologie"] = "CPrisePosologie object_id";
    return $backProps;
  }
  
  function updateFormFields() {
    parent::updateFormFields();   
 
    $this->_nb_alertes = 0;
    $this->_view = $this->_ref_produit->libelle;
    $this->_commercial_view = $this->_ref_produit->nom_commercial;
    //$this->_ucd_view = substr($this->_ref_produit->libelle, 0, strrpos($this->_ref_produit->libelle, ' ')+1);
    if($this->code_ucd){
      $this->_ucd_view = "{$this->_ref_produit->libelle_abrege} {$this->_ref_produit->dosage}";
    } else {
      $this->_ucd_view = substr($this->_ref_produit->libelle, 0, strrpos($this->_ref_produit->libelle, ' ')+1);
    }
    $this->_forme_galenique = $this->_ref_produit->forme;
    $this->_duree_prise = "";
    
    if ($this->fin){
    	$this->_duree_prise .= "Jusqu'au ".mbTransformTime(null, $this->fin, "%d/%m/%Y");
    }
    else {
	    if ($this->debut){
	      $this->_duree_prise .= "� partir du ".mbTransformTime(null, $this->debut, "%d/%m/%Y");
	    }
	    if ($this->duree && $this->unite_duree){
	    	$this->_duree_prise .= " pendant ".$this->duree." ".CAppUI::tr("CPrescriptionLineMedicament.unite_duree.".$this->unite_duree);
	    }
    }

    // Calcul de la fin reelle de la ligne
    $time_fin = ($this->time_fin) ? $this->time_fin : "23:59:00";
    $this->_fin_reelle = $this->_fin ? "$this->_fin $time_fin" : "";    	

    if($this->date_arret){
    	$this->_fin_reelle = $this->date_arret;
      $this->_fin_reelle .= $this->time_arret ? " $this->time_arret" : " 23:59:00";
    }
    
    if($this->_protocole){
      $this->countSubstitutionsLines();
    }
    $this->isPerfusable();
    $this->isInjectable();
  }
  
  /*
   * Calcul des droits
   */
  function getAdvancedPerms($is_praticien = 0, $prescription_type = "", $mode_protocole = 0, $mode_pharma = 0, $operation_id = 0) {
  	
  	/*
  	 * Une infirmiere peut remplir entierement une ligne si elle l'a cr��e.
  	 * Une fois que la ligne est valid� par la praticien ou par le pharmacien, l'infirmiere ne peut plus y toucher
  	 */
  	
		global $AppUI, $can;
	
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
                   ($this->praticien_id == $AppUI->user_id || $is_praticien || $mode_pharma || $operation_id));
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
    // View ALD
    if($perm_edit){
    	$this->_can_view_form_ald = 1;
    }
    // View Conditionnel
    if($perm_edit && !($this->_protocole && $this->substitute_for_id)){
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
  
  /*
   * Chargement des 5 posos les plus utilis�es
   */
  function loadMostUsedPoso($code_cis = "", $praticien_id = "", $type = ""){
    $temp_view = array();
    $this->_most_used_poso = array();
    $most_used_lines = $this->getMostUsedPoso($code_cis, $praticien_id, $type);
    foreach($most_used_lines as $_key => $_line){
      if(is_array($_line)){
	      $view = "";
	      $line = new CPrescriptionLineMedicament();
	      $line->load($_line['prescription_line_medicament_id']);
	      $line->loadRefsPrises();
	      $last_prise = end($line->_ref_prises);
	      foreach($line->_ref_prises as $_prise){
	        $view .= $_prise->_view;
	        if($_prise->_id != $last_prise->_id){
	          $view .= ", ";
	        }
	      }
	      if(!isset($temp_view[$view])){
	        $temp_view[$view] = array("occ" => "", "line_id" => "");
	      }
	      $temp_view[$view]["occ"] += $_line["count_signature"];
	      $temp_view[$view]["line_id"] = $_line['prescription_line_medicament_id'];
      }
    }

    foreach($temp_view as $curr_view => $_tab){
      $this->_most_used_poso[$_tab["line_id"]]["view"] = $curr_view;
	    $this->_most_used_poso[$_tab["line_id"]]["occ"] = $_tab["occ"];
	    $pourcentage = $_tab["occ"] ? (100 * $_tab["occ"] / $most_used_lines['total']) : "0"; 
      $this->_most_used_poso[$_tab["line_id"]]["pourcentage"] = round($pourcentage, 2);
    }
  }
  

  /*
   * Recuperation des posologies les plus utilis�es
   */
  function getMostUsedPoso($code_cis = "", $praticien_id = "", $type = ""){
    $ds = CSQLDataSource::get("std");
   
    $_code_cis = $code_cis ? $code_cis : $this->code_cis;
    $_praticien_id = $praticien_id ? $praticien_id : $this->praticien_id;
    $_type = $type ? $type : $this->_ref_prescription->type;
    
    $sql = "CREATE TEMPORARY TABLE posos AS
							SELECT prescription_line_medicament.prescription_line_medicament_id, prise_posologie.*
							FROM prise_posologie
							LEFT JOIN prescription_line_medicament ON prescription_line_medicament.prescription_line_medicament_id = prise_posologie.object_id AND prise_posologie.object_class = 'CPrescriptionLineMedicament'
							LEFT JOIN prescription ON prescription.prescription_id = prescription_line_medicament.prescription_id
							WHERE prescription_line_medicament.code_cis = '$_code_cis'";
    
              if($_praticien_id != 'global'){
               $sql .= "AND prescription_line_medicament.praticien_id = '$_praticien_id'";
              }
              
              $sql .= "AND prescription.type = '$_type'
							AND prise_posologie.decalage_intervention IS NULL
							ORDER BY moment_unitaire_id;";
    $ds->exec($sql);

		$sql = "CREATE TEMPORARY TABLE signatures AS
							SELECT prescription_line_medicament_id, CONVERT(GROUP_CONCAT(CONCAT_WS('-', quantite, nb_fois, unite_fois, nb_tous_les, unite_tous_les, decalage_prise, unite_prise, decalage_intervention, heure_prise, moment_unitaire_id ) SEPARATOR '|') USING latin1) as signature
	          	FROM posos
							GROUP BY prescription_line_medicament_id";
	  $ds->exec($sql);
   
	  
	  // GROUP_CONCAT(prescription_line_medicament_id SEPARATOR '|')
	  $sql = "SELECT signature, prescription_line_medicament_id, count(*) as count_signature 
					  FROM signatures
						GROUP BY signature
						ORDER BY count_signature DESC
						LIMIT 5;";
	  $signatures = $ds->loadList($sql);
    
	  $sql = "SELECT count(*) FROM signatures";
	  $signatures["total"] = $ds->loadResult($sql);
	  
	  $sql = "DROP TABLE posos";
	  $ds->exec($sql);
	  
	  $sql = "DROP TABLE signatures";
	  $ds->exec($sql);

	  return $signatures;
  }
  
  
  function applyPoso($poso){ 
    $line_medicament = new CPrescriptionLineMedicament();
    $line_medicament->load($poso['prescription_line_medicament_id']);
    $line_medicament->loadRefsPrises();
    foreach($line_medicament->_ref_prises as $_prise){
      $_prise->_id = '';
      $_prise->object_id = $this->_id;
      $_prise->object_class = $this->_class_name;
      $_prise->store();
    }
  }
  
  function updateDBFields(){
    parent::updateDBFields();
    
    if(!$this->_id && $this->code_cip && !$this->code_ucd){
      $produit = new CBcbProduit();
      $produit->load($this->code_cip);
      $this->code_ucd = $produit->code_ucd;
      $this->code_cis = $produit->code_cis;
    }
  }
  
  function store(){
    // Sauvegarde de la voie lors de la creation de la ligne
    if(!$this->_id && !$this->voie){
      $this->loadRefProduit();
      if(isset($this->_ref_produit->voies[0])){
        $this->voie = $this->_ref_produit->voies[0];
      }
    }
    
    $mode_creation = !$this->_id;
    
  	if($msg = parent::store()){
  		return $msg;
  	}

    // Pre-remplissage de la posologie la plus utilis�e
    if($mode_creation && $this->_most_used_poso){
      $posos = $this->getMostUsedPoso();
      if(count($posos)){
        $this->applyPoso(reset($posos));
      }
    }
    
  	// On met en session le dernier guid cr��
    if($mode_creation){
      $_SESSION["dPprescription"]["full_line_guid"] = $this->_guid;
    }
    
  	if($this->_delete_prises){
  		if($msg = $this->deletePrises()){
  			return $msg;
  		}
  	}
  }
    
  /*
   * Calcul des quantite de medicaments � fournir pour les dates indiqu�es
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
    if($this->_ref_produit->voies){
	    foreach($this->_ref_produit->voies as $_voie){
	      if(self::$voies[$_voie]["perfusable"]){
	        $this->_is_perfusable = true;
	        break;  
	      }
	    }
    }
  }

  
  function isInjectable(){
    if($this->voie){
	    if(self::$voies[$this->voie]["injectable"]){
	      $this->_is_injectable = true;  
	    }
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
   * Calcul permettant de savoir si la ligne poss�de un historique (substitution)
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
   * Chargement r�cursif des parents d'une ligne (substitution) permet d'afficher l'historique d'une ligne
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
    if(!$this->substitute_for_id){
		  $this->_ref_substitution_lines["CPrescriptionLineMedicament"] = $this->loadBackRefs("substitutions_medicament"); 
      $this->_ref_substitution_lines["CPerfusion"] = $this->loadBackRefs("substitutions_perfusion");  
      $this->_ref_substitute_for = $this;
    } else {
	    $_base_line = new $this->substitute_for_class;
		  $_base_line->load($this->substitute_for_id);
		  $_base_line->loadRefsSubstitutionLines();
	    $this->_ref_substitution_lines = $_base_line->_ref_substitution_lines;
	    $this->_ref_substitution_lines[$_base_line->_class_name][$_base_line->_id] = $_base_line;
			unset($this->_ref_substitution_lines[$this->_class_name][$this->_id]);		
		  $this->_ref_substitute_for = $_base_line;			  
	  }
  }
  
  /*
   * Permet de connaitre le nombre de lignes de substitutions possibles
   */
  function countSubstitutionsLines(){
    if(!$this->substitute_for_id){
      $this->_count_substitution_lines = $this->countBackRefs("substitutions_medicament") + $this->countBackRefs("substitutions_perfusion");
    } else {
      $object = new $this->substitute_for_class;
      $object->load($this->substitute_for_id);
      $object->countSubstitutionsLines();
      $this->_count_substitution_lines = $object->_count_substitution_lines;    
    }
  }
  
  function delete(){
    // Chargement de la substitution_line de l'objet � supprimer
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
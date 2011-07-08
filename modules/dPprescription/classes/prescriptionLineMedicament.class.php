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
  var $code_cip         = null;  // Code d'Identification de Présentation
  var $code_ucd         = null;  // Unité Commune de Dispensation
  var $code_cis         = null;  // Code d'Identication de Specialite
  
  var $no_poso          = null;

  var $valide_pharma    = null; 
  var $accord_praticien = null;
  var $voie             = null;
  var $commentaire         = null;
  
  // Substitution sous forme d'historique
  var $substitution_line_id = null;
  
  // Alternative entre plusieurs lignes
  var $substitute_for_id    = null; //$substitute_for_id => id de la ligne substituée
  var $substitute_for_class = null;
  
  var $substitution_active  = null;    
  var $substitution_plan_soin = null;
  var $traitement_personnel = null;
	var $injection_ide = null;
	var $stupefiant = null;
  
  var $_most_used_poso = null;
  var $_debut_reel = null;
	var $_fin_reelle = null;
  
  static $corresp_voies = array("Voie parentérale" => array("Voie intraveineuse", "Voie intramusculaire"));
                                                            
  static $voies = array("Voie systémique"                 => array("injectable" => false, "perfusable" => false), 
                        "Voie endocervicale"              => array("injectable" => false, "perfusable" => false), 
                        "Voie péridurale"                 => array("injectable" => true, "perfusable" => true),
                        "Voie extra-amniotique"           => array("injectable" => false, "perfusable" => false),
                        "Voie gastro-entérale"            => array("injectable" => false, "perfusable" => false),
                        "Voie intravasculaire en hémodialyse" => array("injectable" => false, "perfusable" => false),
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
                        "Voie intramusculaire"            => array("injectable" => true, "perfusable" => false),
                        "Voie intra-oculaire"             => array("injectable" => true, "perfusable" => true),
                        "Voie intrapéritonéale"           => array("injectable" => false, "perfusable" => false),
                        "Voie intrapleurale"              => array("injectable" => false, "perfusable" => false),
                        "Voie intrasternale"              => array("injectable" => false, "perfusable" => false),
                        "Voie intrarachidienne"           => array("injectable" => true, "perfusable" => false),
                        "Voie intraveineuse"              => array("injectable" => true, "perfusable" => true),
                        "Voie intravésicale"              => array("injectable" => false, "perfusable" => false),
                        "Voie nasale"                     => array("injectable" => false, "perfusable" => false),
                        "Voie orale"                      => array("injectable" => false, "perfusable" => false),
                        "Voie buccale"                    => array("injectable" => false, "perfusable" => false),
                        "Voie péri-articulaire"           => array("injectable" => false, "perfusable" => false),
                        "Voie périneurale"                => array("injectable" => true, "perfusable" => true),
                        "Voie rectale"                    => array("injectable" => false, "perfusable" => false),
                        "Voie sous-conjonctivale"         => array("injectable" => false, "perfusable" => false),
                        "Voie sous-cutanée"               => array("injectable" => true, "perfusable" => false),
                        "Voie transdermique"              => array("injectable" => false, "perfusable" => false),
                        "Voie intravasculaire"            => array("injectable" => true, "perfusable" => true),
                        "Voie parentérale"                => array("injectable" => true, "perfusable" => true),
                        "Voie intrabuccale"               => array("injectable" => false, "perfusable" => false),
                        "Voie intrapéricardique"          => array("injectable" => false, "perfusable" => false),
                        "Voie inhalée"                    => array("injectable" => false, "perfusable" => false),
                        "Voie sublinguale"                => array("injectable" => false, "perfusable" => false),
                        "Voie endobuccale"                => array("injectable" => false, "perfusable" => false),
                        "Voie sous-arachnoïdienne"        => array("injectable" => false, "perfusable" => false),
                        "Voie endotrachéopulmonaire"      => array("injectable" => false, "perfusable" => false),
                        "Voie endonasale"                 => array("injectable" => false, "perfusable" => false),
                        "Voie intravitréenne"             => array("injectable" => true, "perfusable" => false),
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
                        "Voie intrathécale"               => array("injectable" => true, "perfusable" => false),
                        "Voie intraventriculaire"         => array("injectable" => false, "perfusable" => false),
                        "Voie intracavitaire"             => array("injectable" => false, "perfusable" => false));
	
  // Form Field
  var $_unites_prise    = null;
  var $_specif_prise    = null;
  var $_count_substitution_lines = null;
  var $_ucd_view        = null;
  var $_dci_view        = null;
  var $_is_perfusable   = null;
  var $_is_injectable   = null;
  var $_forme_galenique = null;
  
  // Object References
  var $_ref_prescription = null;
  var $_ref_produit      = null;
  var $_ref_posologie    = null;
  var $_ref_substitution_lines = null;
  var $_ref_substitute_for = null; // ligne (med ou perf) que la ligne peut substituer
  var $_ref_produit_prescription = null;
	
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
  var $_unite_prise                        = null;
  var $_long_view = null;
  var $_duree                              = null;

  static $_load_lite = false;


  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prescription_line_medicament';
    $spec->key   = 'prescription_line_medicament_id';
    $spec->measureable = true;
    $spec->events = array(
      "prescription"   => array(
        "multiple"   => false,
        "reference1" => array("CSejour",  "prescription_id.object_id"),
        "reference2" => array("CPatient", "prescription_id.object_id.patient_id"),
      ),
      "signature"      => array(
        "multiple"   => false,
        "reference1" => array("CSejour",  "prescription_id.object_id"),
        "reference2" => array("CPatient", "prescription_id.object_id.patient_id"),
      ),
      "administration" => array(
        "multiple"   => true,
        "reference1" => array("CSejour",  "prescription_id.object_id"),
        "reference2" => array("CPatient", "prescription_id.object_id.patient_id"),
      ),
    );
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
    $specs["substitution_line_id"]   = "ref class|CPrescriptionLineMedicament show|0";
    $specs["substitute_for_id"]      = "ref class|CMbObject meta|substitute_for_class cascade show|0";
    $specs["substitute_for_class"]   = "enum list|CPrescriptionLineMedicament|CPrescriptionLineMix default|CPrescriptionLineMedicament show|0";
    $specs["substitution_active"]    = "bool";
    $specs["_unite_prise"]           = "str";
    $specs["voie"]                   = "str";
    $specs["substitution_plan_soin"] = "bool show|0";
    $specs["traitement_personnel"]   = "bool";
    $specs["injection_ide"]          = "bool";
    $specs["stupefiant"]             = "bool default|0";
    $specs["_ucd_view"]              = "str";
    $specs["_dci_view"]              = "str";
    $specs["_fin_reelle"]            = "dateTime";
    $specs["_debut_reel"]            = "dateTime";
    $specs["_duree"]                 = "num min|0";
		$specs["commentaire"]            = "text helped|code_ucd";
    return $specs;
  }
  
  function loadView() {
    $this->loadRefsPrises();
    $this->loadRefsTransmissions();
    $this->loadRefPraticien();
    $this->loadRefPrescription();
    $this->_ref_prescription->loadRefPatient();
    $this->_ref_prescription->loadRefObject();
  }
  
  /*
   * Déclaration des backRefs
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["prev_hist_line"] = "CPrescriptionLineMedicament substitution_line_id";
    $backProps["substitutions_medicament"] = "CPrescriptionLineMedicament substitute_for_id";
    $backProps["substitutions_prescription_line_mix"]  = "CPrescriptionLineMix substitute_for_id";
    $backProps["parent_line"]    = "CPrescriptionLineMedicament child_id";  
    $backProps["transmissions"]   = "CTransmissionMedicale object_id";
    $backProps["administration"]  = "CAdministration object_id";
    $backProps["prise_posologie"] = "CPrisePosologie object_id";
		$backProps["planifications"]  = "CPlanificationSysteme object_id";
		$backProps["prises_dispensation"] = "CPriseDispensation object_id";
    return $backProps;
  }
  
  function updateFormFields() {
    parent::updateFormFields();   

    $this->loadRefProduit();
    
		
    if(self::$_load_lite) {
      return;
    }
		
    $this->_nb_alertes = 0;
		
		$this->_dci_view = $this->_ref_produit->loadDCIViewFromProduit();
		
		$this->_view = $this->_ref_produit->libelle;
    $this->_commercial_view = $this->_ref_produit->nom_commercial;
    
		if($this->code_ucd){
      $this->_ucd_view = "{$this->_ref_produit->libelle_abrege} {$this->_ref_produit->dosage}";
    } else {
      $this->_ucd_view = substr($this->_ref_produit->libelle, 0, strrpos($this->_ref_produit->libelle, ' ')+1);
    }
		
	  if (CAppUI::conf("dPprescription CPrescription use_libelle_livret")){
			$this->_ref_produit->updateLibelleProduit();
			if($this->_ref_produit->_libelle_livret){
			  $this->_ucd_view = $this->_ref_produit->_libelle_livret;
			}
		}
		
    $this->_forme_galenique = $this->_ref_produit->forme;
    $this->_duree_prise = "";
    
    if ($this->fin){
    	$this->_duree_prise .= "Jusqu'au ".mbTransformTime(null, $this->fin, "%d/%m/%Y");
    }
    else {
      if($this->debut && (mbDate($this->debut) == mbDate($this->_fin))){
        $this->_duree_prise .= "le ".mbTransformTime(null, $this->debut, "%d/%m/%Y");
      } else {
		    if ($this->debut){
		      $this->_duree_prise .= "à partir du ".mbTransformTime(null, $this->debut, "%d/%m/%Y");
		    }
		    if ($this->duree && $this->unite_duree){
		    	$this->_duree_prise .= " pendant ".$this->duree." ".CAppUI::tr("CPrescriptionLineMedicament.unite_duree.".$this->unite_duree);
		    }
      }
    }

    // Calcul de la fin reelle de la ligne
    $time_fin = ($this->time_fin) ? $this->time_fin : "23:59:00";
	
    // Si l'unite de duree est l'heure
		if($this->unite_duree == "heure" || $this->unite_duree == "minute"){
			$_unite = ($this->unite_duree == "heure") ? "HOURS" : "MINUTES";
		  $this->_fin_reelle = mbDateTime("+ $this->duree $_unite", $this->_debut_reel);
    } else {
      $this->_fin_reelle = $this->_fin ? "$this->_fin $time_fin" : "";      
		}

    if($this->date_arret){
    	$this->_fin_reelle = $this->date_arret;
      $this->_fin_reelle .= $this->time_arret ? " $this->time_arret" : " 23:59:00";
    }
    
    if($this->_protocole){
      $this->countSubstitutionsLines();
    }
    $this->isPerfusable();
    $this->isInjectable();
		
		if(!$this->duree && $this->_ref_prescription->type == "sejour"){
			$this->_duree = mbDaysRelative($this->debut, mbDate($this->_ref_prescription->_ref_object->sortie))+1;
		}
  }
    
  function updateLongView(){
    $this->loadRefsPrises();
    $this->_long_view = "$this->_ucd_view, "; 
    foreach($this->_ref_prises as $_poso){
      $this->_long_view .= "$_poso->_view, ";
    }
    $this->_long_view .= $this->_duree_prise;
  }
  
  /*
   * Calcul des droits
   */
  function getAdvancedPerms($is_praticien = 0, $mode_protocole = 0, $mode_pharma = 0, $operation_id = 0) {
		global $can;
    
    $current_user = CMediusers::get();
	
    // Cas d'une ligne de protocole  
    if($this->_protocole){
      $protocole =& $this->_ref_prescription;
      if($protocole->praticien_id){
        $protocole->loadRefPraticien();
				
				$is_praticien = CAppUI::$user->isPraticien();
				$perm_edit = (!$is_praticien || ($is_praticien && $current_user->user_id == $protocole->praticien_id)) ? 1 : 0;
	      
      } elseif($protocole->function_id){
        $protocole->loadRefFunction();
        $perm_edit = $protocole->_ref_function->canEdit();
      } elseif($protocole->group_id){
        $protocole->loadRefGroup();
        $perm_edit = $protocole->_ref_group->canEdit();
      }
    } else {

      if($mode_pharma && $this->valide_pharma){
        $perm_edit = 0;
      } else {
			  $perm_edit = ($can->admin && !$mode_pharma) || 
			               ((!$this->signee || $mode_pharma) && 
                     ($this->praticien_id == $current_user->user_id || $is_praticien || $mode_pharma || $operation_id || ($current_user->isExecutantPrescription() && CAppUI::conf("dPprescription CPrescription droits_infirmiers_med") && !CAppUI::conf("dPprescription CPrescription role_propre"))));
			}
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
    //if($perm_edit && !($this->_protocole && $this->substitute_for_id)){
    if($perm_edit){
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
    if(!$this->_protocole && $is_praticien){
    	$this->_can_view_form_signature_praticien = 1;
    }
    // Affichage de l'icone Livret Therapeutique
    if(!$this->_ref_produit->inLivret && ($this->_ref_prescription->type === "sejour" || $this->_protocole)){
      $this->_can_vw_livret_therapeutique = 1;
    }
    // Affichage de l'icone Produit Hospitalier
    if(!$this->_ref_produit->hospitalier && ($this->_ref_prescription->type === "sortie" || $this->_protocole)){
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
    if ($perm_edit){
      $this->_can_delete_line = 1;
  	}
		
  	// Affichage du bouton "Modifier une ligne"
  	if(!$this->_protocole && $this->_ref_prescription->type !== "externe" && $this->signee && !(CMediusers::get()->isExecutantPrescription() && CAppUI::conf("dPprescription CPrescription role_propre"))){
  		$this->_can_vw_form_add_line_contigue = 1;
  	}
	}
  
  /*
   * Chargement des 5 posos les plus utilisées
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
				$view .= " ($line->voie)";
				
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
   * Recuperation des posologies les plus utilisées
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
              AND prise_posologie.urgence_datetime IS NULL
							ORDER BY moment_unitaire_id;";
    $ds->exec($sql);

		$sql = "CREATE TEMPORARY TABLE signatures AS
							SELECT posos.prescription_line_medicament_id, CONVERT(GROUP_CONCAT(CONCAT_WS('-', prescription_line_medicament.voie, quantite, nb_fois, unite_fois, nb_tous_les, unite_tous_les, decalage_prise, unite_prise, decalage_intervention, heure_prise, moment_unitaire_id ) SEPARATOR '|') USING latin1) as signature
	          	FROM posos
							LEFT JOIN prescription_line_medicament ON prescription_line_medicament.prescription_line_medicament_id = posos.prescription_line_medicament_id
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
	  // Chargement d'une ligne possedant la poso la plus utilisée
		$line_medicament = new CPrescriptionLineMedicament();
    $line_medicament->load($poso['prescription_line_medicament_id']);
		$line_medicament->loadRefsPrises();
		
		// Pre-remplissage des prises les plus utilisées
    foreach($line_medicament->_ref_prises as $_prise){
      if(!$_prise->urgence_datetime && !$_prise->decalage_intervention){
	      $_prise->_id = '';
	      $_prise->object_id = $this->_id;
	      $_prise->object_class = $this->_class_name;
        $_prise->_ref_object = null;
	      $_prise->store();
      }
    }
		
    // Pre-remplissage de la voie la plus utilisée
    if($line_medicament->voie){
      $this->voie = $line_medicament->voie;
      $this->store();
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
	
	function countLockedPlanif(){
    $administration = new CAdministration();
    $ljoin["planification_systeme"] = "planification_systeme.planification_systeme_id = administration.planification_systeme_id";
    $ljoin["prescription_line_medicament"] = "prescription_line_medicament.prescription_line_medicament_id = planification_systeme.object_id AND planification_systeme.object_class = 'CPrescriptionLineMedicament'";
    
    // Chargement des planifications sur la ligne
    $planification = new CPlanificationSysteme();
    $where = array();
    $where["prescription_line_medicament.prescription_line_medicament_id"] = " = '$this->_id'";
    $this->_count_locked_planif = $count_planifications = $administration->countList($where, null, null, null, $ljoin);
  }
  
  function store(){   
		// Sauvegarde de la voie lors de la creation de la ligne
    if(!$this->_id && !$this->voie){
    	$this->loadRefProduitPrescription();
			if($this->_ref_produit_prescription->_id && $this->_ref_produit_prescription->voie){
			  $this->voie = $this->_ref_produit_prescription->voie;	
			} else {
			  $this->loadRefProduit();
				if(!$this->_ref_produit->voies){
				  $this->_ref_produit->loadVoies();
				}
				if(isset($this->_ref_produit->voies[0])){
	        $this->voie = $this->_ref_produit->voies[0];
	      }
			}
    }
		
		if(!$this->_id){
			$this->isInjectable();
			if($this->_is_injectable){
			  $this->injection_ide = '1';
			}
		}
    
    $mode_creation = !$this->_id;
    $calcul_planif = ($this->fieldModified("debut") || 
		                  $this->fieldModified("time_debut") || 
											$this->fieldModified("duree") || 
											$this->fieldModified("unite_duree") || 
											$this->fieldModified("time_fin") ||
											$this->fieldModified("substitution_active") ||
											$this->fieldModified("date_arret") || 
											$this->fieldModified("substitution_line_id") ||
											$this->_update_planif_systeme ||
											$this->fieldModified("inscription"));
  
	  // Lors du passage d'une inscription à une prescription, on modifie l'unité de prises des adm deja créées
	  if($this->fieldModified("inscription", "0")){
	  	// Chargement de la 1ere prise
			$this->loadRefsPrises();
			$first_prise = reset($this->_ref_prises);
			
			$unite_prise = $first_prise->unite_prise;
			$prise_id = !$first_prise->moment_unitaire_id ? $first_prise->prise_posologie_id : '';
			
			$this->loadRefsAdministrations();
			foreach($this->_ref_administrations as $_administration){
				$_administration->unite_prise = $unite_prise;
				$_administration->prise_id = $prise_id;
        
				if($msg = $_administration->store()){
		      return $msg;
		    }
			}
	  }
	
	  // Stockage du stupefiant lors de la creation de la ligne
    if(!$this->_id){
      $this->completeField("code_cip");
      $this->loadRefProduit();
      $this->_ref_produit->loadRefMonographie();

      if (preg_match("/Stup[éa-z&;]+fiant/i", $this->_ref_produit->_ref_monographie->condition_delivrance)){
        $this->stupefiant = 1;
      }
    }
	
  	if($msg = parent::store()){
  		return $msg;
  	}

    if($calcul_planif && $this->_ref_prescription->type == "sejour"){
  		$this->countLockedPlanif();
	
			// Si aucune planification n'est associée à une administration, on peut les supprimer
			if($this->_count_locked_planif == 0 && !$this->inscription){
				$this->removePlanifSysteme();
				if(!$this->substitution_line_id && $this->substitution_active){
					$this->calculPlanifSysteme();
				}
			}
    }
    
    // Pre-remplissage de la posologie la plus utilisée
    if($mode_creation && $this->_most_used_poso && $this->code_cis){
      $posos = $this->getMostUsedPoso();
      if(count($posos)){
        $this->applyPoso(reset($posos));
      }
    }
    
  	if($this->_delete_prises){
  		if($msg = $this->deletePrises()){
  			return $msg;
  		}
  	}
  }
    
  function loadRefsFwd() {
  	parent::loadRefsFwd();
    $this->loadRefProduit();
		$this->_ref_produit->loadRefPosologies();
    if(!$this->_ref_produit->libelle_unite_presentation){
      $this->_ref_produit->loadLibellePresentation();
      $this->_ref_produit->loadUnitePresentation();
    }
		
    $this->loadPosologie();
   
    $this->_unites_prise = array();

    $produits = $this->_ref_produit->loadRapportUnitePriseByCIS();
    
    $libelle_unite_presentation = $this->_ref_produit->libelle_unite_presentation;
    $libelle_unite_presentation_pluriel = $this->_ref_produit->libelle_unite_presentation_pluriel;
   
    foreach($this->_ref_produit->_ref_posologies as $_poso){
      $unite = $_poso->_code_unite_prise["LIBELLE_UNITE_DE_PRISE_PLURIEL"];
  
      if($unite){
				$unite_escape = preg_quote($unite);
        $unite_escape = str_replace("/", "\\/", $unite_escape);
				
	      $coef_adm = @$this->_ref_produit->rapport_unite_prise[$unite][$libelle_unite_presentation];
	      if($_poso->p_kg) {
	        // On ajoute la poso avec les /kg
	          $_presentation = "";
	          if($coef_adm){
			        if (!preg_match("/$unite_escape/i", $libelle_unite_presentation_pluriel)){
			          $_presentation = " ($coef_adm $libelle_unite_presentation/kg)";
			        }
	          }
		        $this->_unites_prise[] = "$unite/kg".$_presentation;
	      }
        $_presentation = "";
        if($coef_adm){
	        if (!preg_match("/$unite_escape/i", $libelle_unite_presentation_pluriel)){
	          $_presentation = " ($coef_adm $libelle_unite_presentation)";
	        }
        }
        $this->_unites_prise[] = $unite.$_presentation;
      }
    }
    
    // Si aucune prise n'est trouvée par le parcours des posologies, chargement direct des prises
    if(count($this->_unites_prise)<1){
      $this->_ref_produit->loadUnitesPrise();
      if(is_array($this->_ref_produit->_prises)){
	      foreach($this->_ref_produit->_prises as $_prise){
		      if(!in_array($_prise, $this->_unites_prise)){
		        $this->_unites_prise[] = $_prise;
		      }  
	      }
      }
    }
    
    // Ajout de la presentation comme unite de prise
	  foreach($produits as $_produit){
	    if ($_produit->libelle_presentation){
	      $libelle_unite_presentation = $_produit->libelle_unite_presentation;
		    $coef_adm = $_produit->rapport_unite_prise[$_produit->libelle_presentation][$libelle_unite_presentation];
	      $this->_unites_prise[] = "{$_produit->libelle_presentation} ($coef_adm $libelle_unite_presentation)";
	    }
    }

    if (is_array($this->_unites_prise)){
      $this->_unites_prise = array_unique($this->_unites_prise);
    }
		
		if(!in_array("ml", $this->_unites_prise) && (array_key_exists("ml", $this->_ref_produit->rapport_unite_prise) || $libelle_unite_presentation == "ml")){
      $this->_unites_prise[] = "ml";
    }
  }
  
  function isPerfusable(){
  	$this->loadRefProduitPrescription();
    if($this->_ref_produit_prescription->_id && $this->_ref_produit_prescription->voie){
    	if(self::$voies[$this->_ref_produit_prescription->voie]["perfusable"]){
    		  $this->_is_perfusable = true;

    	}
    }
		
		if($this->_ref_produit->voies){
	    foreach($this->_ref_produit->voies as $_voie){
	      if(isset(self::$voies[$_voie]) && self::$voies[$_voie]["perfusable"]){
	        $this->_is_perfusable = true;
	        break;  
	      }
	    }
    }
		
  }

  
  function isInjectable(){
    if($this->voie && $this->voie !== "none"){
	    if(isset(self::$voies[$this->voie]) && self::$voies[$this->voie]["injectable"]){
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
    if(!$this->substitute_for_id){
		  $this->_ref_substitution_lines["CPrescriptionLineMedicament"] = $this->loadBackRefs("substitutions_medicament"); 
      $this->_ref_substitution_lines["CPrescriptionLineMix"] = $this->loadBackRefs("substitutions_prescription_line_mix");  
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
		foreach($this->_ref_substitution_lines["CPrescriptionLineMix"] as $_substitution_line){
		  $_substitution_line->loadRefsLines();
	  }
  }
  
  /*
   * Permet de connaitre le nombre de lignes de substitutions possibles
   */
  function countSubstitutionsLines(){
    if(!$this->substitute_for_id){
      $this->_count_substitution_lines = $this->countBackRefs("substitutions_medicament") + $this->countBackRefs("substitutions_prescription_line_mix");
    } else {
      $object = new $this->substitute_for_class;
      $object->load($this->substitute_for_id);
      $object->countSubstitutionsLines();
      $this->_count_substitution_lines = $object->_count_substitution_lines;    
    }
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
	
	function loadRefProduitPrescription(){
		$this->_ref_produit_prescription = new CProduitPrescription();
		if($this->code_cis){
			$this->_ref_produit_prescription->code_cis = $this->code_cis;
			$this->_ref_produit_prescription->loadMatchingObject();
		}
		
		if(!$this->_ref_produit_prescription->_id && $this->code_ucd){
			$this->_ref_produit_prescription->code_cis = null;
      $this->_ref_produit_prescription->code_ucd = $this->code_ucd;
      $this->_ref_produit_prescription->loadMatchingObject();
		}
		
		if(!$this->_ref_produit_prescription->_id && $this->code_cip){
      $this->_ref_produit_prescription->code_cis = null;
      $this->_ref_produit_prescription->code_ucd = null;
      $this->_ref_produit_prescription->code_cip = $this->code_cip;
      $this->_ref_produit_prescription->loadMatchingObject();
    }
		
		if($this->_ref_produit_prescription->unite_prise){
		  $this->_unite_administration = $this->_ref_produit_prescription->unite_prise;
		}
	}
}

?>
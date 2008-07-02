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
class CPrescription extends CMbObject {
	
  // DB Table key
  var $prescription_id = null;
  
  // DB Fields
  var $praticien_id    = null;
  var $function_id     = null;
  var $object_class    = null;
  var $object_id       = null;
  var $libelle         = null;
  var $type            = null;
  
  // Object References
  var $_ref_object     = null;
  var $_ref_patient    = null;
  var $_ref_current_praticien = null;
  
  // BackRefs
  var $_ref_prescription_lines                = null;
  var $_ref_prescription_lines_element        = null;
  var $_ref_prescription_lines_element_by_cat = null;
  var $_ref_prescription_lines_comment        = null;
  
  // Others Fields
  var $_type_sejour = null;
  var $_counts_by_chapitre = null;
  var $_counts_no_valide = null;
  var $_dates_dispo = null;
  var $_current_praticien_id = null;  // Praticien utilisé pour l'affichage des protocoles / favoris dans la prescription
  var $_praticiens = null;            // Tableau de praticiens prescripteur
  var $_can_add_line = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prescription';
    $spec->key   = 'prescription_id';
    return $spec;
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["prescription_line_medicament"] = "CPrescriptionLineMedicament prescription_id";
    $backRefs["prescription_line_element"]    = "CPrescriptionLineElement prescription_id";
    $backRefs["prescription_line_comment"]    = "CPrescriptionLineComment prescription_id";
    return $backRefs;
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "praticien_id"  => "ref class|CMediusers",
      "function_id"   => "ref class|CFunctions",  
      "object_id"     => "ref class|CCodable meta|object_class",
      "object_class"  => "notNull enum list|CSejour|CConsultation",
      "libelle"       => "str",
      "type"          => "notNull enum list|traitement|pre_admission|sejour|sortie|externe",
      "_type_sejour"  => "notNull enum list|pre_admission|sejour|sortie"
     );
    return array_merge($specsParent, $specs);
  }
  
  function updateFormFields() {
  	global $AppUI;
  	
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = "Prescription du Dr ".$this->_ref_praticien->_view." : ".$this->_ref_object->_view;
    if($this->libelle){
    	$this->_view .= "($this->libelle)";
    }
    if(!$this->object_id){
    	$this->_view = "Protocole: ".$this->libelle;
    }
    
    // Chargement de l'utilisateur courant
    $user_courant = new CMediusers();
    $user_courant->load($AppUI->user_id);
    
    if($this->_ref_object->_class_name == "CSejour"){
	    if($user_courant->isPraticien()){
	    	$this->_current_praticien_id = $user_courant->_id;
	    } else {
	    	$this->_current_praticien_id = $this->_ref_object->_praticien_id;
	    }
    }
    if($this->_ref_object->_class_name == "CConsultation"){
      if($user_courant->isPraticien()){
	    	$this->_current_praticien_id = $user_courant->_id;
	    } else {
	    	$this->_current_praticien_id = $this->praticien_id;
	    }
    }
    $this->loadRefCurrentPraticien();
  }
  
  /*
   * Permet de savoir si l'utilisateur courant a le droit de créer des lignes dans la prescription
   */
  function getAdvancedPerms($is_praticien, $mode_pharma){
  	global $AppUI;
		
  	// Chargement du user_courant
		$user = new CMediusers();
		$user->load($AppUI->user_id);
		
		// Si le user courant est un praticien
		if($is_praticien || !$this->object_id || $mode_pharma){
			$this->_can_add_line = 1;
		} 
		// Sinon (infirmiere)
		else {
			$time = mbTime();
			$borne_start = CAppUI::conf("dPprescription CPrescription infirmiere_borne_start").":00:00";
			$borne_stop = CAppUI::conf("dPprescription CPrescription infirmiere_borne_stop").":00:00";	
			$freeDays = mbBankHolidays();
			$day = mbTransformTime(null, mbDate(), "%w");

		  if(array_key_exists(mbDate(),$freeDays) || ($time >= $borne_start) || ($time <= $borne_stop) || ($day == 0) || ($day == 6)){
		  	$this->_can_add_line = 1;
		  }
		}	
  }
  
  function check(){
  	global $AppUI;
  	
    if ($msg = parent::check()) {
  	  return $msg;
  	}
 
  	// Test permettant d'eviter que plusieurs prescriptions identiques soient créées 
  	if($this->object_id !== null && $this->object_class !== null && $this->praticien_id !== null && $this->type !== null){
      $prescription = new CPrescription();
      $prescription->object_id = $this->object_id;
      $prescription->object_class = $this->object_class;
      if($prescription->type != "externe"){
        $prescription->praticien_id = $this->praticien_id;
      }
      $prescription->type = $this->type;
  	  $prescription->loadMatchingObject();
  	  
  	  if($prescription->_id){
  	  	return "Prescription déjà existante";
  	  }	
  	}	
  }
  
  /*
   * Calcul du praticien_id responsable de la prescription
   */
  function calculPraticienId(){
  	global $AppUI;
  	
  	if($this->object_id !== null && $this->object_class !== null && $this->type !== null && $this->object_id){
  		// Chargement du user courant
  		$user_courant = new CMediusers();
  		$user_courant->load($AppUI->user_id);
  		// Chargement de l'object
  		$object = new $this->object_class;
  	  $object->load($this->object_id);
  	  $object->loadRefsFwd();
  		
  		if($this->type != "sejour"){
  			if($user_courant->isPraticien()){
  				$this->praticien_id = $user_courant->_id;
  			} else {
  				$this->praticien_id = $object->_praticien_id;
  			}
  		}
  		if($this->type == "sejour"){
  			$this->praticien_id = $object->_praticien_id;
  		}
  	}
  }
  
  
  function store(){  	
  	if(!$this->_id){
  		$this->calculPraticienId(); 
  	}
  	
    if ($msg = $this->check()) {
      return $msg;
    }
    return parent::store();
  }
  
  
  /*
   * Chargement du praticien
   */
  function loadRefPraticien(){
  	$this->_ref_praticien = new CMediusers();
  	$this->_ref_praticien->load($this->praticien_id);
  }
  
  /*
   * Chargement du praticien utilisé pour l'affichage des protocoles/favoris
   */
  function loadRefCurrentPraticien(){
  	$this->_ref_current_praticien = new CMediusers();
  	$this->_ref_current_praticien->load($this->_current_praticien_id);
  }
  
  /*
   * Chargement de l'objet de la prescription
   */ 
  function loadRefObject(){
  	$this->_ref_object = new $this->object_class;
    $this->_ref_object->load($this->object_id);
  }
  
  /*
   * Chargement du patient
   */
  function loadRefPatient(){
    $this->_ref_patient = new CPatient();
    $this->_ref_patient->load($this->_ref_object->patient_id);	
  }
  
  /*
   * Forward Refs
   */
  function loadRefsFwd() {
    $this->loadRefPraticien();
    $this->loadRefObject();
    $this->loadRefPatient();
  }
  
  /*
   * Compte le nombre de lignes non validées dans la prescription
   */ 
  function countNoValideLines(){
    $this->_counts_no_valide = 0;
    if($this->_id){
      $line = new CPrescriptionLineMedicament();
      $where = array();
      $where["signee"] = " = '0'";
      $where["prescription_id"] = " = '$this->_id'";
      $where["child_id"] = "IS NULL";
      $where["substitution_line_id"] = "IS NULL";
      $this->_counts_no_valide = $line->countList($where);
    }
  }
  
	/*
	 * Chargement du nombre des medicaments et d'elements
	 */
  function countLinesMedsElements($praticien_sortie_id = null){
  	$this->_counts_by_chapitre = array();
  	
  	$line_comment_med = new CPrescriptionLineComment();
  	$ljoin_comment["category_prescription"] = "prescription_line_comment.category_prescription_id = category_prescription.category_prescription_id";
  	
  	// Count sur les medicaments
    $where = array();
    $where["prescription_id"] = " = '$this->_id'";
    $where["category_prescription.chapitre"] = "IS NULL";
    
    
  	$line_med = new CPrescriptionLineMedicament();
  	$whereMed["prescription_id"] = " = '$this->_id'";
  	$whereMed["child_id"] = "IS NULL";
  	$whereMed["substitution_line_id"] = "IS NULL";
  	if($praticien_sortie_id){
  		$where["praticien_id"] = " = '$praticien_sortie_id'";
  		$whereMed["praticien_id"] = " = '$praticien_sortie_id'";
  	}
  	$this->_counts_by_chapitre["med"] = $line_med->countList($whereMed);
  	$this->_counts_by_chapitre["med"] += $line_comment_med->countList($where, null, null, null, $ljoin_comment);
  	
  	
  	// Count sur les elements
  	$ljoin_element["element_prescription"] = "prescription_line_element.element_prescription_id = element_prescription.element_prescription_id";
	  $ljoin_element["category_prescription"] = "element_prescription.category_prescription_id = category_prescription.category_prescription_id";
	  
  	$line_element = new CPrescriptionLineElement();
  	$line_comment = new CPrescriptionLineComment();
		
  	$category = new CCategoryPrescription;
    $chapitres = explode("|", $category->_specs["chapitre"]->list);
      	
  	// Initialisation du tableau
    foreach ($chapitres as $chapitre){
    	$this->_counts_by_chapitre[$chapitre] = 0;
    }
  	
    // Parcours des elements
 	  $where = array();
    $where["prescription_id"] = " = '$this->_id'";
    if($praticien_sortie_id){
  		$where["praticien_id"] = " = '$praticien_sortie_id'";
  	}
  	
    foreach ($chapitres as $chapitre) {
  	  $where["category_prescription.chapitre"] = " = '$chapitre'";
   	  $nb_element = $line_element->countList($where, null, null, null, $ljoin_element);
			$nb_comment = $line_comment->countList($where, null, null, null, $ljoin_comment);
			$this->_counts_by_chapitre[$chapitre] = $nb_element + $nb_comment;
  	}
  }
  
  
  /*
   * Chargement de l'historique
   */
  function loadRefsLinesHistorique(){
  	$this->loadRefObject();
  	$historique = array();
  	$this->_ref_object->loadRefsPrescriptions();
  	if($this->type == "sejour" || $this->type == "sortie"){
  		$prescription_pre_adm =& $this->_ref_object->_ref_prescriptions["pre_admission"];
  		$prescription_pre_adm->loadRefsLinesMedComments("0");
  		$prescription_pre_adm->loadRefsLinesElementsComments("0");
  		$historique["pre_admission"] = $prescription_pre_adm;
  	}
  	if($this->type == "sortie"){
  		$prescription_sejour =& $this->_ref_object->_ref_prescriptions["sejour"];
  		$prescription_sejour->loadRefsLinesMedComments("0");
  		$prescription_sejour->loadRefsLinesElementsComments("0");
  		$historique["sejour"] = $prescription_sejour;
  	}
  	return $historique;
  }
  
  
  /*
   * Chargement des lignes de prescription de médicament
   */
  function loadRefsLinesMed($with_child = 0, $with_subst = 0) {
    $line = new CPrescriptionLineMedicament();
    $where = array();
    $where["prescription_id"] = " = '$this->_id'";
    
    if($with_child != "1"){
      $where["child_id"] = "IS NULL";
    }
    if($with_subst != "1"){
      $where["substitution_line_id"] = "IS NULL";
    }
    
    $order = "prescription_line_medicament_id DESC";
    $this->_ref_prescription_lines = $line->loadList($where, $order);
    
    foreach($this->_ref_prescription_lines as &$_line){
    	$_line->_ref_produit->loadRefPosologies();
    }
  }
  
  /*
   * Chargement des lignes de medicaments (medicaments + commentaires)
   */
  function loadRefsLinesMedComments($withRefs = "1"){
    // Chargement des lignes de medicaments
  	$this->loadRefsLinesMed();
  	// Chargement des lignes de commentaire du medicament
  	$this->loadRefsLinesComment("medicament");
  	
  	// Initialisation du tableau de fusion
  	$this->_ref_lines_med_comments["med"] = array();
  	$this->_ref_lines_med_comments["comment"] = array();
  	
  	foreach($this->_ref_prescription_lines as &$line_med){
  		if($withRefs){
  			$line_med->loadRefsPrises();
  		  $this->_praticiens[$line_med->praticien_id] = $line_med->_ref_praticien->_view;
  		}
  		$this->_ref_lines_med_comments["med"][] = $line_med;
  	}
  	
  	if(isset($this->_ref_prescription_lines_comment["medicament"]["cat"]["comment"])){
      foreach($this->_ref_prescription_lines_comment["medicament"]["cat"]["comment"] as &$comment_med){
  	  	if($withRefs){
  	  	  $this->_praticiens[$comment_med->praticien_id] = $comment_med->_ref_praticien->_view;
  	  	}
  	  	$this->_ref_lines_med_comments["comment"][] = $comment_med;
      }
  	}
  }
  
  /*
   * Chargement des lignes d'element
   */
  function loadRefsLinesElement($chapitre = "", $withRefs = "1"){
  	$line = new CPrescriptionLineElement();
  	$where = array();
  	$ljoin = array();
  	
  	if($chapitre){
  	  $ljoin["element_prescription"] = "prescription_line_element.element_prescription_id = element_prescription.element_prescription_id";
	    $ljoin["category_prescription"] = "element_prescription.category_prescription_id = category_prescription.category_prescription_id";
      $where["category_prescription.chapitre"] = " = '$chapitre'";
  	}
  	
    $where["prescription_id"] = " = '$this->_id'";
    
    $order = "prescription_line_element_id DESC";
    $this->_ref_prescription_lines_element = $line->loadList($where, $order, null, null, $ljoin);
    foreach ($this->_ref_prescription_lines_element as &$line_element){	
    	$line_element->loadRefElement();
    	if($withRefs){
	    	$line_element->loadRefsPrises();
	    	$line_element->loadRefExecutant();
	    	$this->_praticiens[$line_element->praticien_id] = $line_element->_ref_praticien->_view;
	    }
    	$line_element->_ref_element_prescription->loadRefCategory();
    }
  }
  
  
  /*
   * Chargement des lignes d'elements par catégorie
   */
  function loadRefsLinesElementByCat($withRefs = "1"){
  	$this->loadRefsLinesElement("",$withRefs);
  	$this->_ref_prescription_lines_element_by_cat = array();
  	
  	foreach($this->_ref_prescription_lines_element as $line){
  		$category = new CCategoryPrescription();
  		$category->load($line->_ref_element_prescription->category_prescription_id);
  		$this->_ref_prescription_lines_element_by_cat[$category->chapitre]["$category->_id"]["element"][] = $line;	
   	}
  	ksort($this->_ref_prescription_lines_element_by_cat);
  }
  
  
  /*
   * Chargement des lignes de commentaires
   */
  function loadRefsLinesComment($chapitre = null, $withRefs = "1"){
  	$this->_ref_prescription_lines_comment = array();
    
  	// Initialisation des tableaux
  	$category = new CCategoryPrescription();
  	
  	foreach($category->_specs["chapitre"]->_list as $_chapitre){
  	  $this->_ref_prescription_lines_comment[$_chapitre] = array();	
  	}

  	$commentaires = array();
  	$line_comment = new CPrescriptionLineComment();
  	
  	$where["prescription_id"] = " = '$this->_id'";
  	$order = "prescription_line_comment_id DESC";
  	$ljoin = array();
  	
  	if($chapitre && $chapitre != "medicament"){
  		$ljoin["category_prescription"] = "prescription_line_comment.category_prescription_id = category_prescription.category_prescription_id";
  	  $where["category_prescription.chapitre"] = " = '$chapitre'"; 	
  	}
  	if($chapitre == "medicament"){
  	  $where["category_prescription_id"] = " IS NULL"; 		
  	}
  	$commentaires = $line_comment->loadList($where, $order, null, null, $ljoin);
  	
  	foreach($commentaires as $_line_comment){
  		  if($withRefs){
          $_line_comment->loadRefExecutant();
  		  }
        if($_line_comment->category_prescription_id){
  		  	// Chargement de la categorie
          $_line_comment->loadRefCategory();
  		  	$cat = new CCategoryPrescription();
  		  	$cat->load($_line_comment->category_prescription_id);
  		  	$chapitre = $cat->chapitre;
  		  } else {
  		  	$chapitre = "medicament";
  		  }
        $this->_ref_prescription_lines_comment[$chapitre]["$_line_comment->category_prescription_id"]["comment"][] = $_line_comment;
        $this->_praticiens[$_line_comment->praticien_id] = $_line_comment->_ref_praticien->_view;
    }		
  }
  
  /*
   * Chargement de toutes les lignes (y compris medicaments)
   */
  function loadRefsLinesAllComments(){
  	$this->_ref_prescription_lines_all_comments = $this->loadBackRefs("prescription_line_comment");
  }
  
  /*
   * Chargement des lignes d'elements (Elements + commentaires)
   */
  function loadRefsLinesElementsComments($withRefs = "1"){
  	$this->loadRefsLinesElementByCat($withRefs);
  	$this->loadRefsLinesComment("",$withRefs);
  	
  	// Suppression des ligne de medicaments
  	unset($this->_ref_prescription_lines_comment["medicament"]);
  	
  	// Fusion des tableaux d'element et de commentaire 
  	$this->_ref_lines_elements_comments = array_merge_recursive($this->_ref_prescription_lines_element_by_cat, $this->_ref_prescription_lines_comment);
    
  	foreach($this->_ref_lines_elements_comments as &$chapitre){
  		foreach($chapitre as &$cat){
    	if(!array_key_exists("comment", $cat)){
    		$cat["comment"] = array();
    	}
      if(!array_key_exists("element", $cat)){
    		$cat["element"] = array();
    	}
  		}
    }
  }
  
  /*
   * Chargement des medicaments favoris d'un praticien
   */
  static function getFavorisMedPraticien($praticien_id){
  	$favoris = array();
  	$listFavoris = array();
  	$listFavoris["medicament"] = array();
  	$favoris = CBcbProduit::getFavoris($praticien_id);
  	foreach($favoris as $_fav){
  		$produit = new CBcbProduit();
  		$produit->load($_fav["code_cip"],"0");
  		$listFavoris["medicament"][] = $produit;
    }
  	return $listFavoris["medicament"];
  }
  
  /*
   * Chargement des favoris de prescription pour un praticien donné
   */
  static function getFavorisPraticien($praticien_id){
  	$listFavoris["medicament"] = CPrescription::getFavorisMedPraticien($praticien_id);
  	$category = new CCategoryPrescription();
    foreach($category->_specs["chapitre"]->_list as $chapitre){
  	  $listFavoris[$chapitre] = array();
  	  $favoris[$chapitre] = CElementPrescription::getFavoris($praticien_id, $chapitre);	  
    }
	  foreach($favoris as $key => $typeFavoris) {
	  	foreach($typeFavoris as $curr_fav){
	  		$element = new CElementPrescription();
	  	  $element->load($curr_fav["element_prescription_id"]);
	  		$listFavoris[$key][] = $element;
      }
	  }
	  return $listFavoris;  	
  }
  
  
  // Generation du plan de soin sous forme de tableau
  function calculPlanSoin($date, &$lines_med, &$prises_med, &$list_prises_med, &$lines_element, &$prises_element, &$list_prises_element, &$nb_produit_by_cat, &$all_lines_med="", &$all_lines_element="", &$intitule_prise_med="", &$intitule_prise_element=""){
    // Chargement des lignes
  	$this->loadRefsLinesMed("1");
	  $this->loadRefsLinesElementByCat();
	  $this->_ref_object->loadRefPrescriptionTraitement();
	  $lines["medicament"] = $this->_ref_prescription_lines;
	  $traitement_personnel = $this->_ref_object->_ref_prescription_traitement;
	  if($traitement_personnel->_id){
	    $traitement_personnel->loadRefsLinesMed("1");
	  }
	  $lines["traitement"] = $traitement_personnel->_ref_prescription_lines;
	  
    // Parcours des lignes
    foreach($lines as $cat_name => $lines_cat){
    	if(count($lines_cat)){
    	foreach($lines_cat as &$_line_med){
    		// Si la ligne est prescrite pour la date donnée
    		if(($date >= $_line_med->debut && $date <= mbDate($_line_med->_date_arret_fin)) 
    		|| (!$_line_med->_date_arret_fin && $_line_med->_date_arret_fin <= $date && $date >= $_line_med->debut)){
    		   // Chargement des prises de la ligne
           $_line_med->loadRefsPrises();
           // Parcours des prises
					 foreach($_line_med->_ref_prises as &$_prise_med){
					 	 // Calcul du nombre de lignes
					 	 if($_prise_med->moment_unitaire_id){
					 	 	$prise = $_prise_med->unite_prise;
					 	 	$intitule_prise_med[$_line_med->_id][$_prise_med->unite_prise][$_prise_med->_id] = $_prise_med->_view;
					 	 } else {
					 	 	$prise = $_prise_med->_id;
					 	 	$intitule_prise_med[$_line_med->_id]["autre"][$_prise_med->_id] = $_prise_med->_view;
					 	 }
			       
					 
					 	 if($_prise_med->moment_unitaire_id){
					 	 	 // Stockage des quantite de prises pour le pre-remplissage de la feuille de soin
					 	   $list_prises_med[$_line_med->_id][$prise][substr($_prise_med->_ref_moment->heure, 0, 2)] = $_prise_med->quantite;
					 	 } 
					 	 // Stockage des lignes rangées par unite de prise et par prise
						 if($_prise_med->nb_tous_les && $_prise_med->unite_tous_les){
						 	 if($_prise_med->calculDatesPrise($date)){
						 	 	 if(is_array($all_lines_med)){
						 	     $all_lines_med[$_line_med->_id][$prise] = $_line_med;
						 	 	 }
						 	 	 
								 	 // Stockage du nombre de ligne de medicaments
							 	 if(!@array_key_exists($prise, $lines_med[$_line_med->_id])){
							 	 	 @$nb_produit_by_cat["med"]++;
							 	 }
							 	 
					 	     $lines_med[$_line_med->_id][$prise] = $_line_med;
					 	     $prises_med[$_line_med->_id][$prise][] = $_prise_med;
						 	 }
						 } else {
							 // Stockage du nombre de ligne de medicaments
						 	 if(!@array_key_exists($prise, $lines_med[$_line_med->_id])){
						 	 	 @$nb_produit_by_cat["med"]++;
						 	 }
						 	 
						 	  if(is_array($all_lines_med)){
						 	    $all_lines_med[$_line_med->_id][$prise] = $_line_med;
						 	  }
						 	  $lines_med[$_line_med->_id][$prise] = $_line_med;
					 	    $prises_med[$_line_med->_id][$prise][] = $_prise_med;
						 }
					 }
				 }
    	 }
    	}
     }
       
     
	  // Parcours des elements
	  foreach($this->_ref_prescription_lines_element_by_cat as $name_chap => $elements_chap){
	  	if($name_chap != "dmi"){
		  	foreach($elements_chap as $name_cat => $elements_cat){
		  		foreach($elements_cat as &$_elements){
		  			foreach($_elements as &$_line_element){
			        if(($date >= $_line_element->debut && $date <= mbDate($_line_element->_date_arret_fin))){
			        	// Si l'element est un DM, on le rajoute dans la liste
			        	if($name_chap == "dm" || $name_chap == "anapath" || $name_chap == "consult" || $name_chap == "imagerie"){
						  		$lines_element[$name_chap][$name_cat][$_line_element->_id]["aucune_prise"] = $_line_element;
						  		$all_lines_element[$name_chap][$name_cat][$_line_element->_id]["aucune_prise"] = $_line_element;
						  	  @$nb_produit_by_cat[$name_cat]++;
						  	} 
						  	// Sinon, on regarde si l'element possède des prises pour la date donnée
						  	else {
				        	// Chargement des prises
			  			  	$_line_element->loadRefsPrises();  	
							  	foreach($_line_element->_ref_prises as &$_prise_element){	
								 	  if($_prise_element->moment_unitaire_id){
								 	  	$prise = $_prise_element->unite_prise;
								 	  	$intitule_prise_element[$_line_element->_id][$_prise_element->unite_prise][$_prise_element->_id] = $_prise_element->_view;
								 	  } else {
								 	  	$prise = $_prise_element->_id;
								 	  	$intitule_prise_element[$_line_element->_id]["autre"][$_prise_element->_id] = $_prise_element->_view;
								 	  }
									 	if($_prise_element->moment_unitaire_id){
								 	 	 // Stockage des quantite de prises pour le pre-remplissage de la feuille de soin
								 	   $list_prises_element[$_line_element->_id][$prise][substr($_prise_element->_ref_moment->heure, 0, 2)] = $_prise_element->quantite;
								 	  } 
								 	 
							  		if($_prise_element->nb_tous_les && $_prise_element->unite_tous_les){
									    if($_prise_element->calculDatesPrise($date)){
											  // Stockage du nombre de ligne de medicaments
											 	if(!@array_key_exists($prise, $lines_element[$name_chap][$name_cat][$_line_element->_id])){
											 	  @$nb_produit_by_cat[$name_cat]++;
											 	}
									      $prises_element[$_line_element->_id][$prise][] = $_prise_element;
									      $lines_element[$name_chap][$name_cat][$_line_element->_id][$prise] = $_line_element;
									      if(is_array($all_lines_element)){
									        $all_lines_element[$name_chap][$name_cat][$_line_element->_id][$prise] = $_line_element;
									      }
									    }
									  } else {
										  // Stockage du nombre de ligne de medicaments
										 	if(!@array_key_exists($prise, $lines_element[$name_chap][$name_cat][$_line_element->_id])){
										 	  @$nb_produit_by_cat[$name_cat]++;
										 	}
										 	
									    $prises_element[$_line_element->_id][$prise][] = $_prise_element;
									    $lines_element[$name_chap][$name_cat][$_line_element->_id][$prise] = $_line_element;
									    if(is_array($all_lines_element)){
									      $all_lines_element[$name_chap][$name_cat][$_line_element->_id][$prise] = $_line_element;
									    }
									  }
							  	}
			  			  }
			        }
		    	  }
		  	  }
	  	  }
	  	}
	  }
  }
}

?>
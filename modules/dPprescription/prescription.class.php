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
  var $praticien_id = null;
  var $function_id = null;  // XOR entre praticien_id et function_id
  
  
  var $object_class = null;
  var $object_id    = null;
  var $libelle      = null;
  var $type         = null;
  
  // Object References
  var $_ref_object  = null;
  var $_ref_patient = null;
  
  // BackRefs
  var $_ref_prescription_lines                = null;
  var $_ref_prescription_lines_element        = null;
  var $_ref_prescription_lines_element_by_cat = null;
  var $_ref_prescription_lines_comment        = null;
  
  // Others Fields
  var $_type_sejour = null;
  
  var $_counts_by_chapitre = null;
  
  
  function CPrescription() {
    $this->CMbObject("prescription", "prescription_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
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
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = "Prescription du Dr. ".$this->_ref_praticien->_view." : ".$this->_ref_object->_view;
    if($this->libelle){
    	$this->_view .= "($this->libelle)";
    }
    if(!$this->object_id){
    	$this->_view = "Protocole: ".$this->libelle;
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
  
  
  function store(){
  	global $AppUI;
  	
    if ($msg = $this->check()) {
      return $msg;
    }
    return parent::store();
  }
  
  
  
  function loadRefPraticien(){
  	$this->_ref_praticien = new CMediusers();
  	$this->_ref_praticien->load($this->praticien_id);
  }
  
  function loadRefObject(){
  	$this->_ref_object = new $this->object_class;
    $this->_ref_object->load($this->object_id);
  }
  
  function loadRefPatient(){
    $this->_ref_patient = new CPatient();
    $this->_ref_patient->load($this->_ref_object->patient_id);	
  }
  
  function loadRefsFwd() {
    $this->loadRefPraticien();
    $this->loadRefObject();
    $this->loadRefPatient();
  }
  
  
  //Chargement du nombre des medicaments et d'elements
  function countLinesMedsElements(){
  	$this->_counts_by_chapitre = array();
  	
  	$line_comment_med = new CPrescriptionLineComment();
  	$ljoin_comment["category_prescription"] = "prescription_line_comment.category_prescription_id = category_prescription.category_prescription_id";
  	
  	// Count sur les medicaments
    $where = array();
    $where["prescription_id"] = " = '$this->_id'";
    $where["category_prescription.chapitre"] = "IS NULL";
  	$line_med = new CPrescriptionLineMedicament();
  	$line_med->prescription_id = $this->_id;
  	$this->_counts_by_chapitre["med"] = $line_med->countMatchingList();
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
    foreach ($chapitres as $chapitre) {
  	  $where["category_prescription.chapitre"] = " = '$chapitre'";
   	  $nb_element = $line_element->countList($where, null, null, null, $ljoin_element);
			$nb_comment = $line_comment->countList($where, null, null, null, $ljoin_comment);
			$this->_counts_by_chapitre[$chapitre] = $nb_element + $nb_comment;
  	}
  }
  
  
  // Chargement des lignes de prescription
  function loadRefsLines() {
    $line = new CPrescriptionLineMedicament();
    $where = array("prescription_id" => "= $this->_id");
    $order = "prescription_line_medicament_id DESC";
    $this->_ref_prescription_lines = $line->loadList($where, $order);
    
    foreach($this->_ref_prescription_lines as &$_line){
    	$_line->_ref_produit->loadRefPosologies();
    }
  }
  
  
  //--------------
  // Chargement des lignes de medicaments (medicaments + commentaires)
  //--------------
  function loadRefsLinesMedComments(){
    // Chargement des lignes de medicaments
  	$this->loadRefsLines();
  	// Chargement des lignes de commentaire du medicament
  	$this->loadRefsLinesComment("medicament");
  	
  	// Initialisation du tableau de fusion
  	$this->_ref_lines_med_comments["med"] = array();
  	$this->_ref_lines_med_comments["comment"] = array();
  	
  	foreach($this->_ref_prescription_lines as &$line_med){
  		$line_med->loadRefsPrises();
  		$line_med->loadRefLogDateArret();
  		$line_med->loadRefLogSignee();
  		$line_med->loadRefPraticien();
  		$this->_ref_lines_med_comments["med"][] = $line_med;
  	}
  	
  	if(isset($this->_ref_prescription_lines_comment["medicament"]["cat"]["comment"])){
      foreach($this->_ref_prescription_lines_comment["medicament"]["cat"]["comment"] as &$comment_med){
  	  	$comment_med->loadRefPraticien();
  	  	$comment_med->loadRefLogSignee();
      	$this->_ref_lines_med_comments["comment"][] = $comment_med;
  	  }
  	}
  }
  
  // Chargement des lignes d'element
  function loadRefsLinesElement($chapitre = ""){
  	$line = new CPrescriptionLineElement();
  	$where = array();
  	$ljoin = array();
  	
  	if($chapitre){
  	  $ljoin["element_prescription"] = "prescription_line_element.element_prescription_id = element_prescription.element_prescription_id";
	    $ljoin["category_prescription"] = "element_prescription.category_prescription_id = category_prescription.category_prescription_id";
      $where["category_prescription.chapitre"] = " = 'soin'";
  	}
  	
    $where["prescription_id"] = " = '$this->_id'";
    
    $order = "prescription_line_element_id DESC";
    $this->_ref_prescription_lines_element = $line->loadList($where, $order, null, null, $ljoin);
    foreach ($this->_ref_prescription_lines_element as &$line_element){
    	$line_element->loadRefElement();
    	$line_element->loadRefPraticien();
    	$line_element->loadRefLogSignee();
    	$line_element->loadRefsPrises();
    	$line_element->loadRefExecutant();
    	$line_element->_ref_element_prescription->loadRefCategory();
    }
  }
  
  
  // Chargement des lignes d'elements par catégorie
  function loadRefsLinesElementByCat(){
  	$this->loadRefsLinesElement();
  	$this->_ref_prescription_lines_element_by_cat = array();
  	
  	foreach($this->_ref_prescription_lines_element as $line){
  		$category = new CCategoryPrescription();
  		$category->load($line->_ref_element_prescription->category_prescription_id);
  		$this->_ref_prescription_lines_element_by_cat[$category->chapitre]["cat".$category->_id]["element"][] = $line;	
   	}
  	ksort($this->_ref_prescription_lines_element_by_cat);
  }
  
  
  // Chargement des lignes de commentaires
  function loadRefsLinesComment($category_name = null){
  	$this->_ref_prescription_lines_comment = array();
    
  	// Initialisation des tableaux
  	$category = new CCategoryPrescription();
  	
  	foreach($category->_specs["chapitre"]->_list as $chapitre){
  	  $this->_ref_prescription_lines_comment[$chapitre] = array();	
  	}

  	$commentaires = array();
  	$line_comment = new CPrescriptionLineComment();
  	
  	$where["prescription_id"] = " = '$this->_id'";
  	$order = "prescription_line_comment_id DESC";
  	$ljoin = array();
  	
  	if($category_name && $category_name != "medicament"){
  		$ljoin["category_prescription"] = "prescription_line_comment.category_prescription_id = category_prescription.category_prescription_id";
  	  $where["category_prescription.chapitre"] = " = '$category_name'"; 	
  	}
  	if($category_name == "medicament"){
  	  $where["category_prescription_id"] = " IS NULL"; 		
  	}
  	$commentaires = $line_comment->loadList($where, $order, null, null, $ljoin);
  	
  	foreach($commentaires as $_line_comment){
  		  if($_line_comment->category_prescription_id){
  		  	// Chargement de la categorie
          $_line_comment->loadRefCategory();
          $_line_comment->loadRefPraticien();
          $_line_comment->loadRefLogSignee();
          $_line_comment->loadRefExecutant();
    	
  		  	$cat = new CCategoryPrescription();
  		  	$cat->load($_line_comment->category_prescription_id);
  		  	$chapitre = $cat->chapitre;
  		  } else {
  		  	$chapitre = "medicament";
  		  }
        $this->_ref_prescription_lines_comment[$chapitre]["cat".$_line_comment->category_prescription_id]["comment"][] = $_line_comment;
    }		
  }
  
  
  
  // Chargement de toutes les lignes (y compris medicaments)
  function loadRefsLinesAllComments(){
  	$this->_ref_prescription_lines_all_comments = $this->loadBackRefs("prescription_line_comment");
  }
  

  
  
  //-------------  
  // Chargement des lignes d'elements (Elements + commentaires)
  //-------------
  function loadRefsLinesElementsComments(){
  	$this->loadRefsLinesElementByCat();
  	$this->loadRefsLinesComment();
  	
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
  
  
  // Chargement des favoris de prescription pour un praticien donné
  static function getFavorisPraticien($praticien_id){
  	$favoris = array();
  	$listFavoris = array();
  	$listFavoris["medicament"] = array();
  	$favoris["medicament"] = CBcbProduit::getFavoris($praticien_id);
  	
  	$category = new CCategoryPrescription();
    foreach($category->_specs["chapitre"]->_list as $chapitre){
  	  $listFavoris[$chapitre] = array();
  	  $favoris[$chapitre] = CElementPrescription::getFavoris($praticien_id, $chapitre);	  
    }

	  
	  foreach($favoris as $key => $typeFavoris) {
	  	foreach($typeFavoris as $curr_fav){
	  		if($key == "medicament"){
	  		  $produit = new CBcbProduit();
	        $produit->load($curr_fav["code_cip"]);
	        $listFavoris["medicament"][] = $produit;
	  		} else {
	  			$element = new CElementPrescription();
	  			$element->load($curr_fav["element_prescription_id"]);
	  			$listFavoris[$key][] = $element;
	  		}
	  	}
	  }
	  return $listFavoris;  	
  }
}

?>
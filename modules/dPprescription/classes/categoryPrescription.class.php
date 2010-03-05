<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CCategoryPrescription extends CMbObject {
  // DB Table key
  var $category_prescription_id = null;
  
  // DB Fields
  var $chapitre    = null;
  var $nom         = null;
  var $description = null;
  var $header      = null;
  var $group_id    = null;
  
  // BackRefs
  var $_ref_elements_prescription = null;
	var $_count_elements_prescription = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'category_prescription';
    $spec->key   = 'category_prescription_id';
    return $spec;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["chapitre"]    = "enum notNull list|anapath|biologie|imagerie|consult|kine|soin|dm|dmi";
    $specs["nom"]         = "str notNull seekable";
    $specs["description"] = "text";
    $specs["header"]      = "text";
    $specs["group_id"]    = "ref class|CGroups";
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["elements_prescription"]   = "CElementPrescription category_prescription_id";
    $backProps["executants_prescription"] = "CExecutantPrescriptionLine category_prescription_id";
    $backProps["functions_category"]      = "CFunctionCategoryPrescription category_prescription_id";
    $backProps["comments_prescription"]   = "CPrescriptionLineComment category_prescription_id";
    $backProps["transmissions"]           = "CTransmissionMedicale object_id";
    $backProps["prescription_category_group_items"] = "CPrescriptionCategoryGroupItem category_prescription_id";
    return $backProps;
  }     
  
  function updateFormFields(){
  	parent::updateFormFields();
  	$this->_view = $this->nom;
  }
  
  
  function loadRefGroup(){
    $group = new CGroups();
    $this->_ref_group = $group->getCached($this->group_id);
  }
  
  function loadElementsPrescription($with_cancelled = true) {
    //$this->_ref_elements_prescription = $this->loadBackRefs("elements_prescription","libelle");
    $element = new CElementPrescription();
		if(!$with_cancelled){
		  $element->cancelled = '0';
		}
		$element->category_prescription_id = $this->_id;
		$this->_ref_elements_prescription = $element->loadMatchingList("libelle");
	}
  
	function countElementsPrescription() {
    $this->_count_elements_prescription = $this->countBackRefs("elements_prescription","libelle");
  }
	
  /**
   * Charge toutes les categories triées par chapitre
   * @param $chapitre string Permet de restreindre à un seul chapitre
   * @param $group string 'no_group'     => non associées à une clinique
   * 											'current_group => associées à la clinique courante
   *                      'current'      => no_group OR current_group 
   *                      'all'          => toutes les categories
   * 								 int	 group_id => group selectionné
   * @return array[CCategoryPrescription] Les catégories
   */
  static function loadCategoriesByChap($chapitre = null, $group="all") {
    global $g;
    
		$categorie = new CCategoryPrescription;
		$where = array();

		if($chapitre){
		  $where["chapitre"] = " = '$chapitre'";
		}
    
		// Permet de filtrer les categories
    if($group != 'all'){
      if(is_numeric($group)){
        $where["group_id"] = " = '$group'";
      }
      if($group == 'no_group'){
        $where["group_id"] = "IS NULL";
      }
      if($group == 'current_group'){
        $where["group_id"] = " = '$g'";
      }
      if($group == 'current'){
        $where[] = "group_id = '$g' OR group_id IS NULL"; 
      }
    }
		
		// Initialisation des chapitres
		$chapitres = explode("|", $categorie->_specs["chapitre"]->list);
		
		$categories_par_chapitre = array();
		foreach ($chapitres as $chapitre) {
		  $categories_par_chapitre[$chapitre] = array();
		}
		
		// Chargement et classement par chapitre
    $order = "nom";
    $categories = $categorie->loadList($where, $order);
    foreach ($categories as &$categorie) {
		  $categories_par_chapitre[$categorie->chapitre]["$categorie->_id"] =& $categorie;
		}
  	ksort($categories_par_chapitre);
  	return $categories_par_chapitre;
  }
}

?>
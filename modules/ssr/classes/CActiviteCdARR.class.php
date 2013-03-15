<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/**
 * Activité CdARR
 */
class CActiviteCdARR extends CCdARRObject {
  var $code    = null;
  var $type    = null;
	var $libelle = null;
	var $note    = null;
	var $inclu   = null;
	var $exclu   = null;
	
  // Refs
  public $_ref_type_activite;

  // Counts
  public $_count_elements;
  public $_count_actes;
  public $_count_actes_by_executant;

  // Distant refs
	public $_ref_elements;
	public $_ref_elements_by_cat;
  public $_ref_all_executants;
	
	static $cached = array();
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table       = 'activite';
    $spec->key         = 'code';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();

    // DB Fields
    $props["code"]    = "str notNull length|4 seekable show|0";
    $props["type"]    = "str notNull length|2 seekable show|0";
    $props["libelle"] = "str notNull maxLength|250 seekable show|1";
    $props["note"]    = "text";
    $props["inclu"]   = "text";
    $props["exclu"]   = "text";
    return $props;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->code;
    $this->_shortview = $this->code;
  }
  
  function loadRefTypeActivite() {
    return $this->_ref_type_activite = CTypeActiviteCdARR::get($this->type);
  }
	
	function loadView(){
    parent::loadView();
    $this->loadRefTypeActivite();
  }

  function countElements() {
    $element = new CElementPrescriptionToCdarr();
    $element->code = $this->code;
    return $this->_count_elements = $element->countMatchingList();
  }
    
	function loadRefsElements() {
		$element = new CElementPrescriptionToCdarr();
		$element->code = $this->code;
		return $this->_ref_elements = $element->loadMatchingList();
	}
	
	function loadRefsElementsByCat() {
		foreach ($this->loadRefsElements() as $_element){
      $element = $_element->loadRefElementPrescription();
      $this->_ref_elements_by_cat[$element->category_prescription_id][] = $_element;
    }
	}
  
  function countActes() {
    $acte = new CActeCdARR();
    $acte->code = $this->code;
    return $this->_count_actes = $acte->countMatchingList();
  }
    
  function loadRefsAllExecutants() {
    // Comptage par executant
    $query = "SELECT therapeute_id, COUNT(*)
      FROM `acte_cdarr` 
      LEFT JOIN `evenement_ssr` ON  `evenement_ssr`.`evenement_ssr_id` = `acte_cdarr`.`evenement_ssr_id`
      WHERE `code` = '$this->code'
      GROUP BY `therapeute_id`";
    $acte = new CActeCdARR();  
    $ds = $acte->getDS();
    $counts = $ds->loadHashList($query);
    arsort($counts);
    
    // Chargement des executants
    $user = new CMediusers;
    $executants = $user->loadAll(array_keys($counts));
    foreach ($executants as $_executant) {
      $_executant->loadRefFunction();
    }
    
    // Valeurs de retour
    $this->_count_actes_by_executant = $counts;
    return $this->_ref_all_executants = $executants;
  }
  
 	static function getLibelle($type) {
	  $found = new self();
	  $found->type = $type;
	  $found->loadMatchingObject();
	  
	  return $found->libelle;
	}
	
	/**
	 * Get an instance from the code
	 * @param $code string
	 * @return CActiviteCdARR
	 **/
  static function get($code) {
  	if (!$code) {
  	  return new self(); 
  	}
  	
    if (!isset(self::$cached[$code])) {
      $activite = new self();
      $activite->load($code);
      self::$cached[$code] = $activite;
    }
    
    return self::$cached[$code];
  }
}

?>
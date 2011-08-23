<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Bilan d'entrée SSR
 */
class CBilanSSR extends CMbObject {
  // DB Table key
  var $bilan_id = null;
  
  // DB Fields
  var $sejour_id     = null;
  var $technicien_id = null;
	var $entree        = null;
  var $sortie        = null;
  var $planification = null;
  var $brancardage   = null;
	
  var $hospit_de_jour = null;
  var $demi_journee_1 = null;
  var $demi_journee_2 = null;
	
	// Form fields
  var $_demi_journees = null;

  // References
  var $_ref_technicien = null;

  // Distant Fields
  var $_kine_referent_id    = null;
  var $_kine_journee_id     = null;
  var $_prat_demandeur_id   = null;
  var $_sejour_demandeur_id = null;

  // Distant references
  var $_ref_kine_referent    = null;
  var $_ref_kine_journee     = null;
  var $_ref_prat_demandeur   = null;
  var $_ref_sejour_demandeur = null;
	
  /**
   * Surcharge de la spécification d'objet
   * @return CMbObjectSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "bilan_ssr";
    $spec->key   = "bilan_id";
    $spec->uniques["sejour_id"] = array("sejour_id");
    return $spec;
  }

  /**
   * Surcharge de spécifications de propriétés
   * @return array<string>
   */
  function getProps() {
    $props = parent::getProps();
		
    // DB Fields
    $props["sejour_id"    ] = "ref notNull class|CSejour show|0";
		$props["technicien_id"] = "ref class|CTechnicien";
    $props["entree"       ] = "text helped";
    $props["sortie"       ] = "text helped";
    $props["planification"] = "bool default|1";
    $props["brancardage"  ] = "bool default|0";
		
    $props["hospit_de_jour"] = "bool default|0";
    $props["demi_journee_1"] = "bool default|0";
    $props["demi_journee_2"] = "bool default|0";

    // Form fields
    $props["_demi_journees"] = "enum list|none|am|pm|all";

    // Distant Fields
    $props["_kine_referent_id" ]   = "ref class|CMediusers";
    $props["_kine_journee_id"  ]   = "ref class|CMediusers";
    $props["_prat_demandeur_id"]   = "ref class|CMediusers";
		
    return $props;
  }
	
	function updateFormFields() {
		parent::updateFormFields();
		if ($this->hospit_de_jour) {
			static $demi_journees = array(
		    "0" => array(
          "0" => "none",
          "1" => "pm",
				),
        "1" => array(
          "0" => "am",
          "1" => "all",
        ),
			);
			$this->_demi_journees = $demi_journees[$this->demi_journee_1][$this->demi_journee_2];
		}
	}
	
  /**
   * Chargement du séjour 
   * @return CSejour sejour
   */
  function loadRefSejour() {
    return $this->_ref_sejour = $this->loadFwdRef("sejour_id", true);
  }
	
  /**
   * Chargement du technicien 
   * @return CTechnicien technicien
   */
	function loadRefTechnicien() {
		return $this->_ref_technicien = $this->loadFwdRef("technicien_id", true);
	}
	
  /**
   * Chargement du kiné référent
   * @return CMediusers Kiné référent
   */
	function loadRefKineReferent() {
    $this->loadRefTechnicien();
    $technicien =& $this->_ref_technicien;
    $technicien->loadRefKine();
    $this->_ref_kine_referent = $technicien->_ref_kine;
		$this->_kine_referent_id = $this->_ref_kine_referent->_id;
		return $this->_ref_kine_referent;
	}
	
	/**
	 * Chargement du kiné référent et kiné journée pour une date donnée
	 * @param date $date Date courante if null;
	 * @return CMediusers Kiné journée
	 */
	function loadRefKineJournee($date = null) {
    $this->loadRefKineReferent();
    $this->_ref_kine_journee = $this->_ref_kine_referent;

    // Recherche d'un remplacement
		$sejour = $this->loadRefSejour();
		foreach ($sejour->loadRefReplacements() as $_replacement) {
	    if ($_replacement->_id) {
	      $_replacement->loadRefConge();
	      $conge = $_replacement->_ref_conge;
	      if (CMbRange::in(CValue::first($date, mbDate()), $conge->date_debut, $conge->date_fin)) {
	        $_replacement->loadRefReplacer();
	        $replacer =& $_replacement->_ref_replacer;
	        $replacer->loadRefFunction();
	        $this->_ref_kine_journee = $_replacement->_ref_replacer;
					break;
	      }
	    }
		}
		
		$this->_kine_journee_id = $this->_ref_kine_journee->_id;
		return $this->_ref_kine_journee;
	}

  /**
   * Chargement du séjour probablement demandeur du séjour SSR
   * (dont la sortie est proche de l'entree du séjour SSR)
   * @return CSejour
   */
  function loadRefSejourDemandeur() {
  	// Effet de cache
  	if ($this->_ref_sejour_demandeur) {
  		return;
  	}
		
		// Requête
  	$sejour_ssr = $this->loadRefSejour();
		$tolerance = CAppUI::conf("ssr CBilanSSR tolerance_sejour_demandeur");
		$date_min = mbDate("- $tolerance DAYS", $sejour_ssr->entree);
    $date_max = mbDate("+ $tolerance DAYS", $sejour_ssr->entree);
		$where["sortie"]     = "BETWEEN '$date_min' AND '$date_max'";
		$where["patient_id"] = "= '$sejour_ssr->patient_id'";
    $where["annule"]     = " = '0'";
    $where["type"]       = " != 'ssr'";
		
		// Chargement
		$sejour = new CSejour;
		$sejour->loadObject($where);
    return $this->_ref_sejour_demandeur = $sejour;
  }

  /**
   * Chargement du praticien demandeur sur la base du séjour demandeur
   * @return CMediusers
   */
  function loadRefPraticienDemandeur() {
  	$sejour = $this->loadRefSejourDemandeur();
		$praticien = $sejour->loadRefPraticien(1);
		
		$this->_prat_demandeur_id = $praticien->_id;
		return $this->_ref_prat_demandeur = $praticien;
  }


	/**
	 * Load Sejour for technicien at a date
	 * @return array<CSejour>
	 **/ 
	static function loadSejoursSSRfor($technicien_id, $date, $show_cancelled_services = true) {
    $group = CGroups::loadCurrent();

		// Masquer les services inactifs
		if (!$show_cancelled_services) {
		  $service = new CService;
		  $service->group_id = $group->_id;
		  $service->cancelled = "1";
		  $services = $service->loadMatchingList();
		  $where["service_id"] = CSQLDataSource::prepareNotIn(array_keys($services));
		}
		
	  $where["type"] = "= 'ssr'";
	  $where["group_id"] = "= '$group->_id'";
	  $where["annule"] = "= '0'";
	  $where["bilan_ssr.technicien_id"] = $technicien_id ? "= '$technicien_id'" : "IS NULL";
	  $leftjoin["bilan_ssr"] = "bilan_ssr.sejour_id = sejour.sejour_id";
	  return CSejour::loadListForDate($date, $where, "entree_reelle", null, null, $leftjoin);
	}
}


?>
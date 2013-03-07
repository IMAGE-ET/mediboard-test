<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Résumé Hébdomadaire Simplifié
 * Correspond à une cotation d'actes de réadaptation pour une semaine
 */
class CRHS extends CMbObject {
  static $days = array(
    "1" => "mon",
    "2" => "tue",
    "3" => "wed",
    "4" => "thu",
    "5" => "fri",
    "6" => "sat",
    "7" => "sun",
  );
     
  // DB Table key
  var $rhs_id = null;
 
  // DB Fields
  var $sejour_id   = null;
  var $date_monday = null;
  var $facture     = null;
  
	// Form Field
	var $_date_tuesday   = null;
	var $_date_wednesday = null;
	var $_date_thursday  = null;
	var $_date_friday    = null;
	var $_date_saturday  = null;
	var $_date_sunday    = null;
	var $_week_number    = null;
	
	// Distant fields
  var $_in_bounds     = null;
  var $_in_bounds_mon = null;
  var $_in_bounds_tue = null;
  var $_in_bounds_wed = null;
  var $_in_bounds_thu = null;
  var $_in_bounds_fri = null;
  var $_in_bounds_sat = null;
  var $_in_bounds_sun = null;
	
  // Object References
  var $_ref_sejour           = null;
  var $_ref_dependances      = null;
  var $_ref_dependances_chonology = null;
  var $_ref_lignes_activites = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "rhs";
    $spec->key   = "rhs_id";
    $spec->uniques["rhs"] = array("sejour_id", "date_monday");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
		
    // DB Fields
    $props["sejour_id"]     = "ref notNull class|CSejour";
    $props["date_monday"]   = "date notNull";
    $props["facture"]       = "bool default|0";

    // Form Field
    $props["_date_tuesday"]   = "date";
    $props["_date_wednesday"] = "date";
    $props["_date_thursday"]  = "date";
    $props["_date_friday"]    = "date";
    $props["_date_saturday"]  = "date";
    $props["_date_sunday"]    = "date";
    $props["_week_number"]    = "num min|0 max|52";

    // Remote fields
    $props["_in_bounds"]     = "bool";
    $props["_in_bounds_mon"] = "bool";
    $props["_in_bounds_tue"] = "bool";
    $props["_in_bounds_wed"] = "bool";
    $props["_in_bounds_thu"] = "bool";
    $props["_in_bounds_fri"] = "bool";
    $props["_in_bounds_sat"] = "bool";
    $props["_in_bounds_sun"] = "bool";
		
    return $props;
  }
 
	function getBackProps() {
	  $backProps = parent::getBackProps();
	  $backProps["lines"]       = "CLigneActivitesRHS rhs_id";
	  $backProps["dependances"] = "CDependancesRHS rhs_id";
	  return $backProps;
	}
  
	function check() {
		if ($this->date_monday && CMbDT::transform(null, $this->date_monday, "%w") != "1") {
			return CAppUI::tr("CRHS-failed-monday", $this->date_monday);
		}
    return parent::check();
	}
	
	function updateFormFields() {
		parent::updateFormFields();
		$this->_week_number = CMbDT::transform(null, $this->date_monday, "%U");
      
    $this->_date_tuesday   = CMbDT::date("+1 DAY", $this->date_monday);
    $this->_date_wednesday = CMbDT::date("+2 DAY", $this->date_monday);
    $this->_date_thursday  = CMbDT::date("+3 DAY", $this->date_monday);
    $this->_date_friday    = CMbDT::date("+4 DAY", $this->date_monday);
    $this->_date_saturday  = CMbDT::date("+5 DAY", $this->date_monday);
    $this->_date_sunday    = CMbDT::date("+6 DAY", $this->date_monday);
    
		$this->_view = CAppUI::tr("Week") . " $this->_week_number";
	}

  function loadRefSejour() {
  	$this->_ref_sejour = $sejour = $this->loadFwdRef("sejour_id", true);
  	$sejour->loadRefPatient();
    
  	$this->_in_bounds = 
		  $this->date_monday <= CMbDT::date(null, $sejour->_sortie) &&
      $this->_date_sunday >= CMbDT::date(null, $sejour->_entree);
      
    $this->_in_bounds_mon = 
		  $this->date_monday <= CMbDT::date($sejour->_sortie) &&
      $this->date_monday >= CMbDT::date($sejour->_entree);
    
    $this->_in_bounds_tue = 
		  $this->_date_tuesday <= CMbDT::date($sejour->_sortie) &&
      $this->_date_tuesday >= CMbDT::date($sejour->_entree);
    
    $this->_in_bounds_wed = 
		  $this->_date_wednesday <= CMbDT::date($sejour->_sortie) &&
      $this->_date_wednesday >= CMbDT::date($sejour->_entree);
    
    $this->_in_bounds_thu = 
		  $this->_date_thursday <= CMbDT::date($sejour->_sortie) &&
      $this->_date_thursday >= CMbDT::date($sejour->_entree);
    
    $this->_in_bounds_fri = 
		  $this->_date_friday <= CMbDT::date($sejour->_sortie) &&
      $this->_date_friday >= CMbDT::date($sejour->_entree);
    
    $this->_in_bounds_sat = 
		  $this->_date_saturday <= CMbDT::date($sejour->_sortie) &&
      $this->_date_saturday >= CMbDT::date($sejour->_entree);
    
    $this->_in_bounds_sun = 
		  $this->_date_sunday <= CMbDT::date($sejour->_sortie) &&
      $this->_date_sunday >= CMbDT::date($sejour->_entree);
			
		return $this->_ref_sejour;
  }
	
	/**
	 * Get all possible and existing RHS for given sejour, by date as keys
	 * @param CSejour $sejour
	 * @return array[CRHS], null if not applyable
	 */
	static function getAllRHSsFor(CSejour $sejour) {
		if (!$sejour->_id || $sejour->type != "ssr") {
			return;
		}
			
	  $rhss = array();
    foreach ($sejour->loadBackRefs("rhss") as $_rhs) {
    	$rhss[$_rhs->date_monday] = $_rhs;
    }
    
		for (
		  $date_monday = CMbDT::date("last sunday + 1 day", $sejour->_entree);
			$date_monday <= $sejour->_sortie;
			$date_monday = CMbDT::date("+1 week", $date_monday)
		) {
			if (!isset($rhss[$date_monday])) {
				$rhs = new CRHS;
				$rhs->sejour_id = $sejour->_id;
				$rhs->date_monday = $date_monday;
				$rhs->updateFormFields();
				$rhss[$date_monday] = $rhs;
			}
		}
		
		ksort($rhss);
		
		return $rhss;
	}

  function loadRefDependances() {
    if ($this->_ref_dependances) {
      return $this->_ref_dependances;
    }
    
    $order = "dependances_id ASC";
    $this->_ref_dependances = new CDependancesRHS();
    $this->_ref_dependances->rhs_id = $this->_id;
    $this->_ref_dependances->loadMatchingObject($order);
    return $this->_ref_dependances;
  }
  
  function loadDependancesChronology(){
    $sejour = $this->loadRefSejour();
    $all_rhs = CRHS::getAllRHSsFor($sejour);
    
		$empty = new CDependancesRHS;
		$empty->habillage = 0;
    $empty->deplacement = 0;
    $empty->alimentation = 0;
    $empty->continence = 0;
    $empty->comportement = 0;
    $empty->relation = 0;
		
    $chrono = array(
      "-2" => $empty,
      "-1" => $empty,
      "+0" => $empty,
      "+1" => $empty,
      "+2" => $empty,
    );
    
    foreach($chrono as $ref => &$dep) {
      $date = CMbDT::date("$ref WEEKS", $this->date_monday);
      
      if (array_key_exists($date, $all_rhs)) {
      	$_rhs = $all_rhs[$date];
        $_rhs->loadRefDependances();
        $dep = $_rhs->_ref_dependances;
      }
    }
    
    return $this->_ref_dependances_chonology = $chrono;
  }
  
  function loadRefLignesActivites() {
    if ($this->_ref_lignes_activites) {
      return;
    }
    
    $ligneActivitesRHS = new CLigneActivitesRHS();
    $ligneActivitesRHS->rhs_id = $this->_id;
    $this->_ref_lignes_activites = $ligneActivitesRHS->loadMatchingList();
  }
  
  function countTypeActivite() {
    $totaux = array();
    
    $type_activite = new CTypeActiviteCdARR();
    $types_activite = $type_activite->loadList();
    foreach($types_activite as $_type) {
      $totaux[$_type->code] = 0;
    }
    
    $this->loadRefLignesActivites();
    $lines = $this->_ref_lignes_activites;
    foreach($lines as $_line) {
      $_line->loadRefActiviteCdARR();
      $_line->_ref_activite_cdarr->loadRefTypeActivite();
      $type_activite = $_line->_ref_activite_cdarr->_ref_type_activite;
      $totaux[$type_activite->code] += $_line->_qty_total;
    }
    
    return $totaux;
  }
  
  function recalculate() {
    // Suppression des lignes d'activités du RHS
    $this->loadBackRefs("lines");
    foreach($this->_back["lines"] as $_line) {
      if ($_line->auto) {
        $_line->delete();
      }
    }
    $this->loadBackRefs("lines");
    
    // Chargement du séjour
    $sejour = $this->loadRefSejour();
    
    // Ajout des lignes d'activités 
    $evenementSSR = new CEvenementSSR();
    $evenementSSR->sejour_id = $sejour->_id;
    $evenementSSR->realise = 1;
    $evenements = $evenementSSR->loadMatchingList();

    foreach ($evenements as $_evenement) {
      $evenementRhs = $_evenement->getRHS();
      if ($evenementRhs->_id != $this->_id) {
        continue;
      }
      
      $therapeute = $_evenement->loadRefTherapeute();
      $intervenant = $therapeute->loadRefIntervenantCdARR();
      $code_intervenant_cdarr = $intervenant->code;
      $actes_cdarr =$_evenement->loadRefsActesCdARR();
      foreach ($actes_cdarr as $_acte_cdarr) {
        $ligne = new CLigneActivitesRHS();
        $ligne->rhs_id                 = $this->_id;
        $ligne->executant_id           = $therapeute->_id;
        $ligne->code_activite_cdarr    = $_acte_cdarr->code;
        $ligne->code_intervenant_cdarr = $code_intervenant_cdarr;
        $ligne->loadMatchingObject();
        $ligne->crementDay($_evenement->debut, "inc");
        $ligne->auto = "1";
        $ligne->store();
      }
    }  
    
    // Gestion des administrations
    foreach ($sejour->loadBackRefs("actes_cdarr") as $_acte_cdarr_adm){
      $_acte_cdarr_adm->loadRefAdministration();
      $administration = $_acte_cdarr_adm->_ref_administration;
      $administration->loadRefAdministrateur();
      $therapeute = $_evenement->loadRefTherapeute();
      
      $ligne = new CLigneActivitesRHS();
      $ligne->rhs_id                 = $this->_id;
      $ligne->executant_id           = $therapeute->_id;
      $ligne->code_activite_cdarr    = $_acte_cdarr_adm->code;
      $ligne->code_intervenant_cdarr = $code_intervenant_cdarr;
      $ligne->loadMatchingObject();
      $ligne->crementDay($administration->dateTime, "inc");
      $ligne->auto = "1";
      $ligne->store();
    }
  }
  
  function buildTotaux() {
    // Initialisation des totaux
  	$totaux = array();
    $type_activite = new CTypeActiviteCdARR();
    $types_activite = $type_activite->loadList();
    foreach ($types_activite as $_type) {
      $totaux[$_type->code] = 0;
    }
	
    // Comptage et classement par executants
    $executants = array();
    $lines_by_executant = array();
    foreach ($this->loadBackRefs("lines") as $_line) {
      $activite = $_line->loadRefActiviteCdARR();
      $type = $activite->loadRefTypeActivite();
      $totaux[$type->code] += $_line->_qty_total;
      $_line->loadRefIntervenantCdARR();
      $executant = $_line->loadFwdRef("executant_id", true);
      $executant->loadRefsFwd();
      $executant->loadRefIntervenantCdARR();
      
      // Use guids for keys instead of ids to prevent key corruption by multisorting
      $executants[$executant->_guid] = $executant;
      $lines_by_executant[$executant->_guid][] = $_line;
    }

    // Sort by executants then by code
    array_multisort(CMbArray::pluck($executants, "_view"), SORT_ASC, $lines_by_executant);
    foreach ($lines_by_executant as &$_lines) {
      array_multisort(CMbArray::pluck($_lines, "code_activite_cdarr"), SORT_ASC, $_lines);
    }
    
    $this->_ref_lines_by_executant = $lines_by_executant;
    $this->_ref_executants         = $executants;
    $this->_ref_types_activite     = $types_activite;
    return $this->_totaux = $totaux;
  }
}

?>
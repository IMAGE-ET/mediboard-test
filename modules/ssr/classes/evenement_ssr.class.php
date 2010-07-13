<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CEvenementSSR extends CMbObject {
  // DB Table key
	var $evenement_ssr_id        = null;
	
	// DB Fields
	var $prescription_line_element_id = null;
	var $sejour_id               = null;
	var $debut                   = null; // DateTime
	var $duree                   = null; // Durée en minutes
	var $therapeute_id           = null;
	var $equipement_id           = null;
  var $realise                 = null;
	var $remarque                = null;
	
	// Seances collectives
	var $seance_collective_id    = null; // Evenement lié a une seance collective
	var $_ref_element_prescription = null;
  //var $element_prescription_id = null; // Une seance est liée à un element de prescription et non pas une ligne d'element
	var $_ref_seance_collective = null;
	
	
	// Form Fields
	var $_heure                   = null;
	var $_nb_decalage_min_debut   = null;
	var $_nb_decalage_heure_debut = null;
  var $_nb_decalage_jour_debut  = null;
  var $_nb_decalage_duree       = null;
	
	var $_ref_equipement        = null;
	var $_ref_sejour            = null;
	var $_ref_therapeute        = null;
	var $_ref_actes_cdarr       = null;
	var $_ref_evenements_seance = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table       = 'evenement_ssr';
    $spec->key         = 'evenement_ssr_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["prescription_line_element_id"] = "ref class|CPrescriptionLineElement";
		$props["sejour_id"]     = "ref class|CSejour show|0";
    $props["debut"]         = "dateTime show|0";
    $props["duree"]         = "num min|0";
		$props["therapeute_id"] = "ref class|CMediusers";
		$props["equipement_id"] = "ref class|CEquipement";
		$props["realise"]       = "bool default|0";
		$props["remarque"]      = "str";
		$props["seance_collective_id"] = "ref class|CEvenementSSR";
		$props["_heure"]        = "time";
    $props["_nb_decalage_min_debut"]   = "num";
		$props["_nb_decalage_heure_debut"] = "num";
    $props["_nb_decalage_jour_debut"]  = "num";
		$props["_nb_decalage_duree"]   = "num";
    return $props;
  }
	
	function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["actes_cdarr"] = "CActeCdARR evenement_ssr_id";
		$backProps["evenements_ssr"] = "CEvenementSSR seance_collective_id";
    return $backProps;
  }
	
	function check(){
		// Vérouillage d'un événement réalisé
    $this->completeField("realise");
		if ($this->realise && !$this->fieldModified("realise")) {
			return "Evénément déjà réalisé";
		}

    // Evénement dans les bornes du séjour
    $this->completeField("sejour_id");
    $this->loadRefSejour();
		
    $this->completeField("debut");
		
		if ($this->sejour_id && $this->debut && ($this->debut < $this->_ref_sejour->entree || $this->debut > $this->_ref_sejour->sortie)){
		  return "Evenement SSR en dehors des dates du séjour";
		}
    
		// Cas de la réalisation des événements SSR
	  $this->loadRefTherapeute();
		
		// Si le therapeute n'est pas defini, c'est 
		if($this->therapeute_id){
		  $therapeute = $this->_ref_therapeute;
    } else {
			// Chargement du therapeute de la seance
      $evt_seance = new CEvenementSSR();
      $evt_seance->load($this->seance_collective_id);
      $evt_seance->loadRefTherapeute();
		  $therapeute = $evt_seance->_ref_therapeute;
    }
		
	  if ($this->fieldModified("realise")) {
		  // Si le thérapeute n'a pas d'identifiant CdARR
		  if (!$therapeute->code_intervenant_cdarr) {
		    return CAppUI::tr("CMediusers-code_intervenant_cdarr-none");
		  }
		  $therapeute->loadRefCodeIntervenantCdARR();
      $code_intervenant_cdarr = $therapeute->_ref_code_intervenant_cdarr->code;
      
			
			// Création du RHS au besoins
		  $rhs = $this->getRHS();
      if (!$rhs->_id) {
        $rhs->store();
      }
      
			// Complétion de la ligne RHS    
      $this->loadRefsActesCdARR();
      foreach ($this->_ref_actes_cdarr as $_acte_cdarr) {
        $ligne_activite_rhs = new CLigneActivitesRHS();
        $where["rhs_id"]                 = "= '$rhs->_id'";
        $where["executant_id"]           = "= '$therapeute->_id'";
        $where["code_activite_cdarr"]    = "= '$_acte_cdarr->code'";
        $where["code_intervenant_cdarr"] = "= '$code_intervenant_cdarr'";
        $ligne_activite_rhs->loadObject($where);
        
        $this->realise ? $ligne_activite_rhs->incrementOrDecrementDay($this->debut, "inc") : $ligne_activite_rhs->incrementOrDecrementDay($this->debut, "dec");
        
        if (!$ligne_activite_rhs->_id) {
          $ligne_activite_rhs->rhs_id                 = $rhs->_id;
          $ligne_activite_rhs->executant_id           = $therapeute->_id;
          $ligne_activite_rhs->code_activite_cdarr    = $_acte_cdarr->code;
          $ligne_activite_rhs->code_intervenant_cdarr = $code_intervenant_cdarr;
        }
        
        $ligne_activite_rhs->store();
      }
		}
		
		return parent::check();
	}
	
	function loadView() {
		parent::loadView();
		$this->loadRefSejour();
		$sejour =& $this->_ref_sejour;
		$sejour->loadRefPatient();
		$patient = $sejour->_ref_patient;
		$this->_view = "$patient->_view - ". mbTransformTime(null, $this->debut, CAppUI::conf("datetime"));
		$this->loadRefsActesCdARR();
		
		if(!$this->sejour_id){
		  $this->loadRefsEvenementsSeance();
			foreach($this->_ref_evenements_seance as $_evt_seance){
				$_evt_seance->loadRefSejour();
				$_evt_seance->_ref_sejour->loadRefPatient();
			}
		}
	}
	
	function loadRefPrescriptionLineElement($cache = true){
		$this->_ref_prescription_line_element = $this->loadFwdRef("prescription_line_element_id", $cache);
		$this->_ref_prescription_line_element->loadRefElement();
	}
	
	function loadRefSejour($cache = true){
		$this->_ref_sejour = $this->loadFwdRef("sejour_id", $cache);
	}
	
	function loadRefEquipement($cache = true){
		$this->_ref_equipement = $this->loadFwdRef("equipement_id", $cache);
	}
	
	function loadRefTherapeute($cache = true){
	  $this->_ref_therapeute = $this->loadFwdRef("therapeute_id", $cache);
	}
	
	function loadRefSeanceCollective($cache = true){
    $this->_ref_seance_collective = $this->loadFwdRef("seance_collective_id", $cache);
  }
  
	function loadRefsActesCdARR(){
		$this->_ref_actes_cdarr = $this->loadBackRefs("actes_cdarr");
	}
	
	function loadRefsEvenementsSeance(){
		$this->_ref_evenements_seance = $this->loadBackRefs("evenements_ssr");
	}
	
	function getRHS() {
	  $rhs = new CRHS();
    $rhs->sejour_id = $this->sejour_id;
    $rhs->date_monday = mbDate("last monday", mbDate("+1 day", mbDate($this->debut)));
    $rhs->loadMatchingObject();
    
    return $rhs;
	}
	
  static function getNbJoursPlanning($user_id, $date){
    $sunday = mbDate("next sunday", mbDate("- 1 DAY", $date));
    $saturday = mbDate("-1 DAY", $sunday);
    
    $_evt = new CEvenementSSR();
    $where = array();
    $where["debut"] = "BETWEEN '$sunday 00:00:00' AND '$sunday 23:59:59'";
    $where["therapeute_id"] = " = '$user_id'";
    $count_event_sunday = $_evt->countList($where);
    
    $nb_days = 7;
    
    // Si aucun evenement le dimanche
    if(!$count_event_sunday){
      $nb_days = 6;
      $where["debut"] = "BETWEEN '$saturday 00:00:00' AND '$saturday 23:59:59'";
      $count_event_saturday= $_evt->countList($where);  
      // Aucun evenement le samedi et aucun le dimanche
      if(!$count_event_saturday){
        $nb_days = 5;
      }
    }
    return $nb_days;
  }
}

?>
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
	var $element_prescription_id = null;
	var $sejour_id               = null;
	var $debut                   = null; // DateTime
	var $duree                   = null; // Durée en minutes
	var $therapeute_id           = null;
	var $equipement_id           = null;
  var $realise                 = null;
	var $remarque                = null;
	
	// Form Fields
	var $_heure                  = null;
	var $_nb_decalage_min_debut = null;
	var $_nb_decalage_heure_debut = null;
  var $_nb_decalage_jour_debut = null;
  var $_nb_decalage_duree = null;
	
	var $_ref_element_prescription = null;
	var $_ref_equipement = null;
	var $_ref_sejour = null;
	var $_ref_therapeute = null;
	var $_ref_actes_cdarr = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table       = 'evenement_ssr';
    $spec->key         = 'evenement_ssr_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["element_prescription_id"] = "ref notNull class|CElementPrescription";
    $props["sejour_id"]     = "ref notNull class|CSejour";
    $props["debut"]         = "dateTime notNull";
    $props["duree"]         = "num notNull min|0";
		$props["therapeute_id"] = "ref notNull class|CMediusers";
		$props["equipement_id"] = "ref class|CEquipement";
		$props["realise"]       = "bool default|0";
		$props["remarque"]      = "str";
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
    return $backProps;
  }
	
	function check(){
		$this->completeField("sejour_id");
		$this->completeField("debut");
    
		$this->loadRefSejour();
		
		if ($this->debut < $this->_ref_sejour->entree || $this->debut > $this->_ref_sejour->sortie){
		  return "Evenement SSR en dehors des dates du séjour";
		}
    
		// Cas de la réalisation des événements SSR
	  $this->loadRefTherapeute();
	  $therapeute = $this->_ref_therapeute;
		if ($this->fieldModified("realise")) {
		  // Si le thérapeute n'a pas d'identifiant CdARR
		  if (!$therapeute->code_intervenant_cdarr) {
		    return CAppUI::tr("CMediusers-code_intervenant_cdarr-none");
		  }
		  $therapeute->loadRefCodeIntervenantCdARR();
      $code_intervenant_cdarr = $therapeute->_ref_code_intervenant_cdarr->code;
      
      $this->loadRefsActesCdARR();
      $actes_cdarr = $this->_ref_actes_cdarr;
		  $rhs = $this->getRHS();
      if (!$rhs->_id) {
        $rhs->store();
      }
          
      foreach ($actes_cdarr as $_acte_cdarr) {
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
	}
	
	function loadRefElementPrescription($cache = true) {
    $this->_ref_element_prescription = $this->loadFwdRef("element_prescription_id", $cache); 
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
	
	function loadRefsActesCdARR(){
		$this->_ref_actes_cdarr = $this->loadBackRefs("actes_cdarr");
	}
	
	function getRHS() {
	  $rhs = new CRHS();
    $rhs->sejour_id = $this->sejour_id;
    $rhs->date_monday = mbDate("last monday", mbDate("+1 day", mbDate($this->debut)));
    $rhs->loadMatchingObject();
    
    return $rhs;
	}
}

?>
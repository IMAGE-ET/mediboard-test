<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision$
 *  @author Thomas Despoix
*/

/**
 * Classe CChambre. 
 * @abstract Gère les chambre d'hospitalisation
 * - contient des lits
 */
class CChambre extends CMbObject {
  // DB Table key
	var $chambre_id = null;	
  
  // DB References
  var $service_id = null;

  // DB Fields
  var $nom              = null;
  var $caracteristiques = null; // côté rue, fenêtre, lit accompagnant, ...
  var $annule           = null;

  // Form Fields
  var $_nb_lits_dispo        = null;
  var $_overbooking          = null;
  var $_ecart_age            = null;
  var $_genres_melanges      = null;
  var $_chambre_seule        = null;
  var $_chambre_double       = null;
  var $_conflits_chirurgiens = null;
  var $_conflits_pathologies = null;

  // Object references
  var $_ref_service = null;
  var $_ref_lits    = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'chambre';
    $spec->key   = 'chambre_id';
    $spec->measureable = true;
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["lits"] = "CLit chambre_id";
    return $backProps;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["service_id"]       = "ref notNull class|CService seekable";
    $specs["nom"]              = "str notNull seekable";
    $specs["caracteristiques"] = "text confidential";
    $specs["annule"]           = "bool";
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
	
	function loadRefService() {
		return $this->_ref_service = $this->loadFwdRef("service_id", true);
	}
  
  function loadRefsFwd() {
    $this->loadRefService();
  }

  function loadRefsLits() {
    $order = "lit.nom DESC";
    return $this->_ref_lits = $this->loadBackRefs("lits", $order);
  }

  function loadRefsBack() {
  	$this->loadRefsLits();
  }
  
  function getPerm($permType) {
    $this->loadRefService();
    return ($this->_ref_service->getPerm($permType));
  }
  
  function checkChambre() {
    static $pathos = null;
    if (!$pathos) {
      $pathos = new CDiscipline();
    }
    
    assert($this->_ref_lits !== null);
    $this->_nb_lits_dispo = count($this->_ref_lits);
    
    $listAff = array();
    
    $this->_chambre_seule = 0;
    $this->_chambre_double = 0;
    $this->_conflits_pathologies = 0;
    $this->_ecart_age = 0;
    $this->_genres_melanges = false;
    $this->_conflits_chirurgiens = 0;

    foreach ($this->_ref_lits as $key => $lit) {
      assert($lit->_ref_affectations !== null);

      // overbooking
      $lit->checkOverBooking();
      $this->_overbooking += $lit->_overbooking;

      // Lits dispo
      if (count($lit->_ref_affectations)) {
        $this->_nb_lits_dispo--;
      }
      
      // Liste des affectations
      foreach($lit->_ref_affectations as $key2 => $aff)
        $listAff[] =& $this->_ref_lits[$key]->_ref_affectations[$key2];
    }

    foreach ($listAff as $affectation1) {
      if(!$affectation1->sejour_id){
        continue;
      }
      $sejour1 =& $affectation1->_ref_sejour;
      $patient1 =& $sejour1->_ref_patient;
      $chirurgien1 =& $sejour1->_ref_praticien;
      if ((count($this->_ref_lits) == 1) && $sejour1->chambre_seule == 0)
        $this->_chambre_double++;
      if ((count($this->_ref_lits) > 1) && $sejour1->chambre_seule == 1)
        $this->_chambre_seule++;
      
      foreach ($listAff as $affectation2) {
        if(!$affectation2->sejour_id){
          continue;
        }
        if ($affectation1->affectation_id == $affectation2->affectation_id) {
          continue;
        }
        
        if ($affectation1->lit_id == $affectation2->lit_id) {
          continue;
        }
        
        if (!$affectation1->colide($affectation2)) {
          continue;
        }
        
        $sejour2 =& $affectation2->_ref_sejour;
        $patient2 =& $sejour2->_ref_patient;
        $chirurgien2 =& $sejour2->_ref_praticien;

        // Conflits de pathologies
        if (!$pathos->isCompat($sejour1->pathologie, $sejour2->pathologie, $sejour1->septique, $sejour2->septique))
          $this->_conflits_pathologies++;

        // Ecart d'âge
        $ecart = max($patient1->_age, $patient2->_age) - min($patient1->_age, $patient2->_age);
        $this->_ecart_age = max($ecart, $this->_ecart_age);

        // Genres mélangés
        if (($patient1->sexe != $patient2->sexe) && (($patient1->sexe == "m") || ($patient2->sexe == "m")))
          $this->_genres_melanges = true;
      
        // Conflit de chirurgiens
        if (($chirurgien1->user_id != $chirurgien2->user_id) && ($chirurgien1->function_id == $chirurgien2->function_id))
           $this->_conflits_chirurgiens++;
      }
    }
    $this->_conflits_pathologies /= 2;
    $this->_conflits_chirurgiens /= 2;
  }
}
  
?>
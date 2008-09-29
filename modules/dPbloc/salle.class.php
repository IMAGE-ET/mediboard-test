<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPbloc
 *	@version $Revision$
 *  @author Romain Ollivier
 */

/**
 * The CSalle class
 */
class CSalle extends CMbObject {
  // DB Table key
	var $salle_id = null;
  
  // DB references
  var $group_id = null;
	
  // DB Fields
  var $nom   = null;
  var $stats = null;
  
  var $_ref_group = null;
  
  // Object references per day
  var $_ref_plages = null;
  var $_ref_urgences = null;
  var $_ref_deplacees = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'sallesbloc';
    $spec->key   = 'salle_id';
    return $spec;
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["operations"] = "COperation salle_id";
    $backRefs["plages_op"] = "CPlageOp salle_id";
    return $backRefs;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["group_id"] = "notNull ref class|CGroups";
    $specs["nom"]      = "notNull str";
    $specs["stats"]    = "notNull bool";
    return $specs;
  }
  
  function getSeeks() {
    return array (
      "nom" => "like"
    );
  }
  
  function updateFormFields() {
    $this->_view = $this->nom;
  }
  
  function loadRefsFwd(){
    // Chargement de l'établissement correspondant
    $this->_ref_group = new CGroups;
    $this->_ref_group->load($this->group_id);
  }
  
  /**
   * Charge la liste de plages et opérations pour un jour donné
   * Analogue à CMediusers::loadRefsForDay
   * @param $date date Date to look for
   */
  function loadRefsForDay($date) {
    // Plages d'opérations
	  $plages = new CPlageOp;
	  $where = array();
	  $where["date"] = "= '$date'";
	  $where["salle_id"] = "= '$this->_id'";
	  $order = "debut";
		$this->_ref_plages = $plages->loadList($where, $order);
		foreach ($this->_ref_plages as &$plage) {
		  $plage->loadRefs(0);
		  $plage->_unordered_operations = array();
		  foreach ($plage->_ref_operations as &$operation) {
		    $operation->loadRefPatient();
		    $operation->loadExtCodesCCAM();
		    $operation->updateSalle();

		    if(CAppUI::conf("dPbloc CPlageOp chambre_operation")) {
		    	$operation->loadRefAffectation();
		    }
		    
		    // Extraire les interventions non placées
		    if ($operation->rank == 0) {
		      $plage->_unordered_operations[$operation->_id] = $operation;
		      unset($plage->_ref_operations[$operation->_id]);
		    }
		  }
		}
		
		// Interventions déplacés
		$deplacees = new COperation;
		$ljoin = array();
		$ljoin["plagesop"] = "operations.plageop_id = plagesop.plageop_id";
		$where = array();
		$where["operations.plageop_id"] = "IS NOT NULL";
		$where["plagesop.salle_id"]     = "!= operations.salle_id";
		$where["plagesop.date"]         = "= '$date'";
		$where["operations.salle_id"]   = "= '$this->_id'";
		$order = "operations.time_operation";
		$this->_ref_deplacees = $deplacees->loadList($where, $order, null, null, $ljoin);
		foreach ($this->_ref_deplacees as &$deplacee) {
		  $deplacee->loadRefChir();
		  $deplacee->loadRefPatient();
		  $deplacee->loadExtCodesCCAM();
		}

		// Urgences
	  $urgences = new COperation;
	  $where = array();
	  $where["date"]     = "= '$date'";
	  $where["salle_id"] = "= '$this->_id'";
	  $order = "chir_id";
	  $this->_ref_urgences = $urgences->loadList($where);
	  foreach($this->_ref_urgences as &$urgence) {
	    $urgence->loadRefChir();
	    $urgence->loadRefPatient();
	    $urgence->loadExtCodesCCAM();
	  }
  }
}
?>
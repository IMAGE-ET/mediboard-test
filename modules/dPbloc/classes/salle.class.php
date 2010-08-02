<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSalle extends CMbObject {
  // DB Table key
	var $salle_id = null;
  
  // DB references
  var $bloc_id = null;
	
  // DB Fields
  var $nom   = null;
  var $stats = null;
  
  var $_ref_bloc = null;
  
  // Object references per day
  var $_ref_plages = null;
  var $_ref_urgences = null;
  var $_ref_deplacees = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'sallesbloc';
    $spec->key   = 'salle_id';
    $spec->measureable = true;
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["operations"]  = "COperation salle_id";
    $backProps["plages_op"]   = "CPlageOp salle_id";
    $backProps["check_lists"] = "CDailyCheckList object_id";
    return $backProps;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["bloc_id"] = "ref notNull class|CBlocOperatoire";
    $specs["nom"]     = "str notNull seekable";
    $specs["stats"]   = "bool notNull";
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefBloc();
    $bloc = &$this->_ref_bloc;
    
    $where = array(
      'group_id' => "= '$bloc->group_id'"
    );
    $this->_view = '';
    if ($bloc->countList($where) > 1) {
    	$this->_view = $bloc->nom.' - ';
    }
    $this->_view .= $this->nom;
    $this->_shortview = $this->nom;
  }
  
  /**
   * Load list overlay for current group
   */
  function loadGroupList($where = array(), $order = 'bloc_id, nom', $limit = null, $groupby = null, $ljoin = array()) {
  	$list_blocs = CGroups::loadCurrent()->loadBlocs(PERM_READ, false);
  	
    // Filtre sur l'établissement
		$where[] = "bloc_id ".CSQLDataSource::prepareIn(array_keys($list_blocs));
    
    return $this->loadList($where, $order, $limit, $groupby, $ljoin);
  }
  
  function getPerm($permType) {
  	$this->loadRefBloc();
  	return $this->_ref_bloc->getPerm($permType) && parent::getPerm($permType);
  }
  
  function loadRefBloc(){
  	if (!$this->_ref_bloc) {
	    $bloc = new CBlocOperatoire();
	    $this->_ref_bloc = $bloc->getCached($this->bloc_id);
    }
  }
  
  function loadRefsFwd(){
    $this->loadRefBloc();
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
      $plage->loadRefsNotes();
		  $plage->_unordered_operations = array();
		  foreach ($plage->_ref_operations as &$operation) {
		    $operation->loadRefChir();
		    $operation->loadRefPatient();
		    $operation->loadExtCodesCCAM();
		    $operation->loadRefPlageOp();
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
		  $deplacee->loadRefPlageOp();
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
		  $urgence->loadRefPlageOp();
	  }
  }
  
  function loadRefsAlertesIntervs() {
    $alerte = new CAlert();
    $ljoin = array();
    $ljoin["operations"] = "operations.operation_id = alert.object_id";
    $ljoin["plagesop"]   = "plagesop.plageop_id = operations.plageop_id";
    $where = array();
    $where["alert.object_class"] = "= 'COperation'";
    $where["alert.tag"] = "= 'mouvement_intervention'";
    $where["alert.handled"]   = "= '0'";
    $where[] = "operations.salle_id = '$this->salle_id' OR plagesop.salle_id = '$this->salle_id' OR (plagesop.salle_id IS NULL AND operations.salle_id IS NULL)";
    $order = "operations.date, operations.chir_id";
    return $this->_alertes_intervs = $alerte->loadList($where, $order, null, null, $ljoin);
  }
}
?>
<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CBlocOperatoire extends CMbObject {
  // DB Table key
	var $bloc_operatoire_id = null;
  
  // DB references
  var $group_id   = null;
	
  // DB Fields
  var $nom        = null;
  
  var $_ref_group = null;
  
  // Object references
  var $_ref_salles = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'bloc_operatoire';
    $spec->key   = 'bloc_operatoire_id';
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["salles"] = "CSalle bloc_id";
    $backProps["check_lists"] = "CDailyCheckList object_id";
    return $backProps;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["group_id"] = "ref notNull class|CGroups";
    $specs["nom"]      = "str notNull seekable";
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
  
  /**
   * Load list overlay for current group
   */
  function loadGroupList($where = array(), $order = 'nom', $limit = null, $groupby = null, $ljoin = array()) {
    // Filtre sur l'établissement
		$g = CGroups::loadCurrent();
		$where["group_id"] = "= '$g->_id'";
    
    $list = $this->loadList($where, $order, $limit, $groupby, $ljoin);
    foreach ($list as &$bloc) {
    	$bloc->loadRefsSalles();
    }
    return $list;
  }
  
  function loadRefGroup(){
    $group = new CGroups;
    $this->_ref_group = $group->getCached($this->group_id);
  }
  
  function loadRefsFwd(){
    $this->loadRefGroup();
  }
  
  function loadRefsSalles() {
  	$this->_ref_salles = $this->loadBackRefs('salles', 'nom');
  }
  
  function loadRefsBack() {
    $this->loadRefsSalles();
  }
  
  function loadRefsAlertesIntervs() {
    $this->loadRefsSalles();
    $inSalles = CSQLDataSource::prepareIn(array_keys($this->_ref_salles));
    $alerte = new CAlert();
    $ljoin = array();
    $ljoin["operations"] = "operations.operation_id = alert.object_id";
    $ljoin["plagesop"]   = "plagesop.plageop_id = operations.plageop_id";
    $where = array();
    $where["alert.object_class"] = "= 'COperation'";
    $where["alert.tag"] = "= 'mouvement_intervention'";
    $where["alert.handled"]   = "= '0'";
    $where[] = "operations.salle_id ".$inSalles." OR plagesop.salle_id ".$inSalles." OR (plagesop.salle_id IS NULL AND operations.salle_id IS NULL)";
    $order = "operations.date, operations.chir_id";
    return $this->_alertes_intervs = $alerte->loadList($where, $order, null, null, $ljoin);
  }
}
?>
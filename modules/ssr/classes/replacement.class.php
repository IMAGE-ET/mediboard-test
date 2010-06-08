<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CReplacement extends CMbObject {
  // DB Table key
  var $replacement_id = null;

  var $sejour_id   = null;
	var $conge_id    = null;
	var $replacer_id = null;
	
	var $_ref_sejour   = null;
	var $_ref_conge    = null;
	var $_ref_replacer = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'replacement';
    $spec->key   = 'replacement_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["sejour_id"]   = "ref notNull class|CSejour";
    $props["conge_id"]    = "ref notNull class|CPlageConge";
		$props["replacer_id"] = "ref notNull class|CMediusers";
    return $props;
  }

	function store(){
	  parent::store();
  
	  // Lors de la creation du remplacement, on reaffecte les evenements du kine principal
    $this->completeField("sejour_id", "conge_id");
    $this->loadRefConge();
    $this->loadRefSejour();
    $conge =& $this->_ref_conge;
    $sejour =& $this->_ref_sejour;
    $sejour->loadRefBilanSSR();
    $sejour->_ref_bilan_ssr->loadRefTechnicien();
		$kine_id = $sejour->_ref_bilan_ssr->_ref_technicien->kine_id;
		
		$evenement_ssr = new CEvenementSSR();
		$where = array();
		$where["therapeute_id"] = " = '$kine_id'";
		$where["sejour_id"] = " = '$this->sejour_id'";
		$where["debut"] = "BETWEEN '$conge->date_debut' AND '$conge->date_fin'";
		$evenements = $evenement_ssr->loadList($where);

    foreach($evenements as $_evenement){
    	$_evenement->therapeute_id = $this->replacer_id;
			if($msg = $_evenement->store()){
        return $msg;
      }
		}
	}
	
	function delete(){
		// Lors de la suppression du remplacant, on reaffecte les evenements au kine principal
		$this->completeField("sejour_id", "conge_id","replacer_id");
		$this->loadRefConge();
		$this->loadRefSejour();
		$conge =& $this->_ref_conge;
		$sejour =& $this->_ref_sejour;
		$sejour->loadRefBilanSSR();
	  $sejour->_ref_bilan_ssr->loadRefTechnicien();
		
    $evenement_ssr = new CEvenementSSR();
    $where = array();
    $where["therapeute_id"] = " = '$this->replacer_id'";
    $where["sejour_id"] = " = '$this->sejour_id'";
    $where["debut"] = "BETWEEN '$conge->date_debut' AND '$conge->date_fin'";
    $evenements = $evenement_ssr->loadList($where);
		
		foreach($evenements as $_evenement){
			$_evenement->therapeute_id = $sejour->_ref_bilan_ssr->_ref_technicien->kine_id;
		  if($msg = $_evenement->store()){
				return $msg;
		  }
		}
		
		return parent::delete();
	}
	
  function loadRefSejour(){
  	$this->_ref_sejour = $this->loadFwdRef("sejour_id");
  }

  function loadRefConge(){
    $this->_ref_conge = $this->loadFwdRef("conge_id");
  }
	
  function loadRefReplacer(){
    $this->_ref_replacer = $this->loadFwdRef("replacer_id");
  }	
}

?>
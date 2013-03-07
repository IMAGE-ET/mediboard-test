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

  // DB Fields
  var $sejour_id   = null;
  var $conge_id    = null;
  var $replacer_id = null;

  var $deb = null;  
  var $fin = null;  
  
  
  // References
  var $_ref_sejour   = null;
  var $_ref_conge    = null;
  var $_ref_replacer = null;
  
  // Distant fields
  var $_min_deb = null;
  var $_max_fin = null;
  
  // Distant collections
  var $_ref_replacer_conges = null;
  var $_ref_replacement_fragments = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "replacement";
    $spec->key   = "replacement_id";
    $spec->uniques["unique"] = array("sejour_id", "conge_id");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    
    // DB Fields
    $props["sejour_id"]   = "ref notNull class|CSejour";
    $props["conge_id"]    = "ref notNull class|CPlageConge";
    $props["replacer_id"] = "ref notNull class|CMediusers";

    $props["deb"] = "date";
    $props["fin"] = "date";
    
    // Distant Fields
    $props["_min_deb"] = "date";
    $props["_max_fin"] = "date";
    
    return $props;
  }
  
  function check() {
    if ($msg = parent::check()) {
      return $msg;
    }
    
    $this->completeField("conge_id", "replacer_id");
    $this->loadRefConge();
    
    if ($this->_ref_conge->user_id == $this->replacer_id) {
      return "$this->_class-failed-same_user";
    }
  }

  function store() {
    if ($msg = parent::store()) {
      return $msg;
    }
  
    // Lors de la creation du remplacement, on reaffecte les evenements du kine principal
    $this->completeField("sejour_id", "conge_id");
    $this->loadRefConge();
    $this->loadRefSejour();
    $conge =& $this->_ref_conge;
    $sejour =& $this->_ref_sejour;
    $sejour->loadRefBilanSSR();
    $sejour->_ref_bilan_ssr->loadRefTechnicien();
    $kine_id = $sejour->_ref_bilan_ssr->_ref_technicien->kine_id;
    
    $date_debut = $conge->date_debut;
    $date_fin = CMbDT::date("+1 DAY", $conge->date_fin);
    $evenement_ssr = new CEvenementSSR();
    $where = array();
    $where["therapeute_id"] = " = '$kine_id'";
    $where["sejour_id"] = " = '$this->sejour_id'";
    $where["debut"] = "BETWEEN '$date_debut' AND '$date_fin'";
    $evenements = $evenement_ssr->loadList($where);

    foreach($evenements as $_evenement){
      $_evenement->therapeute_id = $this->replacer_id;
      if ($msg = $_evenement->store()){
        CAppUI::setMsg($msg, UI_MSG_WARNING);
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
    
    $date_debut = $conge->date_debut;
    $date_fin = CMbDT::date("+1 DAY", $conge->date_fin);
    $evenement_ssr = new CEvenementSSR();
    $where = array();
    $where["therapeute_id"] = " = '$this->replacer_id'";
    $where["sejour_id"] = " = '$this->sejour_id'";
    $where["debut"] = "BETWEEN '$date_debut' AND '$date_fin'";
    $evenements = $evenement_ssr->loadList($where);
    
    foreach($evenements as $_evenement){
      $_evenement->therapeute_id = $sejour->_ref_bilan_ssr->_ref_technicien->kine_id;
      if ($msg = $_evenement->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
    }
    
    return parent::delete();
  }
  
  function loadRefSejour(){
    return $this->_ref_sejour = $this->loadFwdRef("sejour_id", true);
  }

  function loadRefConge(){
    return $this->_ref_conge = $this->loadFwdRef("conge_id", true);
  }
  
  function loadRefReplacer() {
    return $this->_ref_replacer = $this->loadFwdRef("replacer_id", true);
  }  
  
  function loadDates() {
    $conge  = $this->loadRefConge();
    $sejour = $this->loadRefSejour();
    
    list($this->_min_deb, $this->_max_fin) = CMbRange::intersection(
      CMbDT::date($sejour->entree), CMbDT::date($sejour->sortie),
      $conge->date_debut, $conge->date_fin
    );
  }
  
  function checkCongesRemplacer() {
    $this->loadDates();
    
    $conge = new CPlageConge;
    return $this->_ref_replacer_conges = $conge->loadListForRange($this->replacer_id, $this->_min_deb, $this->_max_fin);
  }

  function makeFragments() {
    $fragments = array();
    $croppers = array();
    foreach($this->_ref_replacer_conges as $_conge) {
      $croppers[] = array(CMbDT::date("-1 DAY", $_conge->date_debut), CMbDT::date("+1 DAY", $_conge->date_fin));
    }
    
    if (count($this->_ref_replacer_conges) > 0) {
      $fragments = CMbRange::multiCrop($this->deb, $this->fin, $croppers);
      foreach($fragments as $key => $_fragment) {
        if (!CMbRange::collides($this->_min_deb, $this->_max_fin, $_fragment[0], $_fragment[1])) {
          unset($fragments[$key]);
        }       
      }
    }
        
    return $this->_ref_replacement_fragments = $fragments;
    
  }

  function loadListFor($user_id, $date) {
    $join["plageconge"] = "replacement.conge_id = plageconge.plage_id";
    $where[] = "'$date' BETWEEN plageconge.date_debut AND plageconge.date_fin";
    $where["replacement.replacer_id"] = "= '$user_id'";
    return $this->loadList($where, null, null, null, $join);
  }
}

?>
<?php /* $Id */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: 6194 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
class CPlageVacances extends CMbObject {
	// DB Table key
  var $plage_id = null;
  
  // DB Fields
  var $date_debut  = null;
  var $date_fin    = null;
  var $libelle     = null;
  var $user_id     = null;
	var $replacer_id = null;

  // Object References
  var $_ref_user  = null;

  // Form field
  var $_duree      = null;

  function getSpec() {
		$specs = parent::getSpec();
		$specs->table = "plageVacances";
		$specs->key   = "plage_id";
		return $specs;
	}

  //spécification des propriétés
  function getProps() { 
    $specs = parent::getProps();
    $specs["user_id"]     = "ref class|CMediusers notNull";
    $specs["date_debut"]  = "date notNull";
    $specs["date_fin"]    = "date moreEquals|date_debut notNull";
    $specs["libelle"]     = "str notNull";
    $specs["replacer_id"] = "ref class|CMediusers";
		$specs["_duree"]      = "num";
    return $specs;
  }

	function updateFormFields() {
		parent::updateFormFields();
		$this->_shortview = $this->_view = $this->libelle;
	}
	
	function check() {
		$this->completeField("date_debut", "date_fin", "user_id");
		
		$plage_vac  = new CPlageVacances();
		$plage_vac->user_id = $this->user_id;
		$plages_vac = $plage_vac->loadMatchingList();
		unset($plages_vac[$this->_id]);
		
		foreach($plages_vac as $_plage){
			$plageinbounds = (($this->date_debut < $_plage->date_debut) &&
			                  ($this->date_fin < $_plage->date_debut))  ||
											 (($this->date_debut > $_plage->date_fin));
			if (!$plageinbounds) {
        return CAppUI::tr("CPlageVacances-conflit %s", $_plage->_view);
		  }
	  }
    return parent::check();
  }
	
  function loadFor($user_id, $date) {
  	$where["user_id"] = "= '$user_id'";
		$where[] = "'$date' BETWEEN date_debut AND date_fin";
		$this->loadObject($where);
  }
	
	function loadRefsReplacementsFor($user_id, $date) {
    $where["replacer_id"] = "= '$user_id'";
    $where[] = "'$date' BETWEEN date_debut AND date_fin";
    return $this->loadList($where);
	}
	
	function loadRefUser() {
    $this->_ref_user = $this->loadFwdRef("user_id");
	}

  function loadRefsFwd() {
  	$this->loadRefUser(); 
  }
  
	function getPerm($permType) {
    global $AppUI;
		if ($this->user_id == $AppUI->user_id) {
      return true;
    } 

		if(!$this->_ref_user) {
      $this->loadRefsFwd();
    }
    return $this->_ref_user->getPerm($permType);
  }
}
?>
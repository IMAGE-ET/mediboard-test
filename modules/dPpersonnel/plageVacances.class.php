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
  var $date_debut = null;
  var $date_fin   = null;
  var $libelle    = null;
  var $user_id    = null;

  // Object References
  var $_ref_user  = null;

  // Form field
  var $_date_debut = null;
	var $_date_fin   = null;
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
    $specs["date_debut"]  = "date";
    $specs["date_fin"]    = "date moreEquals|date_debut";
    $specs["libelle"]     = "str";
		$specs["user_id"]     = "ref class|CMediusers notNull";
    $specs["_date_debut"] = "date";
    $specs["_date_fin"]   = "date moreEquals|date_debut";
		$specs["_duree"]      = "num";
    return $specs;
  }

  function loadRefsFwd() { 
    $this->_ref_user = new CMediusers;
    $this->_ref_user->load($this->user_id);
  }
	
	function updateFormFields() {
		parent::updateFormFields();
		$this->_view = CAppUI::tr("Plage %s from %s to %s", $this->libelle, $this->date_debut, $this->date_fin);
		if($this->date_debut == $this->date_fin) {
		  $this->_shortview = CAppUI::tr("%s", $this->date_debut);
		}
		else {
			$this->_shortview = CAppUI::tr("From %s to %s", $this->date_debut, $this->date_fin);
		}
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
}
?>
<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CDestinataireHprim extends CMbObject {
  // L'ajout du PMSI se fait en dehors de la classe car dépends de la config
	static $messagesHprim = array(
    "patients" => array ( 
      "evenementPatient" 
	  ),
    "pmsi" => array(
      "evenementServeurActe"
	  ),
    "stock" => array ( 
      "evenementMvtStocks"
	  )
  );
	
  // DB Table key
  var $dest_hprim_id  = null;
  
  // DB Fields
  var $nom       = null;
  var $libelle   = null;
  var $group_id  = null;
  var $type      = null;
	var $message   = null;
  var $actif     = null;
	
  // Forward references
  var $_ref_group             = null;
  var $_ref_exchanges_sources = null;
  
  // Form fields
  var $_tag_patient     = null;
  var $_tag_sejour      = null;
	var $_tag_mediuser    = null;
	var $_type_echange    = "hprimxml";
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'destinataire_hprim';
    $spec->key   = 'dest_hprim_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["nom"]       = "str notNull";
    $specs["libelle"]   = "str";
    $specs["group_id"]  = "ref notNull class|CGroups";
    $specs["type"]      = "enum notNull list|cip|sip default|cip";
		$specs["message"]   = "enum list|pmsi|patients|stock default|patient";
    $specs["actif"]     = "bool notNull";
    
    $specs["_tag_patient"]   = "str";
    $specs["_tag_sejour"]    = "str";
		$specs["_tag_mediuser"]  = "str";
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['object_configs'] = "CDestinataireHprimConfig object_id";
    
    return $backProps;
  }
  
  
  function loadRefsFwd() {
    $this->_ref_group = new CGroups;
    $this->_ref_group->load($this->group_id);
  }
	
	function loadRefsExchangesSources() {
		$this->_ref_exchanges_sources = array();
		foreach (self::$messagesHprim as $_message => $_evenements) {
			if ($_message == $this->message) {
				foreach ($_evenements as $_evenement) {
          $this->_ref_exchanges_sources[$_evenement] = CExchangeSource::get("$this->_guid-$_evenement", null, true, $this->_type_echange);
				}
			}
		}
	}
  
  function updateFormFields() {
    parent::updateFormFields();
    		
    $this->_tag_patient  = str_replace('$g', $this->group_id, CAppUI::conf("dPpatients CPatient tag_ipp"));
    $this->_tag_sejour   = str_replace('$g', $this->group_id, CAppUI::conf("dPplanningOp CSejour tag_dossier"));
		$this->_tag_mediuser = str_replace('$g', $this->group_id, CAppUI::conf("mediusers tag_mediuser"));
  }  
  
  function register($idClient) {
    $this->nom = $idClient;
    $this->loadMatchingObject();
  }
}

// Add
CDestinataireHprim::$messagesHprim['pmsi'][] = (CAppUI::conf("hprimxml send_diagnostic") == "evt_serveuretatspatient") ? 
                                                "evenementServeurEtatsPatient" : "evenementPMSI"; 
?>
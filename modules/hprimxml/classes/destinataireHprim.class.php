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
      "evenementServeurActe",
	    "evenementFraisDivers"
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
  var $register  = null;
	
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
    $specs["register"]  = "bool notNull default|1";
    
    $specs["_tag_patient"]   = "str";
    $specs["_tag_sejour"]    = "str";
		$specs["_tag_mediuser"]  = "str";
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['object_configs'] = "CDestinataireHprimConfig object_id";
    $backProps['emetteurs'] = "CEchangeHprim emetteur_id";
    $backProps['destinataires'] = "CEchangeHprim destinataire_id";
    
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
  
  function getTagIPP($group_id = null) {
    // Pas de tag IPP => pas d'affichage d'IPP
    if (null == $tag_ipp = CAppUI::conf("dPpatients CPatient tag_ipp")) {
      return;
    }

    // Permettre des IPP en fonction de l'établissement
    $group = CGroups::loadCurrent();
    if (!$group_id) {
      $group_id = $group->_id;
    }
    
    // Préférer un identifiant externe de l'établissement
    if ($tag_group_idex = CAppUI::conf("dPpatients CPatient tag_ipp_group_idex")) {
      $idex = new CIdSante400();
      $idex->loadLatestFor($group, $tag_group_idex);
      $group_id = $idex->id400;
    }
   
    return str_replace('$g', $group_id, $tag_ipp);
  }
  
  function getTagNumDossier($group_id = null) {
    // Pas de tag Num dossier
    if (null == $tag_dossier = CAppUI::conf("dPplanningOp CSejour tag_dossier")) {
      return;
    }

    // Permettre des IPP en fonction de l'établissement
    $group = CGroups::loadCurrent();
    if (!$group_id) {
      $group_id = $group->_id;
    }
    
    // Préférer un identifiant externe de l'établissement
    if ($tag_group_idex = CAppUI::conf("dPplanningOp CSejour tag_dossier_group_idex")) {
      $idex = new CIdSante400();
      $idex->loadLatestFor($group, $tag_group_idex);
      $group_id = $idex->id400;
    }

    return str_replace('$g', $group_id, $tag_dossier);
  }
  
  function updateFormFields() {
    parent::updateFormFields();

    $this->_view = $this->libelle ? $this->libelle : $this->nom;
    
    $this->_tag_patient  = $this->getTagIPP($this->group_id);		
    $this->_tag_sejour   = $this->getTagNumDossier($this->group_id);
		
		
		$this->_tag_mediuser = str_replace('$g', $this->group_id, CAppUI::conf("mediusers tag_mediuser"));
  }  
  
  function register($idClient) {
    $this->nom = $idClient;
    $this->loadMatchingObject();
    
    // Enregistrement automatique d'un destinataire
    if (!$this->_id) {

    }
  }
  
  
  function sendEvenementPatient($domEvenement, $mbObject, $referent = null, $initiateur = null) {
    $msgEvtVenuePatient = $domEvenement->generateTypeEvenement($mbObject, $referent, $initiateur);
    
    if ($this->actif) {
      $source = CExchangeSource::get("$this->_guid-evenementPatient");
      if ($source->_id) {
        $source->setData($msgEvtVenuePatient);
        $source->send();
        $acquittement = $source->receive();
        
        if ($acquittement) {
          $echange_hprim = $domEvenement->_ref_echange_hprim;
          $echange_hprim->date_echange = mbDateTime();
          
          $domGetAcquittement = new CHPrimXMLAcquittementsPatients();
          $domGetAcquittement->loadXML(utf8_decode($acquittement));
          $domGetAcquittement->_ref_echange_hprim = $echange_hprim;
          $doc_valid = $domGetAcquittement->schemaValidate();
          
          $echange_hprim->statut_acquittement = $domGetAcquittement->getStatutAcquittementPatient();
          $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
          $echange_hprim->_acquittement = $acquittement;
      
          $echange_hprim->store();
        } 
      }      
    }
  }
}

// Add
CDestinataireHprim::$messagesHprim['pmsi'][] = (CAppUI::conf("hprimxml send_diagnostic") == "evt_serveuretatspatient") ? 
                                                "evenementServeurEtatsPatient" : "evenementPMSI"; 
?>
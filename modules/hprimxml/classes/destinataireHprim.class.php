<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::loadClass("CDestinataireXML");

class CDestinataireHprim extends CDestinataireXML {	
  // DB Table key
  var $dest_hprim_id  = null;
  
  // DB Fields
  var $type        = null;
  var $register    = null;
  var $code_appli  = null;
  var $code_acteur = null;
  var $code_syst   = null;
	  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'destinataire_hprim';
    $spec->key   = 'dest_hprim_id';
    $spec->messages = array(
      "patients" => array ( 
        "evenementPatient" 
      ),
      "pmsi" => array(
        (CAppUI::conf("hprimxml send_diagnostic") == "evt_serveuretatspatient") ? 
          "evenementServeurEtatsPatient" : "evenementPMSI",
        "evenementServeurActe",
        "evenementFraisDivers"
      ),
      "stock" => array ( 
        "evenementMvtStocks"
      )
    );
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["type"]        = "enum notNull list|cip|sip default|cip";
		$props["message"]     = "enum list|pmsi|patients|stock default|patient";
    $props["register"]    = "bool notNull default|1";
    $props["code_appli"]  = "str";
    $props["code_acteur"] = "str";
    $props["code_syst"]   = "str";

    return $props;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['object_configs'] = "CDestinataireHprimConfig object_id";
    $backProps['emetteurs']      = "CEchangeHprim emetteur_id";
    $backProps['destinataires']  = "CEchangeHprim destinataire_id";
    
    return $backProps;
  }
    
	function loadRefsExchangesSources() {
		$this->_ref_exchanges_sources = array();
		foreach ($this->_spec->messages as $_message => $_evenements) {
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
		$this->_tag_service  = str_replace('$g', $this->group_id, CAppUI::conf("dPhospi tag_service"));
		
		$this->code_syst = $this->code_syst ? $this->code_syst : $this->nom;
  }  
  
  function register($idClient) {
    $this->nom = $idClient;
    $this->loadMatchingObject();
    
    // Enregistrement automatique d'un destinataire
    if (!$this->_id) {
      
    }
  }
  
  function sendEvenementPatient($domEvenement, $mbObject, $referent = null, $initiateur = null) {
    $msgEvtPatient = $domEvenement->generateTypeEvenement($mbObject, $referent, $initiateur);
    
    if ($this->actif) {
      $source = CExchangeSource::get("$this->_guid-evenementPatient");
      if ($source->_id) {
        $source->setData($msgEvtPatient);
        $source->send();
        $acquittement = $source->receive();

        if ($acquittement) {
          $echange_hprim = $domEvenement->_ref_echange_hprim;
          $echange_hprim->date_echange = mbDateTime();
          
          $domGetAcquittement = new CHPrimXMLAcquittementsPatients();
          $domGetAcquittement->loadXML($acquittement);
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

?>
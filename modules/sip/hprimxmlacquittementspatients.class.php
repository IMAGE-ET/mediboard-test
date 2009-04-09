<?php

/**
 *  @package Mediboard
 *  @subpackage sip
 *  @version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */


CAppUI::requireModuleClass("dPinterop", "mbxmldocument");
CAppUI::requireModuleClass("dPinterop", "hprimxmldocument");

if (!class_exists("CHPrimXMLDocument")) {
	return;
}

class CHPrimXMLAcquittementsPatients extends CHPrimXMLDocument {
	var $_codes_erreurs = null;

	function __construct() {
		parent::__construct("evenementPatient", "msgAcquittementsPatients105", "sip");
	}

	function generateEnteteMessageAcquittement($statut, $codes = null, $commentaires = null) {
		global $AppUI, $g, $m;

		$acquittementsPatients = $this->addElement($this, "acquittementsPatients", null, "http://www.hprim.org/hprimXML");

		$enteteMessageAcquittement = $this->addElement($acquittementsPatients, "enteteMessageAcquittement");
		$this->addAttribute($enteteMessageAcquittement, "statut", $statut);

		$this->addElement($enteteMessageAcquittement, "identifiantMessage", $this->_identifiant);
		$this->addDateTimeElement($enteteMessageAcquittement, "dateHeureProduction");

		$emetteur = $this->addElement($enteteMessageAcquittement, "emetteur");
		$agents = $this->addElement($emetteur, "agents");
		$this->addAgent($agents, "application", "MediBoard", "Gestion des Etablissements de Santé");
		$group = CGroups::loadCurrent();
		$group->loadLastId400();
		$this->addAgent($agents, "système", $this->_emetteur, $group->text);

		$destinataire = $this->addElement($enteteMessageAcquittement, "destinataire");
		$agents = $this->addElement($destinataire, "agents");
		$this->addAgent($agents, "application", $this->_destinataire, $this->_destinataire_libelle);

		$this->addElement($enteteMessageAcquittement, "identifiantMessageAcquitte", $this->_identifiant);
    
		if ($statut == "OK") {
			if (is_array($codes)) {
				$_codes = $_libelle_codes = "";
        foreach ($codes as $code) {
        	$_codes .= $code;
        	$_libelle_codes .= CHprimSoapHandler::$codesAvertissementInformation[$code]." ";
        }
        $this->addObservation($enteteMessageAcquittement, substr($_codes, 0, 17), substr($_libelle_codes, 0, 80), $commentaires);
			} else {
	      $this->addObservation($enteteMessageAcquittement, $codes, CHprimSoapHandler::$codesAvertissementInformation[$codes], $commentaires);
			}
		}
	}

	function addErreursAvertissements($codes, $commentaires = null, $mbObject = null) {
		$acquittementsPatients = $this->documentElement;
		 
		$erreursAvertissements = $this->addElement($acquittementsPatients, "erreursAvertissements");
		 
		if (is_array($codes)) {
			foreach ($codes as $code) {
				$this->addErreurAvertissement($erreursAvertissements, $code, CHprimSoapHandler::$codesErreur[$code], $commentaires);
			}
		} else {
			$this->addErreurAvertissement($erreursAvertissements, $codes, CHprimSoapHandler::$codesErreur[$codes], $commentaires);
		}		
	}

	function generateAcquittementsPatients($statut, $codes, $commentaires = null, $mbObject = null) {
		$this->_emetteur = CAppUI::conf('mb_id');
		$this->_date_production = mbDateTime();

		if ($statut != "OK") {
			$this->generateEnteteMessageAcquittement($statut);
		  $this->addErreursAvertissements($codes, $commentaires, $mbObject);
		} else {
			$this->generateEnteteMessageAcquittement($statut, $codes, $commentaires);
		}

		$doc_valid = $this->schemaValidate();
		$this->saveTempFile();
		$messageAcquittementPatient = utf8_encode($this->saveXML());

		return $messageAcquittementPatient;
	}
  
	function getStatutAcquittementPatient() {
	  $xpath = new CMbXPath($this);
    $xpath->registerNamespace( "hprim", "http://www.hprim.org/hprimXML" );
        
    return $xpath->queryAttributNode("/hprim:acquittementsPatients/hprim:enteteMessageAcquittement", null, "statut"); 
	}
	
	function getAcquittementsPatients() {
		$xpath = new CMbXPath($this);
    $xpath->registerNamespace( "hprim", "http://www.hprim.org/hprimXML" );
    
    $statut = $xpath->queryAttributNode("/hprim:acquittementsPatients/hprim:enteteMessageAcquittement", null, "statut"); 
    
    $query = "/hprim:acquittementsPatients/hprim:enteteMessageAcquittement";
    $enteteMessageAcquittement = $xpath->queryUniqueNode($query);  
    
    $data['identifiantMessage'] = $xpath->queryTextNode("hprim:identifiantMessage", $enteteMessageAcquittement);
    $agents = $xpath->queryUniqueNode("hprim:emetteur/hprim:agents", $enteteMessageAcquittement);
    $systeme = $xpath->queryUniqueNode("hprim:agent[@categorie='système']", $agents);
    $data['idClient'] = $xpath->queryTextNode("hprim:code", $systeme);
    $data['libelleClient'] = $xpath->queryTextNode("hprim:libelle", $systeme);
    
    $data['identifiantMessageAcquitte'] = $xpath->queryTextNode("hprim:identifiantMessageAcquitte", $enteteMessageAcquittement);
    
    return $data;
	}
	
	function getAcquittementObservation() {
		$xpath = new CMbXPath($this);
    $xpath->registerNamespace( "hprim", "http://www.hprim.org/hprimXML" );
    
    $statut = $xpath->queryAttributNode("/hprim:acquittementsPatients/hprim:enteteMessageAcquittement", null, "statut"); 
    
    $query = "/hprim:acquittementsPatients/hprim:enteteMessageAcquittement";
    $enteteMessageAcquittement = $xpath->queryUniqueNode($query);  
    
	  if ($statut == "OK") {
	  	$d = array();
      $observations[] = &$d;
        
      $observation = $xpath->queryUniqueNode("hprim:observation", $enteteMessageAcquittement);
      $d['code'] = chunk_split($xpath->queryTextNode("hprim:code", $observation, "", false), 3, ' ');
      $d['libelle'] = $xpath->queryTextNode("hprim:libelle", $observation, "", false);
      $d['commentaire'] = $xpath->queryTextNode("hprim:commentaire", $observation, "", false);
    } else {
      $query = "/hprim:acquittementsPatients/hprim:erreursAvertissements/*";
      $erreursAvertissements = $xpath->query($query);   
      
      foreach ($erreursAvertissements as $erreurAvertissement) {
        $d = array();
        $observations[] = &$d;
      
        $observation = $xpath->queryUniqueNode("hprim:observations/hprim:observation", $erreurAvertissement);
        $d['code'] = chunk_split($xpath->queryTextNode("hprim:code", $observation, "", false), 3, ' ');
        $d['libelle'] = $xpath->queryTextNode("hprim:libelle", $observation, "", false);
        $d['commentaire'] = $xpath->queryTextNode("hprim:commentaire", $observation, "", false);
      }
    }  
    
    return $observations;
	}
}

?>
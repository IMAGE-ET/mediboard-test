<?php

/**
 *  @package Mediboard
 *  @subpackage sip
 *  @version $Revision: $
 *  @author Yohann Poiron
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
		$this->addAttribute($enteteMessageAcquittement, "statut", ($statut == false) ? "OK" : "erreur");

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
    
		if ($statut == false) {
			if (is_array($codes)) {
	      foreach ($codes as $code) {
	        $this->addObservation($enteteMessageAcquittement, $code, CHprimSoapHandler::$codesAvertissementInformation[$code], $commentaires);
	      }
	    } else {
	      $this->addObservation($enteteMessageAcquittement, $codes, CHprimSoapHandler::$codesAvertissementInformation[$codes], $commentaires);
	    }
		}
	}

	function addErreursAvertissements($codes, $commentaires, $mbObject = null) {
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

	function generateAcquittementsPatients($statut, $codes, $commentaires, $mbObject = null) {
		$this->_emetteur = CAppUI::conf('mb_id');
		$this->_date_production = mbDateTime();
mbExport($statut, "Statut", true);
		if ($statut == false) {
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

	function getAcquittementEvenementPatient($msgCIP, $erreur) {
		$domAcquittement = new CHPrimXMLAcquittementsPatients();
		// Erreur
		if ($erreur) {
			$domAcquittement->generateEnteteMessageAcquittement("erreur", $msgCIP, $erreur);
		} else {
			$domAcquittement->generateEnteteMessageAcquittement("OK", $msgCIP);
		}

		$doc_valid = $domAcquittement->schemaValidate();
		$domAcquittement->saveTempFile();
		$messageAcquittement = utf8_encode($domAcquittement->saveXML());

		return $messageAcquittement;
	}
}

?>
<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CSipObjectHandler extends CMbObjectHandler {
	static $handled = array ("CPatient");

	static function isHandled(CMbObject &$mbObject) {
		return in_array($mbObject->_class_name, self::$handled);
	}

	function onStore(CMbObject &$mbObject) {
		if (!$this->isHandled($mbObject)) {
			return;
		}

		if (!$mbObject->_ref_last_log) {
			return;
		}

		// Si client et traitement HPRIM
		if (isset($mbObject->_coms_from_hprim) && ($mbObject->_coms_from_hprim == 1) && !CAppUI::conf('sip server')) {
			return;
		}

		// Si serveur et pas d'IPP sur le patient
		if (isset($mbObject->_no_ipp) && ($mbObject->_no_ipp == 1) && CAppUI::conf('sip server')) {
			return;
		}

		$dest_hprim = new CDestinataireHprim();

		// Si Serveur
		if (CAppUI::conf('sip server')) {
			$listDest = $dest_hprim->loadList();

			foreach ($listDest as $_curr_dest) {
				// Recherche si le patient possède un identifiant externe sur le SIP
				$id400 = new CIdSante400();
				//Paramétrage de l'id 400
				$id400->object_id = $mbObject->_id;
				$id400->object_class = "CPatient";
				$id400->tag = $_curr_dest->destinataire;

				if($id400->loadMatchingObject())
				  $mbObject->_id400 = $id400->id400;
				else
				  $mbObject->_id400 = null;

				if (!$mbObject->_IPP) {
					$IPP = new CIdSante400();
					//Paramétrage de l'id 400
					$IPP->object_class = "CPatient";
					$IPP->object_id = $mbObject->_id;
					$IPP->tag = CAppUI::conf("mb_id");
					$IPP->loadMatchingObject();

					$mbObject->_IPP = $IPP->id400;
				}
				
				$domEvenement = new CHPrimXMLEnregistrementPatient();
				$domEvenement->_emetteur = CAppUI::conf('mb_id');
				$domEvenement->_destinataire = $_curr_dest->destinataire;
				$domEvenement->_destinataire_libelle = " ";

				$echange_hprim = new CEchangeHprim();
				if (isset($mbObject->_hprim_initiator_id)) {
					$echange_hprim->load($mbObject->_hprim_initiator_id);
				}

				$initiateur = ($_curr_dest->destinataire == $echange_hprim->emetteur) ? $echange_hprim->_id : null;

				$domEvenement->generateEvenementsPatients($mbObject, true, $initiateur);
			}
		}
		// Si Client
		else {
			$dest_hprim->type = "sip";
			$dest_hprim->loadMatchingObject();

			if (!$mbObject->_IPP) {
				$IPP = new CIdSante400();
				//Paramétrage de l'id 400
				$IPP->object_class = "CPatient";
				$IPP->object_id = $mbObject->_id;
				$IPP->tag = $dest_hprim->destinataire;
				$IPP->loadMatchingObject();

				$mbObject->_IPP = $IPP->id400;
			}

			$domEvenement = new CHPrimXMLEnregistrementPatient();
			$domEvenement->_emetteur = CAppUI::conf('mb_id');
			$domEvenement->_destinataire = $dest_hprim->destinataire;
			$messageEvtPatient = $domEvenement->generateEvenementsPatients($mbObject);

			if (!$client = CMbSOAPClient::make($dest_hprim->url, $dest_hprim->username, $dest_hprim->password)) {
				trigger_error("Impossible de joindre le destinataire : ".$dest_hprim->url);
			}

			// Récupère le message d'acquittement après l'execution la methode evenementPatient
			if (null == $acquittement = $client->evenementPatient($messageEvtPatient)) {
				trigger_error("Evénement patient impossible sur le SIP : ".$dest_hprim->url);
			}
			
			$echange_hprim = new CEchangeHprim();
			$echange_hprim->load($domEvenement->_identifiant);
			$echange_hprim->date_echange = mbDateTime();
			
		  $domGetAcquittement = new CHPrimXMLAcquittementsPatients();
      $domGetAcquittement->loadXML(utf8_decode($acquittement));        
      $doc_valid = $domGetAcquittement->schemaValidate();
      if (!$doc_errors) {
        $echange_hprim->statut_acquittement = $domGetAcquittement->getStatutAcquittementPatient();
      }
      $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
			$echange_hprim->acquittement = $acquittement;

			$echange_hprim->store();
		}
	}

	function onMerge(CMbObject &$mbObject) {
		$this->onStore($mbObject);
	}

	function onDelete(CMbObject &$mbObject) {
	}
}
?>
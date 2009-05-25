<?php
/**
 *  @package Mediboard
 *  @subpackage dPfiles
 *  @version $Revision: 6226 $
 *  @author Yohann Poiron
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CEcDocumentSender extends CDocumentSender { 
  public static $sendables = array(
	  "CPatient"       => array ("PA"),
	  "CSejour"        => array ("SJ", "AT"),
	  "COperation"     => array ("IN"),
	  "CConsultation"  => array ("PA"),
	  "CConsultAnesth" => array ("PA"),
  );

  var $clientSOAP = null;
  
  /**
   * Instanciate Soap Client
   * @return array Base SOAP params array, null on error
   */
  function initClientSOAP () {
    if (!$this->clientSOAP instanceof SoapClient) {
	    try {
	      CMedicap::makeUrls();
				$serviceURL = CMedicap::$urls["soap"]["documents"];
				
				if (!url_exists($serviceURL)) {
				  CAppUI::stepMessage(UI_MSG_ERROR, "Serveur wep inatteignable à l'adresse : $serviceURL");
				  return;
				}
				
				$this->clientSOAP = new SoapClient("$serviceURL?WSDL");
	    } 
	    catch (Exception $e) {
	      trigger_error("Instanciation du SoapClient impossible : ".$e);
	      return;
	    }
    }
	    
    return array (
		  "aLoginApplicatif"       => CAppUI::conf("ecap soap user"),
		  "aPasswordApplicatif"    => CAppUI::conf("ecap soap pass"),
		  "aTypeIdentifiantActeur" => 1,
		  "aIdentifiantActeur"     => "pr1",
		  "aIdClinique"            => CAppUI::conf("dPsante400 group_id"),
	  );
  }
  
  /**
   * Instanciate Soap Client
   * @param string $method
   * @param array $params
   * @return object Parsed from XML response
   */
  function sendSOAP($method, $params)  {
    mbDump($params, "Final SOAP params");
    $result = $this->clientSOAP->__call($method, $params);
    $resultField = $method."Result";
		$result = simplexml_load_string($result->$resultField->any);
		$result->descriptionRetour = utf8_decode($result->descriptionRetour);
		mbTrace($result, "SOAP response");
		return $result;
  }
  
  static $ids = array();
  
  /**
   * Get cached target Id for object and target ecap type
   * @param CMbObject $object Mediboard Object
   * @param string $ecType ecap Type : [PA|SJ|AT|IN]
   * @return string ecap identifier, null on error
   */
  static function getIdFor(CMbObject $object, $ecType) {
    if (!in_array($ecType, self::$sendables[$object->_class_name])) {
      trigger_error("Mauvaise association de la classe Mediboard '$object->_class_name' avec le type eCap '$ecType'");
      return;
    }
    
    if (!@array_key_exists($object->_guid, self::$ids[$ecType])) {
	    $ident = new CIdSante400();
	    
	    if ($object instanceof CPatient) {
	   	  $ident->loadLatestFor($object, CMedicap::getTag($ecType));
	   	}
	    
	   	if ($object instanceof CSejour) {
	   	  $ident->loadLatestFor($object, CMedicap::getTag($ecType));
	   	}
	    
	   	if ($object instanceof COperation) {
	   	  $ident->loadLatestFor($object, CMedicap::getTag($ecType));
	   	}
	
	   	if ($object instanceof CConsultation) {
	      $object->loadRefPatient();
	   	  $ident->loadLatestFor($object->_ref_patient, CMedicap::getTag($ecType));
	   	}
	
	    self::$ids[$ecType][$object->_guid] = $ident->id400;
    }
    
    return self::$ids[$ecType][$object->_guid];  
  }
    
  /**
   * Get Patient Id for object
   * @param CMbObject $object Mediboard Object
   * @return string ecap identifier, null on error
   */
  static function getPatientIdFor(CMbObject $object) {
    if ($object instanceof CPatient) {
    	return self::getIdFor($object, "PA");
    }
    
    if ($object instanceof CCodable) {
      $object->loadRefPatient();
    	return self::getIdFor($object->_ref_patient, "PA");
    }

    return null;
  }
    
  function send(CDocumentItem $docItem) {
    $docItem->load();
    $docItem->loadTargetObject();
    
    // identifiant externe du document 
    $idDocItem = new CIdSante400();
    $idDocItem->loadLatestFor($docItem, CMedicap::getTag("DO"));
    $idDocItem->last_update = mbDateTime(); 
    @list($ecDocument, $ecVersion) = explode("-", $idDocItem->id400); 
    $ecDocument = $ecDocument ? $ecDocument : "0";
    $ecVersion  = $ecVersion  ? $ecVersion  : "0";
    
    // Identifiant externe de la catégorie
    $docItem->loadRefCategory();
    $idCategory = new CIdSante400();
    $idCategory->loadLatestFor($docItem->_ref_category, CMedicap::getTag("DT"));
    list($ecTypeObjet, $ecTypeDocument) = explode("-", $idCategory->id400);
    
    // Identifiant externe du patient, quelle que soit la cible
    $ecPatient = self::getPatientIdFor($docItem->_ref_object);
    
    // Chargement de la cible
    $ecObject = self::getIdFor($docItem->_ref_object, $ecTypeObjet);
    
    if (null == $params = $this->initClientSOAP()) {
      return false;
    }
    
    // Paramètres SOAP
    $params["aIPMachine"      ] = $_SERVER["REMOTE_ADDR"];
    $params["aIdDocument"     ] = (int) $ecDocument;
    $params["aIdVersion"      ] = (int) $ecVersion;
    $params["aIdPatient"      ] = $ecPatient;
    $params["aTypeObjet"      ] = $ecTypeObjet;
    $params["aIdTypeDocument" ] = $ecTypeDocument;
    $params["aCommentaire"    ] = "Commentaire pas si facultatif ?";
    $params["aIdObjet"        ] = $ecObject;
    $params["aLibelleDocument"] = utf8_encode($docItem->_extensioned);
    $params["aNomFichier"     ] = utf8_encode($docItem->_extensioned);
    $params["aFichierByte"    ] = $docItem->getContent();
    
    // Appel SOAP
//    $this->sendSOAP("DeposerDocument", $params));
//    return false;
    
    $result = $this->clientSOAP->DeposerDocument($params);
		$result = simplexml_load_string($result->DeposerDocumentResult->any);
		$result->descriptionRetour = utf8_decode($result->descriptionRetour);
		if ($result->codeRetour != "0") {
	    trigger_error("ecDocumentSender SOAP error [$result->codeRetour] for '$docItem->_guid': $result->descriptionRetour", E_USER_WARNING);
	    return false;
		}

		// Assocation des identifiant 
		$ecDocument = $result->document;
		if (is_array($ecDocument)) $ecDocument = $ecDocument[0];
		$idDocItem->id400 = "$ecDocument->id-$ecDocument->numeroVersion";
		if ($msg = $idDocItem->store()) {
	    trigger_error("ecDocumentSender Identifier store error for '$docItem->_guid': $msg",E_USER_WARNING);
	    return false;
		}
		
    // Change l'etat du document
    $docItem->etat_envoi = "oui";
    
    return true; 
  }
  
  function cancel($docItem) {
    $docItem->load();
    $docItem->loadTargetObject();
    
    // identifiant externe du document 
    $idDocItem = new CIdSante400();
    $idDocItem->loadLatestFor($docItem, CMedicap::getTag("DO"));
    $idDocItem->last_update = mbDateTime(); 
    @list($ecDocument, $ecVersion) = explode("-", $idDocItem->id400); 
    $ecDocument = $ecDocument ? $ecDocument : "0";
    $ecVersion  = $ecVersion  ? $ecVersion  : "0";
    
    // Identifiant externe du patient, quelle que soit la cible
    $ecPatient = self::getPatientIdFor($docItem->_ref_object);
    
    if (null == $params = $this->initClientSOAP()) {
      return false;
    }
    
    // Paramètres SOAP
    $params["aIPMachine"        ] = $_SERVER["REMOTE_ADDR"];
    $params["aIdDocument"       ] = (int) $ecDocument;
    $params["aIdVersion"        ] = (int) $ecVersion;
    $params["aIdPatient"        ] = $ecPatient;
    $params["aMotifInvalidation"] = "Mediboard user request";
    
    $result = $this->clientSOAP->InvaliderDocument($params);
		$result = simplexml_load_string($result->InvaliderDocumentResult->any);
		$result->descriptionRetour = utf8_decode($result->descriptionRetour);
		if ($result->codeRetour != "0") {
	    trigger_error("ecDocumentSender SOAP error [$result->codeRetour] for '$docItem->_guid': $result->descriptionRetour", E_USER_WARNING);
	    return false;
		}
		
    // Change l'etat du document
    $docItem->etat_envoi = "non";
    
    return true; 
  }
  
  function resend($docItem) {
    return $this->send($docItem);
  }
    
  function getSendProblem(CDocumentItem $docItem) {
    // Type de la cible
	  $docItem->loadTargetObject();
    if (!array_key_exists($docItem->_ref_object->_class_name, self::$sendables)) {
      return sprintf("Type d'objet '%s' non pris en charge", 
        CAppUI::tr($docItem->_ref_object->_class_name));
    }
    
    // Catégorie obligatoire
    if (!$docItem->file_category_id) {
      return "Ce document n'a pas de catégorie";
    }
    
    // Type de document Medicap
    $docItem->loadRefCategory();
    $category = $docItem->_ref_category;
    
    $idCategory = new CIdSante400();
    $idCategory->loadLatestFor($docItem->_ref_category, CMedicap::getTag("DT"));
    if (!$idCategory->id400) {
      return "La catégorie de documents n'est pas lié à un type de document e-Cap'";
    }
    list($ecTypeObjet, $ecTypeDocument) = explode("-", $idCategory->id400);
    
    // Identifiant externe du patient, quelle que soit la cible
    if (null == $ecPatient = self::getPatientIdFor($docItem->_ref_object)) {
      return "Patient inconnu par e-Cap pour l'établissement " . CMedicap::$cidc;
    }
    
    // Identifiant de la cible
    $ecObject = self::getIdFor($docItem->_ref_object, $ecTypeObjet);
    if (null == $ecObject = self::getPatientIdFor($docItem->_ref_object)) {
      return "Contexte de l'objet inconnu par e-Cap pour 'établissement " . CMedicap::$cidc;
    }
  }
}

?>
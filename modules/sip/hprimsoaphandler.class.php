<?php

/**
 *  @package Mediboard
 *  @subpackage sip
 *  @version $Revision: $
 *  @author Yohann Poiron
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("sip", "soaphandler");
CAppUI::requireModuleClass("dPinterop", "mbxmldocument");

/**
 * The CHprimSoapHandler class
 */
class CHprimSoapHandler extends CSoapHandler {

  static $paramSpecs = array(
    "evenementPatient" => array ( 
      "acquittement" => "string"),
    "notificationEvenementPatient" => array ( 
      "acquittement" => "string"),
  );

  function evenementPatient($messagePatient) {
    global $m;

    $newPatient = new CPatient();
     
    $domGetEvenement = new CHPrimXMLEvenementsPatients();
    $domGetEvenement->loadXML(utf8_decode($messagePatient));    
    $doc_valid = $domGetEvenement->schemaValidate();
    
    $data = $domGetEvenement->getEvenementPatientXML();

    $cip = new CDestinataireHprim();
    $cip->client_id = $data['idClient'];
    $cip->loadMatchingObject();
    $tagCip = $cip->tag;

    $id400 = new CIdSante400();
    //Paramétrage de l'id 400
    $id400->id400 = $data['idSource'];
    $id400->object_class = "CPatient";
    $id400->tag = $tagCip;

    // Variable en cas d'erreur de la sauvegarde des objets
    $msgID400   = "";
    $msgIPP     = "";
    $msgPatient = "";
    
    $messageAcquittement = "";

    // Cas 1 : Patient existe sur le SIP
    if($id400->loadMatchingObject()) {
      // Identifiant du patient sur le SIP
      $idPatientSIP = $id400->object_id;
      // Cas 1.1 : Pas d'identifiant cible
      if(!$data['idCible']) {
        // Le patient est connu sur le SIP
        if ($newPatient->load($idPatientSIP)) {
          // Mapping du patient
          $newPatient = $data['xpath']->createPatient($data['patient'], $newPatient);

          // Création de l'IPP
          $IPP = new CIdSante400();
          //Paramétrage de l'id 400
          $IPP->object_class = "CPatient";
          $IPP->tag = CAppUI::conf("dPpatients CPatient tag_ipp");
          if (!$IPP->tag)
           $IPP->tag = "sipIPP";
          $IPP->object_id = $idPatientSIP;

          // Chargement du dernier id externe de prescription du praticien s'il existe
          if (!$IPP->loadMatchingObject("id400 DESC")) {
            // Incrementation de l'id400
            $IPP->id400++;
            $IPP->id400 = str_pad($IPP->id400, 6, '0', STR_PAD_LEFT);

            $IPP->_id = null;
            $IPP->last_update = mbDateTime();
            $msgIPP = $IPP->store();
          }

          $msgPatient = $newPatient->store();
        }
      }
      // Cas 1.2 : Identifiant cible envoyé
      else {
        $IPP = new CIdSante400();
        //Paramétrage de l'id 400
        $IPP->object_class = "CPatient";
        $IPP->tag = CAppUI::conf("dPpatients CPatient tag_ipp");
        if (!$IPP->tag)
          $IPP->tag = "sipIPP";

        $IPP->id400 = $data['idCible'];
        $IPP->loadMatchingObject();

        $newPatient->_id = $IPP->object_id;
        $newPatient->loadMatchingObject();

        // Mapping du patient
        $newPatient = $data['xpath']->createPatient($data['patient'], $newPatient);
        $msgPatient = $newPatient->store();
      }
    }
    // Cas 2 : Patient n'existe pas sur le SIP
    else {
      // Mapping du patient
      $newPatient = $data['xpath']->createPatient($data['patient'], $newPatient);

      $msgPatient = $newPatient->store();

      // Création de l'identifiant externe TAG CIP + idSource
      $id400Patient = new CIdSante400();
      //Paramétrage de l'id 400
      $id400Patient->object_class = "CPatient";
      $id400Patient->tag = $tagCip;

      // Incrementation de l'id400
      $id400Patient->id400 = $data['idSource'];

      $id400Patient->object_id = $newPatient->_id;
      $id400Patient->_id = null;
      $id400Patient->last_update = mbDateTime();
      $msgID400 = $id400Patient->store();

      // Création de l'IPP
      $IPP = new CIdSante400();
      //Paramétrage de l'id 400
      $IPP->object_class = "CPatient";
      $IPP->tag = CAppUI::conf("dPpatients CPatient tag_ipp");
      if (!$IPP->tag)
         $IPP->tag = "sipIPP";

      // Chargement du dernier id externe de prescription du praticien s'il existe
      $IPP->loadMatchingObject("id400 DESC");

      // Incrementation de l'id400
      $IPP->id400++;
      $IPP->id400 = str_pad($IPP->id400, 6, '0', STR_PAD_LEFT);

      $IPP->object_id = $newPatient->_id;
      $IPP->_id = null;
      $IPP->last_update = mbDateTime();
      $msgIPP = $IPP->store();
    }
    
    $newPatient->_IPP = $IPP->id400;
    
    $erreur = $msgPatient.$msgID400.$msgIPP;
        
    if ($data['acquittement'] == "oui") {
    	// Gestion de l'acquittement
	    $domAcquittement = new CHPrimXMLAcquittementsPatients();
	    $domAcquittement->_identifiant = $data['identifiantMessage'];
	    $domAcquittement->_destinataire = $data['idClient'];
	    $domAcquittement->_destinataire_libelle = $data['libelleClient'];
    
    	$messageAcquittement = ($erreur) ? $domAcquittement->generateAcquittementsPatients("erreur", $erreur): $domAcquittement->generateAcquittementsPatients("OK", null);
    }
    
    // Dans le cas d'une erreur on retourne l'acquittement d'erreur au CIP sans notifier les autres CIPs
    if ($erreur) {
      return $messageAcquittement;
    }
      
    // Gestion des notifications
    $cip = new CDestinataireHprim();
    $listCip = $cip->loadList();

    // Liste des CIPs connus par le SIP
    foreach ($listCip as $_cip) {
      // Recherche si le patient possède un identifiant externe sur le SIP
      $id400 = new CIdSante400();
      //Paramétrage de l'id 400
      $id400->object_id = $newPatient->_id;
      $id400->object_class = "CPatient";
      $id400->tag = $_cip->tag;
    
      if($id400->loadMatchingObject()) 
        $newPatient->_id400 = $id400->id400;
       else     
        $newPatient->_id400 = null;
        
      $domEvenement = new CHPrimXMLEvenementsPatients();
      $domEvenement->_emetteur = CAppUI::conf('mb_id');
      $domEvenement->_destinataire = $data['idClient'];
      $domEvenement->_destinataire_libelle = $data['libelleClient'];
      
      $domEvenement->generateEvenementsPatients($newPatient, true);      
    }
    
    return $messageAcquittement;
  }

  function notificationEvenementPatient($messagePatient) {
    global $m;

    $newPatient = new CPatient();

    $domGetEvenement = new CHPrimXMLEvenementsPatients();
    $domGetEvenement->loadXML(utf8_decode($messagePatient));    
    $doc_valid = $domGetEvenement->schemaValidate();
    
    $data = $domGetEvenement->getEvenementPatientXML();
    
    $IPP = new CIdSante400();
    //Paramétrage de l'id 400
    $IPP->object_class = "CPatient";
    $IPP->tag = $data['idClient'];

    $IPP->id400 = $data['idSource'];
        
    // Variable en cas d'erreur de la sauvegarde des objets
    $msgIPP     = "";
    $msgPatient = "";
    
    $messageAcquittement = "";
    
    // Mapping du patient
    $newPatient = $data['xpath']->createPatient($data['patient'], $newPatient);
    // Evite de passer dans le sip handler
    $newPatient->_coms_from_sip = 1;
    
   // Le SIP renvoi l'identifiant local du patient
    if($data['idCible']) {
    	$tmpPatient = new CPatient();
      $tmpPatient->_id = $data['idCible'];
      $tmpPatient->load();
      
      if(($tmpPatient->nom == $newPatient->nom) && 
          ($tmpPatient->prenom == $newPatient->prenom) &&
          ($tmpPatient->naissance == $newPatient->naissance)) {
        
        $newPatient->_id = $data['idCible'];
      }        
    } 
    if(!$IPP->loadMatchingObject()) {
    	$msgPatient = $newPatient->store();
      
        $IPP->object_id = $newPatient->_id;
        $IPP->last_update = mbDateTime();
        $msgIPP = $IPP->store();
    } else {
      $newPatient->_id = $IPP->object_id;
      
      $msgPatient = $newPatient->store();
      $IPP->last_update = mbDateTime();
      $msgIPP = $IPP->store();
    }
         
    $erreur = $msgPatient.$msgIPP;
    
    if ($data['acquittement'] == "oui") {
      // Gestion de l'acquittement
      $domAcquittement = new CHPrimXMLAcquittementsPatients();
      $domAcquittement->_identifiant = $data['identifiantMessage'];
      $domAcquittement->_destinataire = $data['idClient'];
      $domAcquittement->_destinataire_libelle = $data['libelleClient'];
    
      $messageAcquittement = ($erreur) ? $domAcquittement->generateAcquittementsPatients("erreur", $erreur): $domAcquittement->generateAcquittementsPatients("OK", null);
    }
   
    return $messageAcquittement;
  }
}
?>
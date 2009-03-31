<?php

/**
 *  @package Mediboard
 *  @subpackage sip
 *  @version $Revision: $
 *  @author Yohann Poiron
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("sip", "soaphandler");

/**
 * The CHprimSoapHandler class
 */
class CHprimSoapHandler extends CSoapHandler {

  static $paramSpecs = array(
    "evenementPatient" => array ( 
      "messagePatient" => "string")
  );

  function evenementPatient($messagePatient) {
    global $m;

    // Récupération des informations du message XML
    $domGetEvenement = new CHPrimXMLEvenementsPatients();
    $domGetEvenement->loadXML(utf8_decode($messagePatient));
    $doc_valid = $domGetEvenement->schemaValidate();

    $data = $domGetEvenement->getEvenementPatientXML();

    // Création de l'échange
    $echange_hprim = new CEchangeHprim();
    if (CAppUI::conf('sip server')) {
      $echange_hprim->identifiant_emetteur = intval($data['identifiantMessage']);
      $echange_hprim->loadMatchingObject();
    }
    if (!$echange_hprim->_id) {
      $echange_hprim->emetteur = $data['idClient'];
      $echange_hprim->destinataire = CAppUI::conf('mb_id');
      $echange_hprim->identifiant_emetteur = $data['identifiantMessage'];
      $echange_hprim->type = "evenementsPatients";
      $echange_hprim->sous_type = "enregistrementPatient";
      $echange_hprim->message = $messagePatient;
    }
    $echange_hprim->date_production = mbDateTime();
    $echange_hprim->store();

    // Traitement du patient
    $msgID400 = $msgIPP = $msgPatient = $messageAcquittement = "";

    $newPatient = new CPatient();
    $newPatient->_hprim_initiator_id = $echange_hprim->_id;
    
    // Si SIP
    if (CAppUI::conf('sip server')) {
      $id400 = new CIdSante400();
      //Paramétrage de l'id 400
      $id400->id400 = $data['idSource'];
      $id400->object_class = "CPatient";
      $id400->tag = $data['idClient'];

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
            $IPP->tag = CAppUI::conf("mb_id");
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
            
            $newPatient->_IPP = $IPP->_id;
            $msgPatient = $newPatient->store();
          }
        }
        // Cas 1.2 : Identifiant cible envoyé
        else {
          $IPP = new CIdSante400();
          //Paramétrage de l'id 400
          $IPP->object_class = "CPatient";
          $IPP->tag = CAppUI::conf("mb_id");

          $IPP->id400 = $data['idCible'];
          $IPP->loadMatchingObject();

          $newPatient->_id = $IPP->object_id;
          $newPatient->loadMatchingObject();
mbTrace($newPatient, "Patient", true);
          // Mapping du patient
          $newPatient = $data['xpath']->createPatient($data['patient'], $newPatient);
          $newPatient->_IPP = $IPP->_id;
          $msgPatient = $newPatient->store();
        }
      }
      // Cas 2 : Patient n'existe pas sur le SIP
      else {
        // Mapping du patient
        $newPatient = $data['xpath']->createPatient($data['patient'], $newPatient);
        
        $newPatient->_no_ipp = 1;
        $msgPatient = $newPatient->store();

        // Création de l'identifiant externe TAG CIP + idSource
        $id400Patient = new CIdSante400();
        //Paramétrage de l'id 400
        $id400Patient->object_class = "CPatient";
        $id400Patient->tag = $data['idClient'];

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
        $IPP->tag = CAppUI::conf("mb_id");

        // Chargement du dernier id externe de prescription du praticien s'il existe
        $IPP->loadMatchingObject("id400 DESC");

        // Incrementation de l'id400
        $IPP->id400++;
        $IPP->id400 = str_pad($IPP->id400, 6, '0', STR_PAD_LEFT);

        $IPP->object_id = $newPatient->_id;
        $IPP->_id = null;
        $IPP->last_update = mbDateTime();
        $msgIPP = $IPP->store();
        
        $newPatient->_IPP = $IPP->id400;
        $newPatient->_no_ipp = 0;
        $newPatient->store();
      }
    } else {
      $IPP = new CIdSante400();
      //Paramétrage de l'id 400
      $IPP->object_class = "CPatient";
      $IPP->tag = $data['idClient'];
      $IPP->id400 = $data['idSource'];
    
      // Mapping du patient
      $newPatient = $data['xpath']->createPatient($data['patient'], $newPatient);
      
      // Evite de passer dans le sip handler
      $newPatient->_coms_from_hprim = 1;
  
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
      } else {
        $newPatient->_id = $IPP->object_id; 
        $msgPatient = $newPatient->store();
      }
      $IPP->last_update = mbDateTime();
      $msgIPP = $IPP->store();
    }
    
    $erreur = $msgPatient.$msgID400.$msgIPP;

    if ($data['acquittement'] == "oui") {
      // Gestion de l'acquittement
      $domAcquittement = new CHPrimXMLAcquittementsPatients();
      $domAcquittement->_identifiant = $data['identifiantMessage'];
      $domAcquittement->_destinataire = $data['idClient'];
      $domAcquittement->_destinataire_libelle = $data['libelleClient'];

      $messageAcquittement = ($erreur) ? $domAcquittement->generateAcquittementsPatients("erreur", $erreur): $domAcquittement->generateAcquittementsPatients("OK", null);
    }
    
    $echange_hprim->acquittement = $messageAcquittement;
    $echange_hprim->date_echange = mbDateTime();
    $echange_hprim->store();

    return $messageAcquittement;    
  }
}
?>
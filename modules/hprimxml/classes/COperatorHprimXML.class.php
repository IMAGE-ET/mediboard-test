<?php
/**
 * $Id:$
 * 
 * @package    Mediboard
 * @subpackage hprimxml
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision: 16236 $
 */

/**
 * The COperatorHprimXML class
 */
class COperatorHprimXML extends CEAIOperator {
  /**
   * Event dispatch
   *
   * @param CExchangeDataFormat $data_format Exchange data format
   *
   * @throws CMbException
   *
   * @return string Acquittement
   */
  function event(CExchangeDataFormat $data_format) {
    $msg     = $data_format->_message;
    /** @var CHPrimXMLEvenements $dom_evt */
    $dom_evt = $data_format->_family_message->getHPrimXMLEvenements($msg);

    $dom_evt_class = get_class($dom_evt);
    if (!in_array($dom_evt_class, $data_format->_messages_supported_class)) {
      throw new CMbException(CAppUI::tr("CEAIDispatcher-no_message_supported_for_this_actor", $dom_evt_class));
    }

    // Récupération des informations du message XML
    $dom_evt->loadXML($msg);
    
    // Récupération du noeud racine
    $root     = $dom_evt->documentElement;
    $nodeName = $root->nodeName;

    // Création de l'échange
    $echg_hprim = new CEchangeHprim();

    try {
      // Récupération des données de l'entête
      $data = $dom_evt->getEnteteEvenementXML($nodeName);

      $echg_hprim->load($data_format->_exchange_id);

      // Gestion des notifications ?
      if (!$echg_hprim->_id) {
        $echg_hprim->populateEchange($data_format, $dom_evt);
        $echg_hprim->identifiant_emetteur = $data['identifiantMessage'];
        $echg_hprim->message_valide       = 1;
      }

      $echg_hprim->loadRefsInteropActor();

      // Chargement des configs de l'expéditeur
      $echg_hprim->_ref_sender->getConfigs($data_format);

      $configs = $echg_hprim->_ref_sender->_configs;

      $display_errors = isset($configs["display_errors"]) ? $configs["display_errors"] : true;
      $doc_errors = $dom_evt->schemaValidate(null, false, $display_errors);
  
      // Gestion de l'acquittement
      $dom_acq                        = CHPrimXMLAcquittements::getAcquittementEvenementXML($dom_evt);
      $dom_acq->_identifiant_acquitte = $data['identifiantMessage'];
      $dom_acq->_sous_type_evt        = $dom_evt->sous_type;

      // Acquittement d'erreur d'un document XML recu non valide
      if ($doc_errors !== true) {
        $echg_hprim->populateEchange($data_format, $dom_evt);

        $dom_acq->_ref_echange_hprim = $echg_hprim;
        $msgAcq    = $dom_acq->generateAcquittements(
          $dom_acq instanceof CHPrimXMLAcquittementsServeurActivitePmsi ? "err" : "erreur", "E002", $doc_errors
        );
        $doc_valid = $dom_acq->schemaValidate(null, false, $display_errors);

        $echg_hprim->populateErrorEchange($msgAcq, $doc_valid, "erreur");

        return $msgAcq;
      }
      
      $echg_hprim->date_production = CMbDT::dateTime();
      $echg_hprim->store();
      
      if (!$data_format->_to_treatment) {
        return null;
      }

      $dom_evt->_ref_echange_hprim = $echg_hprim;
      $dom_acq->_ref_echange_hprim = $echg_hprim;
    
      // Message événement patient
      if ($dom_evt instanceof CHPrimXMLEvenementsPatients) {
        return self::eventPatient($dom_evt, $dom_acq, $echg_hprim, $data);
      }
      
      // Message serveur activité PMSI
      if ($dom_evt instanceof CHPrimXMLEvenementsServeurActivitePmsi) {
        return self::eventPMSI($dom_evt, $dom_acq, $echg_hprim, $data);
      }
    } 
    catch(Exception $e) {
      $echg_hprim->populateEchange($data_format, $dom_evt);
      
      $dom_acq = CHPrimXMLAcquittements::getAcquittementEvenementXML($dom_evt);
      
      // Type par défaut
      $dom_acq->_sous_type_evt        = "none";
      $dom_acq->_identifiant_acquitte = isset($data['identifiantMessage']) ? $data['identifiantMessage'] : "000000000";
      $dom_acq->_ref_echange_hprim    = $echg_hprim;

      $msgAcq = $dom_acq->generateAcquittements(
        $dom_acq instanceof CHPrimXMLAcquittementsServeurActivitePmsi ? "err" : "erreur", "E009", $e->getMessage(), null, $data
      );

      $doc_valid = $dom_acq->schemaValidate(null, false, false);
      $echg_hprim->populateErrorEchange($msgAcq, $doc_valid, "erreur");
      
      return $msgAcq;
    }

    return null;
  }
  
  /**
   * The message contains a collection of administrative notifications of events occurring to patients in a healthcare 
   * facility.
   *
   * @param CHPrimXMLEvenementsPatients    $dom_evt    DOM event PMSI
   * @param CHPrimXMLAcquittementsPatients $dom_acq    DOM acquittement PMSI
   * @param CEchangeHprim                  $echg_hprim Exchange H'XML
   * @param array                          &$data      Data
   * 
   * @return string Acquittement
   **/
  static function eventPatient(CHPrimXMLEvenementsPatients $dom_evt, CHPrimXMLAcquittementsPatients $dom_acq,
      CEchangeHprim $echg_hprim, &$data = array()
  ) {
    $newPatient = new CPatient();
    $newPatient->_eai_exchange_initiator_id = $echg_hprim->_id;
    
    $data = array_merge($data, $dom_evt->getContentsXML());
    if ($msgAcq = $dom_evt->isActionValide($data['action'], $dom_acq, $echg_hprim)) {
      return $msgAcq;
    }

    // Un événement concernant un patient appartient à l'une des six catégories suivantes :
    switch (get_class($dom_evt)) {
      // Enregistrement d'un patient avec son identifiant (ipp) dans le système
      case "CHPrimXMLEnregistrementPatient" :
        /** @var CHPrimXMLEnregistrementPatient $dom_evt */
        $echg_hprim->id_permanent = $data['idSourcePatient'];

        return $dom_evt->enregistrementPatient($dom_acq, $newPatient, $data);

      // Fusion de deux ipp
      case "CHPrimXMLFusionPatient" :
        /** @var CHPrimXMLFusionPatient $dom_evt */
        $echg_hprim->id_permanent = $data['idSourcePatient'];

        return $dom_evt->fusionPatient($dom_acq, $newPatient, $data);

      // Venue d'un patient dans l'établissement avec son numéro de venue
      case "CHPrimXMLVenuePatient" :
        /** @var CHPrimXMLVenuePatient $dom_evt */
        $echg_hprim->id_permanent = $data['idSourceVenue'];

        return $dom_evt->venuePatient($dom_acq, $newPatient, $data);

      // Fusion de deux venues
      case "CHPrimXMLFusionVenue" :
        /** @var CHPrimXMLFusionVenue $dom_evt */
        $echg_hprim->id_permanent = $data['idSourceVenue'];

        return $dom_evt->fusionVenue($dom_acq, $newPatient, $data);

      // Mouvement du patient dans une unité fonctionnelle ou médicale
      case "CHPrimXMLMouvementPatient" :
        /** @var CHPrimXMLMouvementPatient $dom_evt */
        $echg_hprim->id_permanent = $data['idSourceVenue'];

        return $dom_evt->mouvementPatient($dom_acq, $newPatient, $data);

        // Gestion des débiteurs d'une venue de patient
      case "CHPrimXMLDebiteursVenue" :
        /** @var CHPrimXMLDebiteursVenue $dom_evt */
        $echg_hprim->id_permanent = $data['idSourcePatient'];

        return $dom_evt->debiteursVenue($dom_acq, $newPatient, $data);

      default :
        return $dom_acq->generateAcquittements("erreur", "E007");
    }
  }
  
  /**
   * The message contains a collection of 
   * 
   * @param CHPrimXMLEvenementsServeurActivitePmsi    $dom_evt    DOM event PMSI
   * @param CHPrimXMLAcquittementsServeurActivitePmsi $dom_acq    DOM acquittement PMSI
   * @param CEchangeHprim                             $echg_hprim Exchange H'XML
   * @param array                                     &$data      Data
   * 
   * @return string Acquittement 
   **/
  static function eventPMSI(CHPrimXMLEvenementsServeurActivitePmsi $dom_evt, CHPrimXMLAcquittementsServeurActivitePmsi $dom_acq,
      CEchangeHprim $echg_hprim, &$data = array()
  ) {
    $data   = array_merge($data, $dom_evt->getContentsXML());
    if (CMbArray::get($data, "action") && $msgAcq = $dom_evt->isActionValide($data['action'], $dom_acq, $echg_hprim)) {
      return $msgAcq;
    } 
    
    $operation = new COperation();
    $operation->_eai_exchange_initiator_id = $echg_hprim->_id;
    
    return $dom_evt->handle($dom_acq, $operation, $data);
  }
}

<?php

/**
 * Serveur actes
 *
 * @category Hprimxml
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPrimXMLEvenementsServeurActes
 * Serveur actes
 */

class CHPrimXMLEvenementsServeurActes extends CHPrimXMLEvenementsServeurActivitePmsi {
  var $actions = array(
    'création'     => "création",
    'remplacement' => "remplacement",
    'modification' => "modification",
    'suppression'  => "suppression",
    'information'  => "information",
  );

  /**
   * Construct
   *
   * @return CHPrimXMLEvenementsServeurActes
   */
  function __construct() {
    $this->sous_type = "evenementServeurActe";
    $this->evenement = "evt_serveuractes";
    
    parent::__construct("serveurActes", "msgEvenementsServeurActes");
  }

  /**
   * Generate header message
   *
   * @return void
   */
  function generateEnteteMessage() {
    parent::generateEnteteMessage("evenementsServeurActes");
  }

  /**
   * Generate content message
   *
   * @param CCodable $codable Codable
   *
   * @return void
   */
  function generateFromOperation(CCodable $codable) {
    $evenementsServeurActes = $this->documentElement;

    $evenementServeurActe = $this->addElement($evenementsServeurActes, "evenementServeurActe");
    $this->addDateTimeElement($evenementServeurActe, "dateAction");

    // Ajout du patient
    $patient = $this->addElement($evenementServeurActe, "patient");
    switch ($codable->_class) {
      // CSejour / CConsultation
      case 'CSejour': case 'CConsultation':
        $mbPatient = $codable->_ref_patient;
        break;
      
      // COperation
      case 'COperation':
        $mbPatient = $codable->_ref_sejour->_ref_patient;
        break;
    }  
    $this->addPatient($patient, $mbPatient, false, true);
    
    // Ajout de la venue
    $venue = $this->addElement($evenementServeurActe, "venue");
    switch ($codable->_class) {
      // COperation / CConsultation
      case 'COperation': case 'CConsultation':
        $mbSejour = $codable->_ref_sejour;
        break;
      
      // CSejour
      case 'CSejour':
        $mbSejour = $codable;
        break;
    }
    $this->addVenue($venue, $mbSejour, null, true);
    
    // Ajout de l'intervention ou consultation ou sejour
    $intervention = $this->addElement($evenementServeurActe, "intervention");
    switch ($codable->_class) {
      // COperation 
      case 'COperation':
        $this->addIntervention($intervention, $codable, false, true);
        break;
        
      // CConsultation / CSejour
      // On ajoute seulement l'identifiant de la consultation ou séjour
      case 'CConsultation': case 'CSejour':
        $identifiant = $this->addElement($intervention, "identifiant");
        $this->addElement($identifiant, "emetteur", $codable->_id);
        break;
    }
      
    // Ajout des actes CCAM
    $actesCCAM = $this->addElement($evenementServeurActe, "actesCCAM");
    foreach ($codable->_ref_actes_ccam as $_acte_ccam) {
      if ((CAppUI::conf("dPpmsi transmission_actes") == "signature") && (!$_acte_ccam->signe || $_acte_ccam->sent)) {
        continue;
      }
      $this->addActeCCAM($actesCCAM, $_acte_ccam, $codable);
    }
    
    // Ajout des actes NGAP
    if (CAppUI::conf("hprimxml send_actes_ngap")) {
      $actesNGAP = $this->addElement($evenementServeurActe, "actesNGAP");
      
      $actes_ngap_excludes = array();
      if (CAppUI::conf("hprimxml actes_ngap_excludes")) {
        $actes_ngap_excludes = array_flip(explode("|", CAppUI::conf("hprimxml actes_ngap_excludes")));
      }

      foreach ($codable->_ref_actes_ngap as $_acte_ngap) {
        if (array_key_exists($_acte_ngap->code, $actes_ngap_excludes)) {
          continue;
        }
        $this->addActeNGAP($actesNGAP, $_acte_ngap, $codable);
      }
    }

    // Traitement final
    $this->purgeEmptyElements();
  }

  /**
   * Get content XML
   *
   * @return array
   */
  function getContentsXML() {
    $data = array();
    $xpath = new CHPrimXPath($this);   
    
    $evenementServeurActe = $xpath->queryUniqueNode("/hprim:evenementsServeurActes/hprim:evenementServeurActe");
    
    $data['patient']         = $xpath->queryUniqueNode("hprim:patient", $evenementServeurActe);
    $data['idSourcePatient'] = $this->getIdSource($data['patient']);
    $data['idCiblePatient']  = $this->getIdCible($data['patient']);
    
    $data['venue']           = $xpath->queryUniqueNode("hprim:venue", $evenementServeurActe);
    $data['idSourceVenue']   = $this->getIdSource($data['venue']);
    $data['idCibleVenue']    = $this->getIdCible($data['venue']);
    
    $data['intervention']         = $xpath->queryUniqueNode("hprim:intervention", $evenementServeurActe);
    $data['idSourceIntervention'] = $this->getIdSource($data['intervention'], false);
    $data['idCibleIntervention']  = $this->getIdCible($data['intervention'], false);
    
    $data['actesCCAM']         = $xpath->queryUniqueNode("hprim:actesCCAM", $evenementServeurActe);  
    $data['idSourceActesCCAM'] = $this->getIdSource($data['actesCCAM'], false);
    $data['idCibleActesCCAM']  = $this->getIdCible($data['actesCCAM'], false);
    
    return $data; 
  }
  
  /**
   * Enregistrement des actes CCAM
   * 
   * @param CHPrimXMLAcquittementsServeurActivitePmsi $dom_acq  DOM Acquittement
   * @param CMbObject                                 $mbObject Object
   * @param array                                     $data     Data that contain the nodes
   * 
   * @return string Acquittement 
   **/
  function handle(CHPrimXMLAcquittementsServeurActivitePmsi $dom_acq, CMbObject $mbObject, $data) {
    return;
    $messageAcquittement = null;
    
     // Si pas Serveur d'Actes
    if (!CAppUI::conf('dPpmsi server')) { 
      $dest_hprim = new CDestinataireHprim();
      $dest_hprim->nom = $data['idClient'];
      $dest_hprim->loadMatchingObject();
      
      $avertissement = null;
      
      // Récupération de l'élément patient du message
      $elPatient  = $data['patient'];
      
      // Mapping actes CCAM
      $actesCCAM = $this->mappingActesCCAM($data);
          
      // Acquittement d'erreur : identifiants source du patient / séjour non fournis
      if (!$data['idSourcePatient'] || !$data['idSourceVenue']) {
        $messageAcquittement = $domAcquittement->generateAcquittements("err", "E206", null, null, $actesCCAM, $elPatient);
        $doc_valid = $domAcquittement->schemaValidate();
        
        $echange_hprim->setAckError($doc_valid, $messageAcquittement, "err");
        return $messageAcquittement;
      }
      
      // IPP non connu => message d'erreur
      $IPP = new CIdSante400();
      $IPP->object_class = "CPatient";
      $IPP->tag = $dest_hprim->_tag_patient;
      $IPP->id400 = $data['idSourcePatient'];

      if (!$IPP->loadMatchingObject()) {
        $messageAcquittement = $domAcquittement->generateAcquittements("err", "E013", null, null, $actesCCAM, $elPatient);
        $doc_valid = $domAcquittement->schemaValidate();
        
        $echange_hprim->setAckError($doc_valid, $messageAcquittement, "err");
        return $messageAcquittement;    
      }
      
      // Chargement du patient
      $patient = new CPatient();   
      $patient->load($IPP->object_id);
      
      // Num dossier non connu => message d'erreur
      $num_dos = new CIdSante400();
      $num_dos->object_class = "CSejour";
      $num_dos->tag = $dest_hprim->_tag_sejour;
      $num_dos->id400 = $data['idSourceVenue'];
      
      if (!$num_dos->loadMatchingObject()) {
        $messageAcquittement = $domAcquittement->generateAcquittements("err", "E014", null, null, $actesCCAM, $elPatient);
        $doc_valid = $domAcquittement->schemaValidate();
        
        $echange_hprim->setAckError($doc_valid, $messageAcquittement, "err");
        return $messageAcquittement;    
      }
      
      // Chargement du séjour
      $sejour = new CSejour();
      $sejour->load($num_dos->object_id);
      
      // Si patient H'XML est différent du séjour
      if ($sejour->patient_id != $patient->_id) {
        $messageAcquittement = $domAcquittement->generateAcquittements("err", "E015", null, null, $actesCCAM, $elPatient);
        $doc_valid = $domAcquittement->schemaValidate();
        
        $echange_hprim->setAckError($doc_valid, $messageAcquittement, "err");
        return $messageAcquittement;    
      }

      // Chargement du patient du séjour
      $sejour->loadRefPatient();
      
      // Récupération de la date de l'intervention
      $dateInterv = $this->getDateInterv($data['intervention']);
      
      // Chargement des interventions du séjour
      $sejour->loadRefsOperations();  
      $operation = null;
      foreach ($sejour->_ref_operations as $_operation) {
        if (mbDate($_operation->_datetime)) {
          $operation = $_operation;
        }
      }
      
      /* @FIXME Penser à virer par la suite pour rattacher des actes à un séjour... */
      if (!$operation) {
        $messageAcquittement = $domAcquittement->generateAcquittements("err", "E201", null, null, $actesCCAM, $elPatient);
        $doc_valid = $domAcquittement->schemaValidate();
        
        $echange_hprim->setAckError($doc_valid, $messageAcquittement, "err");
        return $messageAcquittement; 
      }     

      $operation->loadRefsActesCCAM();
      $mbActesCCAM = $operation->_ref_actes_ccam;
      
      /*mbTrace($actesCCAM, "actesCCAM", true);
      mbTrace($mbActesCCAM, "mbActesCCAM", true);
      foreach () {
      
      }*/
      $messageAcquittement = $domAcquittement->generateAcquittements("ok", "I201", null, null, $actesCCAM, $elPatient);
    }
    
    $echange_hprim->_acquittement = $messageAcquittement;
    $echange_hprim->date_echange = mbDateTime();
    $echange_hprim->setObjectIdClass("CSejour", $data['idCibleVenue']);
    $echange_hprim->store();

    return $messageAcquittement;
  }
}
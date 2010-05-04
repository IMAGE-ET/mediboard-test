<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hprimxml", "evenementsserveuractivitepmsi");

class CHPrimXMLEvenementsServeurActes extends CHPrimXMLEvenementsServeurActivitePmsi {
  function __construct() {
    $this->sous_type = "evenementServeurActe";
    $this->evenement = "evt_serveuractes";
    
    parent::__construct("serveurActes", "msgEvenementsServeurActes");
  }

  function generateEnteteMessage() {
    $evenementsServeurActes = $this->addElement($this, "evenementsServeurActes", null, "http://www.hprim.org/hprimXML");
    $this->addAttribute($evenementsServeurActes, "version", CAppUI::conf('hprimxml evt_serveuractes version'));
    
    $this->addEnteteMessage($evenementsServeurActes);
  }
  
  function generateFromOperation($mbOp) {
    $evenementsServeurActes = $this->documentElement;

    $evenementServeurActe = $this->addElement($evenementsServeurActes, "evenementServeurActe");
    $this->addDateTimeElement($evenementServeurActe, "dateAction");

    // Ajout du patient
    $mbPatient =& $mbOp->_ref_sejour->_ref_patient;
    $patient = $this->addElement($evenementServeurActe, "patient");
    $this->addPatient($patient, $mbPatient, false, true);
    
    // Ajout de la venue, c'est-à-dire le séjour
    $mbSejour =& $mbOp->_ref_sejour;
    $venue = $this->addElement($evenementServeurActe, "venue");
    $this->addVenue($venue, $mbSejour, null, true);
    
    // Ajout de l'intervention
    $intervention = $this->addElement($evenementServeurActe, "intervention");
    $this->addIntervention($intervention, $mbOp);
    
    // Ajout des actes CCAM
    $actesCCAM = $this->addElement($evenementServeurActe, "actesCCAM");
    foreach ($mbOp->_ref_actes_ccam as $mbActe) {
      $this->addActeCCAM($actesCCAM, $mbActe, $mbOp);
    }

    // Traitement final
    $this->purgeEmptyElements();
  }
  
  function getEnteteServeurActesXML() {
    $data = array();
    $xpath = new CMbXPath($this, true);   

    $entete = $xpath->queryUniqueNode("/hprim:evenementsServeurActes/hprim:enteteMessage");
    
    $data['identifiantMessage'] = $xpath->queryTextNode("hprim:identifiantMessage", $entete);
    $agents = $xpath->queryUniqueNode("hprim:emetteur/hprim:agents", $entete);
    $systeme = $xpath->queryUniqueNode("hprim:agent[@categorie='système']", $agents, false);
    $this->destinataire = $data['idClient'] = $xpath->queryTextNode("hprim:code", $systeme);
    $data['libelleClient'] = $xpath->queryTextNode("hprim:libelle", $systeme);    
    
    return $data;
  }
  
  function getServeurActesXML() {
    $data = array();
    $xpath = new CMbXPath($this, true);   
    
    $evenementServeurActe = $xpath->queryUniqueNode("/hprim:evenementsServeurActes/hprim:evenementServeurActe");
    
    $data['patient']         = $xpath->queryUniqueNode("hprim:patient", $evenementServeurActe);
    $data['idSourcePatient'] = $this->getIdSource($data['patient']);
    $data['idCiblePatient']  = $this->getIdCible($data['patient']);
    
    $data['venue']         = $xpath->queryUniqueNode("hprim:patient", $evenementServeurActe);
    $data['idSourceVenue'] = $this->getIdSource($data['venue']);
    $data['idCibleVenue']  = $this->getIdCible($data['venue']);
    
    $data['intervention']         = $xpath->queryUniqueNode("hprim:intervention", $evenementServeurActe);
    
    $data['actesCCAM']     = $xpath->queryUniqueNode("hprim:actesCCAM", $evenementServeurActe);  
    
    return $data; 
  }
  
  /**
   * Enregistrement des actes CCAM
   * @param CHPrimXMLAcquittementsServeurActes $domAcquittement
   * @param CEchangeHprim $echange_hprim
   * @param array $data
   * @return CHPrimXMLAcquittementsServeurActes $messageAcquittement 
   **/
  function serveurActes($domAcquittement, &$echange_hprim, $data) {
    $messageAcquittement = null;
    
     // Si pas Serveur d'Actes
    if (!CAppUI::conf('dPpmsi server')) { 
      $dest_hprim = new CDestinataireHprim();
      $dest_hprim->nom = $data['idClient'];
      $dest_hprim->loadMatchingObject();
      
      $avertissement = null;
      
      // Mapping des actes CCAM
      $actesCCAM = $this->mappingActesCCAM($data);
          
      // Acquittement d'erreur : identifiants source du patient / séjour non fournis
      if (!$data['idSourcePatient'] || !$data['idSourceVenue']) {
        $messageAcquittement = $domAcquittement->generateAcquittementsServeurActivitePmsi("err", "E206", $actesCCAM);
        $doc_valid = $domAcquittement->schemaValidate();
        
        $echange_hprim->setAckError($doc_valid, $messageAcquittement, "err");
        return $messageAcquittement;
      }
      
      // IPP non connu => message d'erreur
      $IPP = new CIdSante400();
      $IPP->object_class = "CPatient";
      $IPP->tag = $dest_hprim->_tag_patient;
      $IPP->id400 = $data['idSourcePatient'];
      if(!$IPP->loadMatchingObject()) {
        $messageAcquittement = $domAcquittement->generateAcquittementsServeurActivitePmsi("err", "E013", $actesCCAM);
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
      if(!$num_dos->loadMatchingObject()) {
        $messageAcquittement = $domAcquittement->generateAcquittementsServeurActivitePmsi("err", "E014", $actesCCAM);
        $doc_valid = $domAcquittement->schemaValidate();
        
        $echange_hprim->setAckError($doc_valid, $messageAcquittement, "err");
        return $messageAcquittement;    
      }
      
      // Chargement du séjour
      $sejour = CSejour();
      $sejour->load($num_dos->object_id);
      
      // Si patient H'XML est différent du séjour
      if ($sejour->patient_id != $patient->_id) {
        $messageAcquittement = $domAcquittement->generateAcquittementsServeurActivitePmsi("err", "E015", $actesCCAM);
        $doc_valid = $domAcquittement->schemaValidate();
        
        $echange_hprim->setAckError($doc_valid, $messageAcquittement, "err");
        return $messageAcquittement;    
      }
            
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
      if (!$_operation) {
        $messageAcquittement = $domAcquittement->generateAcquittementsServeurActivitePmsi("err", "E201", $actesCCAM);
        $doc_valid = $domAcquittement->schemaValidate();
        
        $echange_hprim->setAckError($doc_valid, $messageAcquittement, "err");
        return $messageAcquittement; 
      }     
      
      //$messageAcquittement = $domAcquittement->generateAcquittementsServeurActivitePmsi($avertissement ? "avt" : "ok", $codes, $avertissement ? $avertissement : substr($commentaire, 0, 4000)); 
    }
    
    $echange_hprim->acquittement = $messageAcquittement;
    $echange_hprim->date_echange = mbDateTime();
    $echange_hprim->setObjectIdClass("CSejour", $data['idCibleVenue']);
    $echange_hprim->store();

    return $messageAcquittement;
  }
}
?>
<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 8208 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHPrimXMLAcquittementsServeurActivitePmsi extends CHPrimXMLAcquittements {
  static $evenements = array(
    'evenementPMSI'                => "CHPrimXMLAcquittementsPmsi",
    'evenementServeurActe'         => "CHPrimXMLAcquittementsServeurActes",
    'evenementServeurEtatsPatient' => "CHPrimXMLAcquittementsServeurEtatsPatient",
    'evenementFraisDivers'         => "CHPrimXMLAcquittementsFraisDivers"
  );
  
  var $acquittement = null;
  
  var $_identifiant_acquitte = null;
  var $_sous_type_evt        = null;
  var $_codes_erreurs        = null;
  
  static function getEvtAcquittement(CHPrimXMLEvenementsServeurActivitePmsi $dom_evt) {
    $acq_evt = null;
    if ($dom_evt instanceof CHPrimXMLEvenementsServeurActes) {
      $acq_evt = new CHPrimXMLAcquittementsServeurActes;
    }
    else if ($dom_evt instanceof CHPrimXMLEvenementsPmsi) {
      $acq_evt = new CHPrimXMLAcquittementsPmsi;
    }
    else if ($dom_evt instanceof CHPrimXMLEvenementsFraisDivers) {
      $acq_evt = new CHPrimXMLAcquittementsFraisDivers;
    }
    
    return $acq_evt;
  }
  
  function __construct($messageAcquittement) {     
    parent::__construct("serveurActivitePmsi", $messageAcquittement);
  }
  
  function generateEnteteMessageAcquittement($statut) {
    $echg_hprim      = $this->_ref_echange_hprim;
    $identifiant     = $echg_hprim->_id ? str_pad($echg_hprim->_id, 6, '0', STR_PAD_LEFT) : "ES{$this->now}";
    
    $acquittementsServeurActivitePmsi = $this->addElement($this, $this->acquittement, null, "http://www.hprim.org/hprimXML");
    $this->addAttribute($acquittementsServeurActivitePmsi, "version", CAppUI::conf("hprimxml $this->evenement version"));
    
    $enteteMessageAcquittement = $this->addElement($acquittementsServeurActivitePmsi, "enteteMessage");
    $this->addAttribute($enteteMessageAcquittement, "statut", $statut);

    $this->addElement($enteteMessageAcquittement, "identifiantMessage", $this->identifiant);
    $this->addDateTimeElement($enteteMessageAcquittement, "dateHeureProduction");

    $emetteur = $this->addElement($enteteMessageAcquittement, "emetteur");
    $agents = $this->addElement($emetteur, "agents");
    $this->addAgent($agents, "application", "MediBoard", "Gestion des Etablissements de Santé");
    $group = CGroups::loadCurrent();
    $group->loadLastId400();
    $this->addAgent($agents, $this->getAttSysteme(), $this->emetteur, $group->text);

    $destinataire = $this->addElement($enteteMessageAcquittement, "destinataire");
    $agents = $this->addElement($destinataire, "agents");
    $this->addAgent($agents, "application", $this->destinataire, $this->destinataire_libelle);

    $this->addElement($enteteMessageAcquittement, "identifiantMessageAcquitte", $this->identifiant);
  }
  
  function addReponses($statut, $codes, $actesCCAM, $elPatient, $mbObject = null, $commentaires = null) {
    $acquittementsServeurActivitePmsi = $this->documentElement;
    
    $mbPatient = $mbSejour = null;    
    if ($mbObject instanceof CSejour) {
      $mbPatient =& $mbObject->_ref_patient;
      $mbSejour  =& $mbObject;
    }
    if ($mbObject instanceof COperation) {
      $mbPatient =& $mbObject->_ref_sejour->_ref_patient;
      $mbSejour  =& $mbObject->_ref_sejour;
    } 
     
    // Ajout des réponses
    $reponses = $this->addElement($acquittementsServeurActivitePmsi, "reponses");
    // Ajout du patient et de la venue
    if ($mbPatient) {
      $patient = $this->addElement($reponses, "patient");
      $this->addPatient($patient, $mbPatient, false, true);
    } else {
      $reponses->appendChild($this->importNode($elPatient, true));
    }
    if ($mbSejour) {    
      $venue = $this->addElement($reponses, "venue");
      $this->addVenue($venue, $mbSejour, false, true);
    }
    
    foreach ($actesCCAM as $_acteCCAM) {
      $this->addReponse($reponses, $statut, $codes, $_acteCCAM, $mbObject, $commentaires);
    }   
  }

  function generateAcquittements($statut, $codes, $commentaires = null, $mbObject = null, $actesCCAM, $elPatient) {
    $this->emetteur = CAppUI::conf('mb_id');
    $this->date_production = mbDateTime();

    $this->generateEnteteMessageAcquittement($statut);
    $this->addReponses($statut, $codes, $actesCCAM, $elPatient, $mbObject, $commentaires);
     
    // Traitement final
    $this->purgeEmptyElements();

    $this->saveTempFile();
    $messageAckServeurActivitePmsi = utf8_encode($this->saveXML());

    return $messageAckServeurActivitePmsi;
  }
  
  function getStatutAcquittement() {
    return $this->getStatutAcquittementServeurActivitePmsi();
  }
  
  function getStatutAcquittementServeurActivitePmsi() {
    $xpath = new CHPrimXPath($this);

    return $xpath->queryAttributNode("/hprim:$this->acquittement/hprim:enteteMessage", null, "statut"); 
  }
  
  function getAcquittementsServeurActivitePmsi() {
    $xpath = new CHPrimXPath($this);
    
    $statut = $xpath->queryAttributNode("/hprim:$this->acquittement/hprim:enteteMessage", null, "statut"); 
    
    $query = "/hprim:$this->evenement/hprim:enteteMessage";
    $enteteMessageAcquittement = $xpath->queryUniqueNode($query);  
    
    $data['identifiantMessage'] = $xpath->queryTextNode("hprim:identifiantMessage", $enteteMessageAcquittement);
    $agents = $xpath->queryUniqueNode("hprim:emetteur/hprim:agents", $enteteMessageAcquittement);
    $systeme = $xpath->queryUniqueNode("hprim:agent[@categorie='".$this->getAttSysteme()."']", $agents);
    $data['idClient'] = $xpath->queryTextNode("hprim:code", $systeme);
    $data['libelleClient'] = $xpath->queryTextNode("hprim:libelle", $systeme);
    
    $data['identifiantMessageAcquitte'] = $xpath->queryTextNode("hprim:identifiantMessageAcquitte", $enteteMessageAcquittement);
    
    return $data;
  }
  
  function getAcquittementReponsesServeurActivitePmsi() {
    $xpath = new CHPrimXPath($this);
    
    $statut = $xpath->queryAttributNode("/hprim:a$this->acquittement/hprim:enteteMessage", null, "statut"); 
    
    $query = "/hprim:$this->evenement/hprim:enteteMessageAcquittement";
    $enteteMessageAcquittement = $xpath->queryUniqueNode($query);  
    
    $observations = array();
    if ($statut == "ok") {
      $d = array();
      $observations[] = &$d;
        
      $observation = $xpath->queryUniqueNode("hprim:observation", $enteteMessageAcquittement);
      $d['code'] = chunk_split($xpath->queryTextNode("hprim:code", $observation, "", false), 4, ' ');
      $d['libelle'] = $xpath->queryTextNode("hprim:libelle", $observation, "", false);
      $d['commentaire'] = $xpath->queryTextNode("hprim:commentaire", $observation, "", false);
    } else {
      $query = "/hprim:$this->evenements/hprim:reponses/*";
      $reponses = $xpath->query($query);   

      foreach ($reponses as $_reponse) {
        $d = array();

        $observation = $xpath->queryUniqueNode("hprim:observations/hprim:observation", $_reponse);
        $d['code'] = chunk_split($xpath->queryTextNode("hprim:code", $observation, "", false), 4, ' ');
        $d['libelle'] = $xpath->queryTextNode("hprim:libelle", $observation, "", false);
        $d['commentaire'] = $xpath->queryTextNode("hprim:commentaire", $observation, "", false);
        $observations[] = $d;
      }
    }  
    
    return $observations;
  } 
}

?>
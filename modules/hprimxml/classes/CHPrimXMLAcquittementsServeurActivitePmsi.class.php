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
    'evenementFraisDivers'         => "CHPrimXMLAcquittementsFraisDivers",
    'evenementServeurIntervention' => "CHPrimXMLAcquittementsServeurIntervention",
  );
  
  var $acquittement = null;
  
  var $_identifiant_acquitte = null;
  var $_sous_type_evt        = null;
  var $_codes_erreurs        = array(
    "ok"  => "ok",
    "avt" => "avt",
    "err" => "err"
  );
  
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
    else if ($dom_evt instanceof CHPrimXMLEvenementsServeurIntervention) {
      $acq_evt = new CHPrimXMLAcquittementsServeurIntervention;
    }
    
    return $acq_evt;
  }
  
  function generateEnteteMessageAcquittement($statut, $codes = null, $commentaires = null) {
    $echg_hprim      = $this->_ref_echange_hprim;
    $identifiant     = $echg_hprim->_id ? str_pad($echg_hprim->_id, 6, '0', STR_PAD_LEFT) : "ES{$this->now}";
    
    $acquittementsServeurActivitePmsi = $this->addElement($this, $this->acquittement, null, "http://www.hprim.org/hprimXML");
    $this->addAttribute($acquittementsServeurActivitePmsi, "version", CAppUI::conf("hprimxml $this->evenement version"));
    
    $enteteMessageAcquittement = $this->addElement($acquittementsServeurActivitePmsi, "enteteMessage");
    $this->addAttribute($enteteMessageAcquittement, "statut", $statut);

    $this->addElement($enteteMessageAcquittement, "identifiantMessage", $identifiant);
    $this->addDateTimeElement($enteteMessageAcquittement, "dateHeureProduction");
    
    $emetteur = $this->addElement($enteteMessageAcquittement, "emetteur");
    $agents = $this->addElement($emetteur, "agents");
    $this->addAgent($agents, "application", "MediBoard", "Gestion des Etablissements de Santé");
    $group = CGroups::loadCurrent();
    $group->loadLastId400();
    $this->addAgent($agents, $this->getAttSysteme(), CAppUI::conf('mb_id'), $group->text);

    $echg_hprim->loadRefsInteropActor();
    // Pour un acquittement l'emetteur du message devient destinataire
    $destinataire = $this->addElement($enteteMessageAcquittement, "destinataire");
    $agents = $this->addElement($destinataire, "agents");
    $this->addAgent($agents, "application", $echg_hprim->_ref_sender->nom, $echg_hprim->_ref_sender->libelle);
    /* @todo Doit-on gérer le système du destinataire ? */
    //$this->addAgent($agents, "système", $group->_id, $group->text);

    $this->addElement($enteteMessageAcquittement, "identifiantMessageAcquitte", $this->_identifiant_acquitte);
  }
  
  function addReponses($statut, $codes, $commentaires = null, $mbObject = null, $data = array()) {
    $acquittementsServeurActivitePmsi = $this->documentElement;
    
    $mbPatient = $mbSejour = null;    
    if ($mbObject instanceof CSejour) {
      $mbPatient = $mbObject->loadRefPatient();
      $mbSejour  = $mbObject;
    }
    if ($mbObject instanceof COperation) {
      $mbPatient = $mbObject->loadRefSejour()->loadRefPatient();
      $mbSejour  = $mbObject->_ref_sejour;
    } 

    // Ajout des réponses
    $reponses = $this->addElement($acquittementsServeurActivitePmsi, "reponses");

    // Ajout du patient et de la venue
    $patient = $this->addElement($reponses, "patient");
    if (isset($mbPatient->_id)) {
      $this->addPatient($patient, $mbPatient, false, true);
    }
    
    $venue = $this->addElement($reponses, "venue");
    if (isset($mbSejour->_id)) {
      $this->addVenue($venue, $mbSejour, false, true);
    }
    
    // Génération des réponses en fonction du type de l'acquittement
    switch (get_class($this)) {
      case "CHPrimXMLAcquittementsServeurActes" :
        $actesCCAM = $data;
        
        foreach ($actesCCAM as $_acteCCAM) {
          $this->addReponseCCAM($reponses, $statut, $codes, $_acteCCAM, $mbObject, $commentaires);
        } 
        
        break;
      case "CHPrimXMLAcquittementsServeurIntervention" :
        $this->addReponseIntervention($reponses, $statut, $codes, null, $mbObject, $commentaires);
        break;
    }
  }

  function generateAcquittements($statut, $codes, $commentaires = null, $mbObject = null, $data = array()) {
    $this->date_production = mbDateTime();

    $this->generateEnteteMessageAcquittement($statut);
    $this->addReponses($statut, $codes, $commentaires, $mbObject, $data);
     
    // Traitement final
    $this->purgeEmptyElements();

    return utf8_encode($this->saveXML());
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
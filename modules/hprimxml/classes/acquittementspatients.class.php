<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hprimxml", "hprimxmldocument");

class CHPrimXMLAcquittementsPatients extends CHPrimXMLDocument {
  var $_identifiant_acquitte = null;
  var $_sous_type_evt        = null;
  var $_codes_erreurs        = null;
  
	static function getVersionAcquittementsPatients() {    
    return "msgAcquittementsPatients".str_replace(".", "", CAppUI::conf('hprimxml evt_patients version'));
  } 
	
  function __construct() {
    $this->evenement = "evt_patients";
     
    parent::__construct("patients", self::getVersionAcquittementsPatients());
  }

  function generateEnteteMessageAcquittement($statut, $codes = null, $commentaires = null) {
    $echg_hprim      = $this->_ref_echange_hprim;
    $identifiant     = isset($echg_hprim->_id) ? str_pad($echg_hprim->_id, 6, '0', STR_PAD_LEFT) : "ES{$this->now}";
    
    $acquittementsPatients = $this->addElement($this, "acquittementsPatients", null, "http://www.hprim.org/hprimXML");

    $enteteMessageAcquittement = $this->addElement($acquittementsPatients, "enteteMessageAcquittement");
    $this->addAttribute($enteteMessageAcquittement, "statut", $statut);

    $this->addElement($enteteMessageAcquittement, "identifiantMessage", $identifiant);
    $this->addDateTimeElement($enteteMessageAcquittement, "dateHeureProduction");

    $emetteur = $this->addElement($enteteMessageAcquittement, "emetteur");
    $agents = $this->addElement($emetteur, "agents");
    $this->addAgent($agents, "application", "MediBoard", "Gestion des Etablissements de Santé");
    $group = CGroups::loadCurrent();
    $group->loadLastId400();
    $this->addAgent($agents, "système", CAppUI::conf('mb_id'), $group->text);

    $echg_hprim->loadRefsDestinataireHprim();
    // Pour un acquittement l'emetteur du message devient destinataire
    $destinataire = $this->addElement($enteteMessageAcquittement, "destinataire");
    $agents = $this->addElement($destinataire, "agents");
    $this->addAgent($agents, "application", $echg_hprim->_ref_emetteur->nom, $echg_hprim->_ref_emetteur->libelle);
    /* @todo Doit-on gérer le système du destinataire ? */
    //$this->addAgent($agents, "système", $group->_id, $group->text);

    $this->addElement($enteteMessageAcquittement, "identifiantMessageAcquitte", $this->_identifiant_acquitte);
    
    if ($statut == "OK") {
      if (is_array($codes)) {
        $_codes = $_libelle_codes = "";
        foreach ($codes as $code) {
          $_codes .= $code;
          $_libelle_codes .= CAppUI::tr("hprimxml-error-$code");
        }
        $this->addObservation($enteteMessageAcquittement, $_codes, $_libelle_codes, $commentaires);
      } else {
        $this->addObservation($enteteMessageAcquittement, $codes, CAppUI::tr("hprimxml-error-$codes"), $commentaires);
      }
    }
  }

  function addErreursAvertissements($statut, $codes, $commentaires = null, $mbObject = null) {
    $acquittementsPatients = $this->documentElement;
     
    $erreursAvertissements = $this->addElement($acquittementsPatients, "erreursAvertissements");
     
    if (is_array($codes)) {
      foreach ($codes as $code) {
        $this->addErreurAvertissement($erreursAvertissements, $statut, $code, CAppUI::tr("hprimxml-error-$code"), $commentaires, $mbObject);
      }
    } else {
      $this->addErreurAvertissement($erreursAvertissements, $statut, $codes, CAppUI::tr("hprimxml-error-$codes"), $commentaires, $mbObject);
    }   
  }

  function generateAcquittementsPatients($statut, $codes, $commentaires = null, $mbObject = null) {
    if ($statut != "OK") {
      $this->generateEnteteMessageAcquittement($statut);
      $this->addErreursAvertissements($statut, $codes, $commentaires, $mbObject);
    } else {
      $this->generateEnteteMessageAcquittement($statut, $codes, $commentaires);
    }

    $this->saveTempFile();
    $messageAcquittementPatient = utf8_encode($this->saveXML());

    return $messageAcquittementPatient;
  }
   
  function getStatutAcquittementPatient() {
    $xpath = new CHPrimXPath($this);
        
    return $xpath->queryAttributNode("/hprim:acquittementsPatients/hprim:enteteMessageAcquittement", null, "statut"); 
  }
  
  function getAcquittementsPatients() {
    $xpath = new CHPrimXPath($this);
    
    $statut = $xpath->queryAttributNode("/hprim:acquittementsPatients/hprim:enteteMessageAcquittement", null, "statut"); 
    
    $query = "/hprim:acquittementsPatients/hprim:enteteMessageAcquittement";
    $enteteMessageAcquittement = $xpath->queryUniqueNode($query);  
    
    $data['identifiantMessage'] = $xpath->queryTextNode("hprim:identifiantMessage", $enteteMessageAcquittement);
    $agents = $xpath->queryUniqueNode("hprim:emetteur/hprim:agents", $enteteMessageAcquittement);
    $systeme = $xpath->queryUniqueNode("hprim:agent[@categorie='système']", $agents);
    $data['idClient'] = $xpath->queryTextNode("hprim:code", $systeme);
    $data['libelleClient'] = $xpath->queryTextNode("hprim:libelle", $systeme);
    
    $data['identifiantMessageAcquitte'] = $xpath->queryTextNode("hprim:identifiantMessageAcquitte", $enteteMessageAcquittement);
    
    return $data;
  }
  
  function getAcquittementObservationPatients() {
    $xpath = new CHPrimXPath($this);
    
    $statut = $xpath->queryAttributNode("/hprim:acquittementsPatients/hprim:enteteMessageAcquittement", null, "statut"); 
    
    $query = "/hprim:acquittementsPatients/hprim:enteteMessageAcquittement";
    $enteteMessageAcquittement = $xpath->queryUniqueNode($query);  
    
    $observations = array();
    if ($statut == "OK") {
      $d = array();
      $observations[] = &$d;
        
      $observation = $xpath->queryUniqueNode("hprim:observation", $enteteMessageAcquittement);
      $d['code'] = chunk_split($xpath->queryTextNode("hprim:code", $observation, "", false), 4, ' ');
      $d['libelle'] = $xpath->queryTextNode("hprim:libelle", $observation, "", false);
      $d['commentaire'] = $xpath->queryTextNode("hprim:commentaire", $observation, "", false);
    } else {
      $query = "/hprim:acquittementsPatients/hprim:erreursAvertissements/*";
      $erreursAvertissements = $xpath->query($query);   

      foreach ($erreursAvertissements as $erreurAvertissement) {
        $d = array();

        $observation = $xpath->queryUniqueNode("hprim:observations/hprim:observation", $erreurAvertissement);
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
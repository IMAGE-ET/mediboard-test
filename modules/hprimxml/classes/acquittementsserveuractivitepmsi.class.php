<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 8208 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hprimxml", "hprimxmldocument");

class CHPrimXMLAcquittementsServeurActivitePmsi extends CHPrimXMLDocument {
  var $acquittement = null;
  
  var $_sous_type_evt = null;
  var $_codes_erreurs = null;
  
  function __construct($messageAcquittement) {     
    parent::__construct("serveurActivitePmsi", $messageAcquittement);
  }
	
  function generateEnteteMessageAcquittement($statut) {
  	$acquittementsServeurActivitePmsi = $this->addElement($this, $this->acquittement, null, "http://www.hprim.org/hprimXML");

    $enteteMessageAcquittement = $this->addElement($acquittementsServeurActivitePmsi, "enteteMessageAcquittement");
    $this->addAttribute($enteteMessageAcquittement, "statut", $statut);
    $this->addAttribute($enteteMessageAcquittement, "version", CAppUI::conf("hprimxml $this->evenement version"));

    $this->addElement($enteteMessageAcquittement, "identifiantMessage", $this->identifiant);
    $this->addDateTimeElement($enteteMessageAcquittement, "dateHeureProduction");

    $emetteur = $this->addElement($enteteMessageAcquittement, "emetteur");
    $agents = $this->addElement($emetteur, "agents");
    $this->addAgent($agents, "application", "MediBoard", "Gestion des Etablissements de Santé");
    $group = CGroups::loadCurrent();
    $group->loadLastId400();
    $this->addAgent($agents, "système", $this->emetteur, $group->text);

    $destinataire = $this->addElement($enteteMessageAcquittement, "destinataire");
    $agents = $this->addElement($destinataire, "agents");
    $this->addAgent($agents, "application", $this->destinataire, $this->destinataire_libelle);

    $this->addElement($enteteMessageAcquittement, "identifiantMessageAcquitte", $this->identifiant);
  }
  
  function addReponses($statut, $codes, $commentaires = null, $mbObject = null) {
    $acquittementsServeurActivitePmsi = $this->documentElement;
        
    if ($mbObject instanceof CSejour) {
	    $mbPatient =& $mbObject->_ref_patient;
	    $mbSejour  =& $mbObject;
    }
  	if ($mbObject instanceof COperation) {
    	$mbPatient =& $mbObject->_ref_sejour->_ref_patient;
	    $mbSejour  =& $mbObject->_ref_sejour;
    }	
   	
    // Ajout du patient et de la venue
    $patient = $this->addElement($acquittementsServeurActivitePmsi, "patient");
	  $this->addPatient($patient, $mbPatient, false, true);
	  
	  $venue = $this->addElement($acquittementsServeurActivitePmsi, "venue");
		$this->addVenue($venue, $mbSejour, false, true);
		
    // Ajout des réponses
    $reponses = $this->addElement($acquittementsServeurActivitePmsi, "reponses");
    if (is_array($codes)) {
      foreach ($codes as $code) {
        $this->addReponse($reponses, $statut, $code, CAppUI::tr("hprimxml-error-$code"), $commentaires, $mbObject);
      }
    } else {
      $this->addReponse($reponses, $statut, $codes, CAppUI::tr("hprimxml-error-$codes"), $commentaires, $mbObject);
    }   
  }

  function generateAcquittementsServeurActivitePmsi($statut, $codes, $commentaires = null, $mbObject = null) {
    $this->emetteur = CAppUI::conf('mb_id');
    $this->date_production = mbDateTime();

    $this->generateEnteteMessageAcquittement($statut);
    $this->addReponses($statut, $codes, $commentaires, $mbObject);

    $this->saveTempFile();
    $messageAcquittementServeurActivitePmsi = utf8_encode($this->saveXML());

    return $messageAcquittementServeurActivitePmsi;
  }
   
  function getStatutAcquittementServeurActivitePmsi() {
    $xpath = new CMbXPath($this, true);
        
    return $xpath->queryAttributNode("/hprim:$this->evenement/hprim:enteteMessageAcquittement", null, "statut"); 
  }
  
  function getAcquittementsServeurActivitePmsi() {
    $xpath = new CMbXPath($this, true);
    
    $statut = $xpath->queryAttributNode("/hprim:$this->evenement/hprim:enteteMessageAcquittement", null, "statut"); 
    
    $query = "/hprim:$this->evenement/hprim:enteteMessageAcquittement";
    $enteteMessageAcquittement = $xpath->queryUniqueNode($query);  
    
    $data['identifiantMessage'] = $xpath->queryTextNode("hprim:identifiantMessage", $enteteMessageAcquittement);
    $agents = $xpath->queryUniqueNode("hprim:emetteur/hprim:agents", $enteteMessageAcquittement);
    $systeme = $xpath->queryUniqueNode("hprim:agent[@categorie='système']", $agents);
    $data['idClient'] = $xpath->queryTextNode("hprim:code", $systeme);
    $data['libelleClient'] = $xpath->queryTextNode("hprim:libelle", $systeme);
    
    $data['identifiantMessageAcquitte'] = $xpath->queryTextNode("hprim:identifiantMessageAcquitte", $enteteMessageAcquittement);
    
    return $data;
  }
  
  function getAcquittementReponsesServeurActivitePmsi() {
    $xpath = new CMbXPath($this, true);
    
    $statut = $xpath->queryAttributNode("/hprim:a$this->evenement/hprim:enteteMessageAcquittement", null, "statut"); 
    
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
<?php 

/**
 *  @package Mediboard
 *  @subpackage sip
 *  @version $Revision: $
 *  @author Yohann Poiron  
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


CAppUI::requireModuleClass("dPinterop", "mbxmldocument");
CAppUI::requireModuleClass("dPinterop", "hprimxmldocument");

if (!class_exists("CHPrimXMLDocument")) {
  return;
}

class CHPrimXMLEvenementsPatients extends CHPrimXMLDocument { 
  function __construct() {            
    parent::__construct("evenementPatient", "msgEvenementsPatients105", "sip");
  }
  
  function generateEnteteMessageEvenementsPatients() {
    global $AppUI, $g, $m;

    $evenementsPatients = $this->addElement($this, "evenementsPatients", null, "http://www.hprim.org/hprimXML");
    // Retourne un message d'acquittement par le récepteur
    $this->addAttribute($evenementsPatients, "acquittementAttendu", "oui");
    
    $enteteMessage = $this->addElement($evenementsPatients, "enteteMessage");
    $this->addElement($enteteMessage, "identifiantMessage", $this->_identifiant);
    $this->addDateTimeElement($enteteMessage, "dateHeureProduction", $this->_date_production);
    
    $emetteur = $this->addElement($enteteMessage, "emetteur");
    $agents = $this->addElement($emetteur, "agents");
    $this->addAgent($agents, "application", "MediBoard", "Gestion des Etablissements de Santé");
    $group = CGroups::loadCurrent();
    $group->loadLastId400();
    $this->addAgent($agents, "acteur", "user$AppUI->user_id", "$AppUI->user_first_name $AppUI->user_last_name");
    $this->addAgent($agents, "système", $this->_emetteur, $group->text);
    
    $destinataire = $this->addElement($enteteMessage, "destinataire");
    $agents = $this->addElement($destinataire, "agents");
    $this->destinataire = "MediBoard";
    $this->addAgent($agents, "application", $this->_destinataire, "Gestion des Etablissements de Santé");
  }
  
  function generateFromOperation($mbPatient, $referent) {  
    $evenementsPatients = $this->documentElement;
    $evenementPatient = $this->addElement($evenementsPatients, "evenementPatient");
    
    $enregistrementPatient = $this->addElement($evenementPatient, "enregistrementPatient");
    $actionConversion = array (
      "create" => "création",
      "store" => "modification",
      "delete" => "suppression"
    );
    //$this->addAttribute($enregistrementPatient, "action", $actionConversion[$mbPatient->_ref_last_log->type]);

    // Ajout du patient   
    //$this->addPatient($enregistrementPatient, $mbPatient, null, $referent);
        
    // Traitement final
    $this->purgeEmptyElements();
  }
  
  function generateEvenementsPatients($mbObject, $referent = null, $initiateur = null) {
    $echg_hprim = new CEchangeHprim();
    $this->_date_production = $echg_hprim->date_production = mbDateTime();
    $echg_hprim->emetteur = $this->_emetteur;
    $echg_hprim->destinataire = $this->_destinataire;
    $echg_hprim->type = "evenementsPatients";
    $echg_hprim->sous_type = "enregistrementPatient";
    $echg_hprim->message = utf8_encode($this->saveXML());
    if ($initiateur) {
      $echg_hprim->initiateur_id = $initiateur;
    }
    
    $echg_hprim->store();
    
    $this->_identifiant = str_pad($echg_hprim->_id, 6, '0', STR_PAD_LEFT);
            
    $this->generateEnteteMessageEvenementsPatients();
    $this->generateFromOperation($mbObject, $referent);
    
   // $doc_valid = $this->schemaValidate();
    $this->saveTempFile();
    $messageEvtPatient = utf8_encode($this->saveXML()); 
    
    $echg_hprim->message = $messageEvtPatient;
    $echg_hprim->store();
    
    return $messageEvtPatient;
  }
  
  function getEvenementPatientXML() {
    global $m;

    $xpath = new CMbXPath($this);
    $xpath->registerNamespace( "hprim", "http://www.hprim.org/hprimXML" );

    $data['xpath'] = $xpath;

    $data['acquittement'] = $xpath->queryAttributNode("/hprim:evenementsPatients", null, "acquittementAttendu");

    $query = "/hprim:evenementsPatients/hprim:enteteMessage";

    $entete = $xpath->queryUniqueNode($query);

    $data['identifiantMessage'] = $xpath->queryTextNode("hprim:identifiantMessage", $entete);
    $agents = $xpath->queryUniqueNode("hprim:emetteur/hprim:agents", $entete);
    $systeme = $xpath->queryUniqueNode("hprim:agent[@categorie='système']", $agents);
    $data['idClient'] = $xpath->queryTextNode("hprim:code", $systeme);
    $data['libelleClient'] = $xpath->queryTextNode("hprim:libelle", $systeme);

    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $enregistrementPatient = $xpath->queryUniqueNode("hprim:enregistrementPatient", $evenementPatient);

    $data['action'] = $xpath->getActionEvenement($evenementPatient);

    $data['patient'] = $xpath->queryUniqueNode("hprim:patient", $enregistrementPatient);
    $data['voletMedical'] = $xpath->queryUniqueNode("hprim:voletMedical", $enregistrementPatient);

    $data['idSource'] = $xpath->getIdSource($data['patient']);
    $data['idCible'] = $xpath->getIdCible($data['patient']);
    
    return $data;
  }
  
  function getIPPPatient() {
  	global $m;

    $xpath = new CMbXPath($this);
    $xpath->registerNamespace( "hprim", "http://www.hprim.org/hprimXML" );
    
    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $enregistrementPatient = $xpath->queryUniqueNode("hprim:enregistrementPatient", $evenementPatient);
    
    $patient = $xpath->queryUniqueNode("hprim:patient", $enregistrementPatient);

    return $xpath->getIdSource($patient);
  }
}
?>
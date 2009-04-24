<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("sip", "hprimxmlevenementspatients");

class CHPrimXMLFusionPatient extends CHPrimXMLEvenementsPatients { 
  function __construct() {            
    parent::__construct();
  }
  
  function generateFromOperation($mbPatient, $referent) {  
    $evenementsPatients = $this->documentElement;
    $evenementPatient = $this->addElement($evenementsPatients, "evenementPatient");
    
    $fusionPatient = $this->addElement($evenementPatient, "fusionPatient");
    $this->addAttribute($fusionPatient, "action", "fusion");
      
    $patient = $this->addElement($fusionPatient, "patient");
    mbTrace($mbPatient, "Patient", true);
    // Ajout du nouveau patient   
    $this->addPatient($patient, $mbPatient, null, $referent);
      
    $patientElimine = $this->addElement($fusionPatient, "patientElimine");
    $mbPatientElimine = new CPatient();
    $mbPatientElimine->load($mbPatient->_merging);
    mbTrace($mbPatientElimine, "Patient Elimine", true);
    // Ajout du patient a eliminer
    $this->addPatient($patientElimine, $mbPatientElimine, null, $referent);
        
    // Traitement final
    $this->purgeEmptyElements();
  }
  
  function generateTypeEvenement($mbObject, $referent = null, $initiateur = null) {
    $echg_hprim = new CEchangeHprim();
    $this->_date_production = $echg_hprim->date_production = mbDateTime();
    $echg_hprim->emetteur = $this->_emetteur;
    $echg_hprim->destinataire = $this->_destinataire;
    $echg_hprim->type = "evenementsPatients";
    $echg_hprim->sous_type = "fusionPatient";
    $echg_hprim->message = utf8_encode($this->saveXML());
    if ($initiateur) {
      $echg_hprim->initiateur_id = $initiateur;
    }
    
    $echg_hprim->store();
    
    $this->_identifiant = str_pad($echg_hprim->_id, 6, '0', STR_PAD_LEFT);
            
    $this->generateEnteteMessageEvenementsPatients();
    $this->generateFromOperation($mbObject, $referent);
    
    $doc_valid = $this->schemaValidate();
    $echg_hprim->message_valide = $doc_valid ? 1 : 0;

    $this->saveTempFile();
    $msgFusionPatient = utf8_encode($this->saveXML()); 
    
    $echg_hprim->message = $msgFusionPatient;
    
    $echg_hprim->store();
    
    return $msgFusionPatient;
  }
  
  function getFusionPatientXML() {
    global $m;

    $xpath = new CMbXPath($this);
    $xpath->registerNamespace( "hprim", "http://www.hprim.org/hprimXML" );

    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $fusionPatient = $xpath->queryUniqueNode("hprim:fusionPatient", $evenementPatient);

    $data['action'] = $this->getActionEvenement("hprim:fusionPatient", $evenementPatient);

    $data['patient'] = $xpath->queryUniqueNode("hprim:patient", $fusionPatient);
    $data['patientElimine'] = $xpath->queryUniqueNode("hprim:patientElimine", $fusionPatient);

    $data['idSource'] = $this->getIdSource($data['patient']);
    $data['idCible'] = $this->getIdCible($data['patient']);
    
    return $data;
  }
  
  /**
   * Fusion and recording a patient with an IPP in the system
   * @param CHPrimXMLAcquittementsPatients $domAcquittement
   * @param CEchangeHprim $echange_hprim
   * @param CPatient $newPatient
   * @param array $data
   * @return string acquittement 
   **/
  function fusionPatient($domAcquittement, $echange_hprim, $newPatient, $data) {
  	
  }
}
?>
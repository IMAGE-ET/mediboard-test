<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hprimxml", "evenementspatients");

class CHPrimXMLMouvementPatient extends CHPrimXMLEvenementsPatients { 
  function __construct() {    
    $this->sous_type = "fusionVenue";
            
    parent::__construct();
  }
  
  function generateFromOperation($mbVenue, $referent) {  
    $evenementsPatients = $this->documentElement;
    $evenementPatient = $this->addElement($evenementsPatients, "evenementPatient");
    
    $mouvementPatient = $this->addElement($evenementPatient, "mouvementPatient");
        
    // Traitement final
    $this->purgeEmptyElements();
  }
  
  function getMouvementPatientXML() {
    $xpath = new CMbXPath($this, true);

    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $mouvementPatient = $xpath->queryUniqueNode("hprim:mouvementPatient", $evenementPatient);

    $data['action']  = $this->getActionEvenement("hprim:mouvementPatient", $evenementPatient);
  
    $data['patient']  = $xpath->queryUniqueNode("hprim:patient", $mouvementPatient);
    $data['idSource'] = $this->getIdSource($data['patient']);
    $data['idCible']  = $this->getIdCible($data['patient']);
    
    $data['venue']         = $xpath->queryUniqueNode("hprim:venue", $mouvementPatient);
    $data['idSourceVenue'] = $this->getIdSource($data['venue']);
    $data['idCibleVenue']  = $this->getIdCible($data['venue']);
    
    $data['priseEnCharge'] = $xpath->queryUniqueNode("hprim:priseEnCharge", $mouvementPatient);
    $data['mouvements']    = $xpath->queryUniqueNode("hprim:mouvements", $mouvementPatient);
    $data['voletMedical']  = $xpath->queryUniqueNode("hprim:voletMedical", $mouvementPatient);
    $data['dossierResume'] = $xpath->queryUniqueNode("hprim:dossierResume", $mouvementPatient);
        
    return $data;
  }
  
  /**
   * Fusion and recording a stay with an num_dos in the system
   * @param CHPrimXMLAcquittementsPatients $domAcquittement
   * @param CEchangeHprim $echange_hprim.
   * @param CPatient $newPatient
   * @param array $data
   * @return string acquittement 
   **/
  function mouvementPatient($domAcquittement, $echange_hprim, $newPatient, $data) {
  	 if (($data['action'] != "création") && ($data['action'] != "modification")) {
      $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E08");
      $doc_valid = $domAcquittement->schemaValidate();
      $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        
      $echange_hprim->acquittement = $messageAcquittement;
      $echange_hprim->statut_acquittement = "erreur";
      $echange_hprim->store();
      
      return $messageAcquittement;
    }
    
    // Traitement du patient
    $domEnregistrementPatient = new CHPrimXMLEnregistrementPatient();
    $messageAcquittement = $domEnregistrementPatient->enregistrementPatient($domAcquittement, $echange_hprim, $newPatient, $data);
    if ($echange_hprim->statut_acquittement != "OK") {
      return $messageAcquittement;
    }
    
    $domAcquittement = new CHPrimXMLAcquittementsPatients();
    $domAcquittement->identifiant = $data['identifiantMessage'];
    $domAcquittement->destinataire = $data['idClient'];
    $domAcquittement->destinataire_libelle = $data['libelleClient'];
    
     // Si CIP
    if (!CAppUI::conf('sip server')) {
      $mbVenue = new CSejour();
      
      $etatVenue = CHPrimXMLEvenementsPatients::getEtatVenue($data['venue']);
       
      $id400Venue = new CIdSante400();
      //Paramétrage de l'id 400
      $id400Venue->object_class = "CSejour";
      $id400Venue->tag = ($etatVenue == "préadmission") ? CAppUI::conf('dPplanningOp CSejour tag_dossier_pa').$data['idClient'] : $data['idClient'];
      $id400Venue->id400 = $data['idSourceVenue'];
      $id400Venue->loadMatchingObject();
      if ($mbVenue->load($data['idCibleVenue'])) {
        if ($mbVenue->_id != $id400Venue->object_id) {
          $commentaire = "L'identifiant source fait référence au séjour : $id400Venue->object_id et l'identifiant cible au séjour : $mbVenue->_id.";
          $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E104", $commentaire);
          $doc_valid = $domAcquittement->schemaValidate();
          $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
    
          $echange_hprim->acquittement = $messageAcquittement;
          $echange_hprim->statut_acquittement = "erreur";
          $echange_hprim->store();
          return $messageAcquittement;
        }
      }
   		
      // Mapping de la venue 
      $mbVenue = $this->mappingVenue($data['venue'], $mbVenue);
      
      $mbVenue = $this->mappingMouvements($data['mouvements'], $mbVenue);
    }
  } 
}
?>
<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hprimxml", "evenementspatients");

class CHPrimXMLVenuePatient extends CHPrimXMLEvenementsPatients { 
  function __construct() {    
  	$this->sous_type = "venuePatient";
  	        
    parent::__construct();
  }
  
  function generateFromOperation($mbVenue, $referent) {  
    $evenementsPatients = $this->documentElement;
    $evenementPatient = $this->addElement($evenementsPatients, "evenementPatient");
    
    $venuePatient = $this->addElement($evenementPatient, "venuePatient");
    $actionConversion = array (
      "create" => "création",
      "store"  => "modification",
      "delete" => "suppression"
    );
    $this->addAttribute($venuePatient, "action", $actionConversion[$mbVenue->_ref_last_log->type]);
    
    $patient = $this->addElement($venuePatient, "patient");
    // Ajout du patient   
    $this->addPatient($patient, $mbVenue->_ref_patient, null, $referent);
    
    $venue = $this->addElement($venuePatient, "venue"); 
    // Ajout de la venue   
    $this->addVenue($venue, $mbVenue, null, $referent);
    
    // Ajout des attributs du séjour
    $this->addAttribute($venue, "confidentiel", "non");
    
    // Etat d'une venue : encours, clôturée ou préadmission
    if (!$mbVenue->entree_reelle && !$mbVenue->sortie_reelle) {
      $etat = "préadmission";
    }
    else if ($mbVenue->entree_reelle && !$mbVenue->sortie_reelle) {
      $etat = "encours";
    }
    else if ($mbVenue->entree_reelle && $mbVenue->sortie_reelle) {
      $etat = "clôturée";
    }
    $this->addAttribute($venue, "etat", $etat);
    
    $this->addAttribute($venue, "facturable", ($mbVenue->facturable)  ? "oui" : "non");
    $this->addAttribute($venue, "declarationMedecinTraitant", ($mbVenue->_adresse_par_prat)  ? "oui" : "non");
        
    // Traitement final
    $this->purgeEmptyElements();
  }

  function getVenuePatientXML() {
    $xpath = new CMbXPath($this, true);

    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $venuePatient= $xpath->queryUniqueNode("hprim:venuePatient", $evenementPatient);

    $data['action'] = $this->getActionEvenement("hprim:venuePatient", $evenementPatient);

    $data['patient'] = $xpath->queryUniqueNode("hprim:patient", $venuePatient);
    $data['venue'] = $xpath->queryUniqueNode("hprim:venue", $venuePatient);

    $data['idSource'] = $this->getIdSource($data['patient']);
    $data['idCible'] = $this->getIdCible($data['patient']);
    
    $data['idSourceVenue'] = $this->getIdSource($data['venue']);
    $data['idCibleVenue'] = $this->getIdCible($data['venue']);
    
    return $data;
  }
  
  function addVenue($elParent, $mbVenue, $referent = null) {
    $identifiant = $this->addElement($elParent, "identifiant");
    
    $this->addIdentifiantPart($identifiant, "emetteur",  $mbVenue->sejour_id, $referent);
    if ($mbVenue->_num_dossier != "-") {
      $this->addIdentifiantPart($identifiant, "recepteur",  $mbVenue->_num_dossier, $referent);
    }
    
    $natureVenueHprim = $this->addElement($elParent, "natureVenueHprim");
    $attrNatureVenueHprim = array (
      "CSejour" => "hsp",
      "CConsultation" => "cslt",
    );
    $this->addAttribute($natureVenueHprim, "valeur", (($mbVenue->_class_name == "CSejour") && ($mbVenue->type == "seances")) ? "sc" : $attrNatureVenueHprim[$mbVenue->_class_name]);
    
    $entree = $this->addElement($elParent, "entree");
    
    $dateHeureOptionnelle = $this->addElement($entree, "dateHeureOptionnelle");
    $this->addElement($dateHeureOptionnelle, "date", mbDate($mbVenue->_entree));
    $this->addElement($dateHeureOptionnelle, "heure", mbTime($mbVenue->_entree));
    
    $modeEntree = $this->addElement($entree, "modeEntree");
    // mode d'entrée inconnu
    $mode = "09";
    // admission après consultation d'un médecin de l'établissement
    if ($mbVenue->_ref_consult_anesth->_id) {
      $mode = "01";
    }
    // malade envoyé par un médecin extérieur
    if ($mbVenue->_ref_adresse_par_prat->_id) {
      $mode = "02";
    }
    $this->addAttribute($modeEntree, "valeur", $mode);
    
    $medecins = $this->addElement($elParent, "medecins");
    
    // Traitement du medecin traitant du patient
    $_ref_medecin_traitant = $mbVenue->_ref_patient->_ref_medecin_traitant;
    if ($_ref_medecin_traitant->_id) {
      if ($_ref_medecin_traitant->adeli) {
        $this->addMedecin($medecins, $_ref_medecin_traitant, "trt");
      }
    }
    
    // Traitement du medecin adressant
    $_ref_adresse_par_prat = $mbVenue->_ref_adresse_par_prat;
    if ($mbVenue->_adresse_par_prat) {
      if ($_ref_adresse_par_prat->adeli) {
        $this->addMedecin($medecins, $_ref_adresse_par_prat, "adrs");
      }
    }
    
    // Traitement du responsable du séjour
    $this->addMedecin($medecins, $mbVenue->_ref_praticien, "rsp");
    
    // Traitement des prescripteurs
    $_ref_prescripteurs = $mbVenue->_ref_prescripteurs;
    if (is_array($_ref_prescripteurs)) {
      foreach ($_ref_prescripteurs as $prescripteur) {
        $this->addMedecin($medecins, $prescripteur, "prsc");
      }
    }
    
    // Traitement des intervenant (ayant effectués des actes)
    $_ref_actes_ccam = $mbVenue->_ref_actes_ccam;
    if (is_array($_ref_actes_ccam)) {
      foreach ($_ref_actes_ccam as $acte_ccam) {
        $intervenant = $acte_ccam->_ref_praticien;
        $this->addMedecin($medecins, $intervenant, "intv");
      }
    }
    
    $sortie = $this->addElement($elParent, "sortie");
    $dateHeureOptionnelle = $this->addElement($sortie, "dateHeureOptionnelle");
    $this->addElement($dateHeureOptionnelle, "date", mbDate($mbVenue->_sortie));
    $this->addElement($dateHeureOptionnelle, "heure", mbTime($mbVenue->_sortie)); 
    
    if ($mbVenue->mode_sortie) {
      $typeModeSortieEtablissementHprim = $this->addElement($elParent, "typeModeSortieEtablissementHprim");
      //retour au domicile
      if ($mbVenue->mode_sortie == "normal") {
        $modeSortieEtablissementHprim = "04";
      } 
      // décès
      else if ($mbVenue->mode_sortie == "deces") {
        $modeSortieEtablissementHprim = "05";
      } 
      // autre transfert dans un autre CH
      else if ($mbVenue->mode_sortie == "transfert") {
        $modeSortieEtablissementHprim = "02";
        if ($mbVenue->etablissement_transfert_id) {
          $destination = $this->addElement($elParent, "destination");
          $this->addElement($destination, "libelle", $mbVenue->etablissement_transfert_id);
        }
      }
      $this->addAttribute($typeModeSortieEtablissementHprim, "valeur", $modeSortieEtablissementHprim);
    }
    
    $placement = $this->addElement($elParent, "Placement");
    $modePlacement = $this->addElement($placement, "modePlacement");
    $this->addAttribute($modePlacement, "modaliteHospitalisation", $mbVenue->modalite);
    $this->addElement($modePlacement, "libelle", substr($mbVenue->_view, 0, 80));   
    
    $datePlacement = $this->addElement($placement, "datePlacement");
    $this->addElement($datePlacement, "date", mbDate($mbVenue->_ref_first_log->date));
  }
  
  /**
   * Stay recording 
   * @param CHPrimXMLAcquittementsPatients $domAcquittement
   * @param CEchangeHprim $echange_hprim
   * @param CPatient $newPatient
   * @param CSejour $newSejour
   * @param array $data
   * @return CHPrimXMLAcquittementsPatients $messageAcquittement 
   **/
  function venuePatient($domAcquittement, $echange_hprim, $newPatient, $data) {    
    // Acquittement d'erreur : identifiants source et cible non fournis pour le patient / venue
    if (!$data['idSource'] && !$data['idCible'] && !$data['idSourceVenue'] && !$data['idCibleVenue']) {
      $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E05");
      $doc_valid = $domAcquittement->schemaValidate();
      $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        
      $echange_hprim->message = $messagePatient;
      $echange_hprim->acquittement = $messageAcquittement;
      $echange_hprim->statut_acquittement = "erreur";
      $echange_hprim->store();
      
      return $messageAcquittement;
    }
    
    // Traitement du patient
    $enregistrementPatient = new CHPrimXMLEnregistrementPatient();
    mbTrace($data, "data", true);
    $enregistrementPatient->enregistrementPatient($domAcquittement, $echange_hprim, $newPatient, $data);
    mbTrace($echange_hprim, "echange", true);
    
    // Traitement de la venue
    $newVenue = CSejour();
    
    // Si CIP
    if (!CAppUI::conf('sip server')) {
      
    }
    $newVenue = $this->mappingVenue($data['venue'], $newVenue);
    mbTrace($newVenue, "Traitement venue", true);
  }
}

?>
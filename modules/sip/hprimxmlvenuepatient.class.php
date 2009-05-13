<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("sip", "hprimxmlevenementspatients");

class CHPrimXMLVenuePatient extends CHPrimXMLEvenementsPatients { 
  function __construct() {            
    parent::__construct();
  }
  
  function generateFromOperation($mbSejour, $referent) {  
    $evenementsPatients = $this->documentElement;
    $evenementPatient = $this->addElement($evenementsPatients, "evenementPatient");
    
    $venuePatient = $this->addElement($evenementPatient, "venuePatient");
    $actionConversion = array (
      "create" => "création",
      "store"  => "modification",
      "delete" => "suppression"
    );
    $this->addAttribute($venuePatient, "action", $actionConversion[$mbSejour->_ref_last_log->type]);
    
    $patient = $this->addElement($venuePatient, "patient");
    // Ajout du patient   
    $this->addPatient($patient, $mbSejour->_ref_patient, null, $referent);
    
    $venue = $this->addElement($venuePatient, "venue"); 
    // Ajout de la venue   
    $this->addVenue($venue, $mbSejour, null, $referent);
    
    // Ajout des attributs du séjour
    $this->addAttribute($venue, "confidentiel", "non");
    
    // Etat d'une venue : encours, clôturée ou préadmission
    if (!$mbSejour->entree_reelle && !$mbSejour->sortie_reelle) {
    	$etat = "préadmission";
    }
    else if ($mbSejour->entree_reelle && !$mbSejour->sortie_reelle) {
      $etat = "encours";
    }
    else if ($mbSejour->entree_reelle && $mbSejour->sortie_reelle) {
      $etat = "clôturée";
    }
    $this->addAttribute($venue, "etat", $etat);
    
    $this->addAttribute($venue, "facturable", ($mbSejour->facturable)  ? "oui" : "non");
    $this->addAttribute($venue, "declarationMedecinTraitant", ($mbSejour->_adresse_par_prat)  ? "oui" : "non");
        
    // Traitement final
    $this->purgeEmptyElements();
  }
  
  function generateTypeEvenement($mbObject, $referent = null, $initiateur = null) {
    $echg_hprim = new CEchangeHprim();
    $this->_date_production = $echg_hprim->date_production = mbDateTime();
    $echg_hprim->emetteur = $this->_emetteur;
    $echg_hprim->destinataire = $this->_destinataire;
    $echg_hprim->type = "evenementsPatients";
    $echg_hprim->sous_type = "venuePatient";
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
    $msgVenuePatient = utf8_encode($this->saveXML()); 
    
    $echg_hprim->message = $msgVenuePatient;
    
    $echg_hprim->store();
    
    return $msgVenuePatient;
  }
  
  function getVenuePatientXML() {
    $xpath = new CMbXPath($this);
    $xpath->registerNamespace( "hprim", "http://www.hprim.org/hprimXML" );

    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $venuePatient= $xpath->queryUniqueNode("hprim:venuePatient", $evenementPatient);

    $data['action'] = $this->getActionEvenement("hprim:venuePatient", $evenementPatient);

    $data['patient'] = $xpath->queryUniqueNode("hprim:patient", $venuePatient);
    $data['venue'] = $xpath->queryUniqueNode("hprim:voletMedical", $venuePatient);

    $data['idSource'] = $this->getIdSource($data['patient']);
    $data['idCible'] = $this->getIdCible($data['patient']);
    
    return $data;
  }
  
  function addVenue($elParent, $mbVenue, $referent = null) {
    $identifiant = $this->addElement($elParent, "identifiant");
    
    $this->addIdentifiantPart($identifiant, "emetteur",  $mbVenue->sejour_id, $referent);
    $this->addIdentifiantPart($identifiant, "recepteur",  $mbVenue->_num_dossier, $referent);
    
    $natureVenueHprim = $this->addElement($elParent, "natureVenueHprim");
    $attrNatureVenueHprim = array (
      "CSejour" => "hsp",
      "CConsultation" => "cslt",
    );
    $this->addAttribute($natureVenueHprim, "valeur", $attrNatureVenueHprim[$mbVenue->_class_name]);
    
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
    if ($mbVenue->_ref_adresse_par_prat) {
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
   * @param array $data
   * @return string acquittement 
   **/
  function venuePatient($domAcquittement, $echange_hprim, $newPatient, $data) {
    $mutex = new CMbSemaphore("sip-ipp");

    // Si CIP
    if (!CAppUI::conf('sip server')) {
      // Acquittement d'erreur : identifiants source et cible non fournis
      if (!$data['idCible'] && !$data['idSource']) {
        $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E05");
        $doc_valid = $domAcquittement->schemaValidate();
        $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        
        $echange_hprim->acquittement = $messageAcquittement;
        $echange_hprim->statut_acquittement = "erreur";
        $echange_hprim->date_echange = mbDateTime();
        $echange_hprim->store();
      
        return $messageAcquittement;
      }
    }
  }
}

?>
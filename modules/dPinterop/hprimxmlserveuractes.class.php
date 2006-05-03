<?php /* $Id: hprimxmlserveuractes.class.php,v 1.3 2006/03/13 08:24:31 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision: 1.3 $
* @author Thomas Despoix
*/

if (!class_exists("DOMDocument")) {
  trigger_error("sorry, DOMDocument is needed");
  return;
}

global $AppUI, $m;

require_once($AppUI->getModuleClass("dPplanningOp", "planning"));
require_once($AppUI->getModuleClass("dPinterop", "hprimxmldocument"));

class CHPrimXMLServeurActes extends CHPrimXMLDocument {
  function __construct() {
    parent::__construct("serveurActes");
    global $AppUI;
        
    $evenementsServeurActes = $this->addElement($this, "evenementsServeurActes", null, "http://www.hprim.org/hprimXML");
    $this->addAttribute($evenementsServeurActes, "version", "1.01");

    $enteteMessage = $this->addElement($evenementsServeurActes, "enteteMessage");
    $this->addAttribute($enteteMessage, "modeTraitement", "test"); // A supprimer pour un utilisation réelle
    $this->addElement($enteteMessage, "identifiantMessage", "ES{$this->now}");
    $this->addDateTimeElement($enteteMessage, "dateHeureProduction");
    
    $emetteur = $this->addElement($enteteMessage, "emetteur");
    $agents = $this->addElement($emetteur, "agents");
    $this->addAgent($agents, "application", "MediBoard", "Gestion des Etablissements de Santé");
    $this->addAgent($agents, "système", "CMCA", "Centre Médico-Chir. de l'Atlantique");
    $this->addAgent($agents, "acteur", "user$AppUI->user_id", "$AppUI->user_first_name $AppUI->user_last_name");
    
    $destinataire = $this->addElement($enteteMessage, "destinataire");
    $agents = $this->addElement($destinataire, "agents");
    $this->addAgent($agents, "application", "SANTEcom", "Siemens Health Services: S@NTE.com");
    $this->addAgent($agents, "système", "CMCA", "Centre Médico-Chir. de l'Atlantique");
  }
  
  function generateFromOperation($mbOp) {
    $this->documentfinalprefix = "op" . sprintf("%06d", $mbOp->operation_id);    

    $evenementsServeurActes = $this->documentElement;

    $evenementServeurActe = $this->addElement($evenementsServeurActes, "evenementServeurActe");
    $this->addDateTimeElement($evenementServeurActe, "dateAction");

    // Ajout du patient
    $mbPatient =& $mbOp->_ref_pat;
    
    $patient = $this->addElement($evenementServeurActe, "patient");
    $identifiant = $this->addElement($patient, "identifiant");
    $this->addIdentifiantPart($identifiant, "emetteur", "pat$mbPatient->patient_id");
    $this->addIdentifiantPart($identifiant, "recepteur", $mbOp->_ref_pat->SHS);
    
    $personnePhysique = $this->addElement($patient, "personnePhysique");
    
    $sexeConversion = array (
      "m" => "M",
      "f" => "F",
      "j" => "F"
    );
    
    $this->addAttribute($personnePhysique, "sexe", $sexeConversion[$mbPatient->sexe]);
    $this->addElement($personnePhysique, "nomUsuel", substr($mbPatient->nom, 0, 35));
    $this->addElement($personnePhysique, "nomNaissance", substr($mbPatient->_nom_naissance, 0, 35));
    
    $prenoms = $this->addElement($personnePhysique, "prenoms");
    foreach ($mbPatient->_prenoms as $mbKey => $mbPrenom) {
      if ($mbKey < 4) {
        $this->addElement($prenoms, "prenom", substr($mbPrenom, 0, 35));
      }
    }
    
    $adresses = $this->addElement($personnePhysique, "adresses");
    $adresse = $this->addElement($adresses, "adresse");
    $this->addElement($adresse, "ligne", $mbPatient->adresse);
    $this->addElement($adresse, "ville", $mbPatient->ville);
    $this->addElement($adresse, "codePostal", $mbPatient->cp);
    
    $telephones = $this->addElement($personnePhysique, "telephones");
    $this->addElement($telephones, "telephone", $mbPatient->tel);
    $this->addElement($telephones, "telephone", $mbPatient->tel2);
    
    $dateNaissance = $this->addElement($personnePhysique, "dateNaissance");
    $this->addElement($dateNaissance, "date", $mbPatient->naissance);
    
    // Ajout de la venue: +/- l'hospitalisation
    $venue = $this->addElement($evenementServeurActe, "venue");
    
    $identifiant = $this->addElement($venue, "identifiant");
    $this->addIdentifiantPart($identifiant, "emetteur", "op$mbOp->operation_id");
    $this->addIdentifiantPart($identifiant, "recepteur", $mbOp->venue_SHS);
    
    $entree = $this->addElement($venue, "entree");
    $dateHeureOptionnelle = $this->addElement($entree, "dateHeureOptionnelle");
    $this->addElement($dateHeureOptionnelle, "date", $mbOp->date_adm);
    $this->addElement($dateHeureOptionnelle, "heure", $mbOp->time_adm);
    
    // Ajout du médecin prescripteur
    $mbChir =& $mbOp->_ref_chir;
    
    $medecins = $this->addElement($venue, "medecins");
    $medecin = $this->addElement($medecins, "medecin");
    $this->addElement($medecin, "numeroAdeli", $mbChir->adeli);
    $this->addAttribute($medecin, "lien", "exec");
    
    $identification = $this->addElement($medecin, "identification");
    $this->addElement($identification, "code", "chir$mbChir->user_id");
    $this->addElement($identification, "libelle", $mbChir->_user_username);
    
    $sortie = $this->addElement($venue, "sortie");
    $dateHeureOptionnelle = $this->addElement($sortie, "dateHeureOptionnelle");
    $this->addElement($dateHeureOptionnelle, "date", mbDate(null, $mbOp->_ref_last_affectation->sortie));
    $this->addElement($dateHeureOptionnelle, "heure", mbTime(null, $mbOp->_ref_last_affectation->sortie));
    
    $placement = $this->addElement($venue, "Placement");
    $modePlacement = $this->addElement($placement, "modePlacement");
    $this->addAttribute($modePlacement, "modaliteHospitalisation", $mbOp->_modalite_hospitalisation);
    $datePlacement = $this->addElement($placement, "datePlacement");
    $this->addElement($datePlacement, "date", $mbOp->date_adm);
    $this->addElement($datePlacement, "heure", $mbOp->time_adm);
    
    // Ajout de l'intervention
    $intervention = $this->addElement($evenementServeurActe, "intervention");
    $identifiant = $this->addElement($intervention, "identifiant");
    $emetteur = $this->addElement($identifiant, "emetteur", "op$mbOp->operation_id");
    
    $mbOpDebut = $mbOp->entree_bloc ? $mbOp->entree_bloc : $mbOp->time_operation;
    $debut = $this->addElement($intervention, "debut");
    $this->addElement($debut, "date", $mbOp->_ref_plageop->date);
    $this->addElement($debut, "heure", $mbOpDebut);
    
    $mbOpFin   = $mbOp->sortie_bloc ? $mbOp->sortie_bloc : mbAddTime($mbOp->temp_operation, $mbOp->time_operation);
    $fin = $this->addElement($intervention, "fin");
    $this->addElement($fin, "date", $mbOp->_ref_plageop->date);
    $this->addElement($fin, "heure", $mbOpFin);
    
    $mbChir->loadRefs();
    $this->addUniteFonctionnelle($intervention, $mbOp);
    
    // Ajout des participants
    $mbParticipants = array();
    foreach($mbOp->_ref_actes_ccam as $acte_ccam) {
      $mbParticipant = $acte_ccam->_ref_executant;
      $mbParticipants[$mbParticipant->user_id] = $mbParticipant;
    }
    
    $participants = $this->addElement($intervention, "participants");
    foreach ($mbParticipants as $mbParticipant) {
      $participant = $this->addElement($participants, "participant");
      $this->addProfessionnelSante($participant, $mbParticipant);
    }
        
    // Libellé de l'opération
    $this->addElement($intervention, "libelle", substr($mbOp->libelle, 0, 80));
    
    // Ajout des actes CCAM
    $actesCCAM = $this->addElement($evenementServeurActe, "actesCCAM");
    
    foreach ($mbOp->_ref_actes_ccam as $mbActe) {
      $this->addActeCCAM($actesCCAM, $mbActe, $mbOp);
    }
    
    // Traitement final
    $this->purgeEmptyElements();
  }
  
}

?>

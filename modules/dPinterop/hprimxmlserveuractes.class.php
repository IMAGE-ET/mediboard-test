<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $m, $g;

require_once($AppUI->getModuleClass("dPinterop", "mbxmldocument"));

if (!class_exists("CHPrimXMLDocument")) {
  return;
}

class CHPrimXMLServeurActes extends CHPrimXMLDocument {
  function __construct() {
    parent::__construct("serveurActes");
    global $AppUI, $g;
        
    $evenementsServeurActes = $this->addElement($this, "evenementsServeurActes", null, "http://www.hprim.org/hprimXML");
    $this->addAttribute($evenementsServeurActes, "version", "1.01");

    $enteteMessage = $this->addElement($evenementsServeurActes, "enteteMessage");
    $this->addAttribute($enteteMessage, "modeTraitement", "test"); // A supprimer pour un utilisation réelle
    $this->addElement($enteteMessage, "identifiantMessage", "ES{$this->now}");
    $this->addDateTimeElement($enteteMessage, "dateHeureProduction");
    
    $emetteur = $this->addElement($enteteMessage, "emetteur");
    $agents = $this->addElement($emetteur, "agents");
    $this->addAgent($agents, "application", "MediBoard", "Gestion des Etablissements de Santé");
    $group = new CGroups();
    $group->load($g);
    $this->addAgent($agents, "système", $group->text, $group->text);
    $this->addAgent($agents, "acteur", "user$AppUI->user_id", "$AppUI->user_first_name $AppUI->user_last_name");
    
    $destinataire = $this->addElement($enteteMessage, "destinataire");
    $agents = $this->addElement($destinataire, "agents");
    $this->addAgent($agents, "application", "SANTEcom", "Siemens Health Services: S@NTE.com");
    $this->addAgent($agents, "système", $group->text, $group->text);
  }
  
  function generateFromOperation($mbOp) {
    $this->documentfinalprefix = "op" . sprintf("%06d", $mbOp->operation_id);    

    $evenementsServeurActes = $this->documentElement;

    $evenementServeurActe = $this->addElement($evenementsServeurActes, "evenementServeurActe");
    $this->addDateTimeElement($evenementServeurActe, "dateAction");

    // Ajout du patient
    $mbPatient =& $mbOp->_ref_sejour->_ref_patient;
    
    $patient = $this->addElement($evenementServeurActe, "patient");
    $identifiant = $this->addElement($patient, "identifiant");
    $this->addIdentifiantPart($identifiant, "emetteur", "pat$mbPatient->patient_id");
    $this->addIdentifiantPart($identifiant, "recepteur", $mbPatient->SHS);
    
    $personnePhysique = $this->addElement($patient, "personnePhysique");
    
    $sexeConversion = array (
      "m" => "M",
      "f" => "F",
      "j" => "F"
    );
    
    $this->addAttribute($personnePhysique, "sexe", $sexeConversion[$mbPatient->sexe]);
    $this->addTexte($personnePhysique, "nomUsuel", $mbPatient->nom);
    $this->addTexte($personnePhysique, "nomNaissance", $mbPatient->_nom_naissance);
    
    $prenoms = $this->addElement($personnePhysique, "prenoms");
    foreach ($mbPatient->_prenoms as $mbKey => $mbPrenom) {
      if ($mbKey < 4) {
        $this->addTexte($prenoms, "prenom", $mbPrenom);
      }
    }
    
    $adresses = $this->addElement($personnePhysique, "adresses");
    $adresse = $this->addElement($adresses, "adresse");
    $this->addTexte($adresse, "ligne", $mbPatient->adresse);
    $this->addTexte($adresse, "ville", $mbPatient->ville);
    $this->addElement($adresse, "codePostal", $mbPatient->cp);
    
    $telephones = $this->addElement($personnePhysique, "telephones");
    $this->addElement($telephones, "telephone", $mbPatient->tel);
    $this->addElement($telephones, "telephone", $mbPatient->tel2);
    
    $dateNaissance = $this->addElement($personnePhysique, "dateNaissance");
    $this->addElement($dateNaissance, "date", $mbPatient->naissance);
    
    // Ajout de la venue, c'est-à-dire le séjour
    $mbSejour =& $mbOp->_ref_sejour;
    $venue = $this->addElement($evenementServeurActe, "venue");
    
    $identifiant = $this->addElement($venue, "identifiant");
    $this->addIdentifiantPart($identifiant, "emetteur", "sj$mbSejour->sejour_id");
    $this->addIdentifiantPart($identifiant, "recepteur", $mbSejour->venue_SHS);
    
    // Entrée de séjour
    $mbEntree = mbGetValue($mbSejour->entree_reelle, $mbSejour->entree_prevue);
    $entree = $this->addElement($venue, "entree");
    $dateHeureOptionnelle = $this->addElement($entree, "dateHeureOptionnelle");
    $this->addDateHeure($dateHeureOptionnelle, $mbEntree);
    
    // Ajout du médecin prescripteur
    $mbPraticien =& $mbSejour->_ref_praticien;
    
    $medecins = $this->addElement($venue, "medecins");
    $medecin = $this->addElement($medecins, "medecin");
    $this->addElement($medecin, "numeroAdeli", $mbPraticien->adeli);
    $this->addAttribute($medecin, "lien", "rsp");
    $this->addCodeLibelle($medecin, "identification", "prat$mbPraticien->user_id", $mbPraticien->_user_username);
    
    // Sortie de séjour
    $mbSortie =& mbGetValue($mbSejour->sortie_reelle, $mbSejour->sortie_prevue);
    $sortie = $this->addElement($venue, "sortie");
    $dateHeureOptionnelle = $this->addElement($sortie, "dateHeureOptionnelle");
    $this->addDateHeure($dateHeureOptionnelle, $mbSortie);
    
    $placement = $this->addElement($venue, "Placement");
    $modePlacement = $this->addElement($placement, "modePlacement");
    $this->addAttribute($modePlacement, "modaliteHospitalisation", $mbSejour->modalite);
    $datePlacement = $this->addElement($placement, "datePlacement");
    $this->addDateHeure($datePlacement, $mbEntree);
    
    // Ajout de l'intervention
    $intervention = $this->addElement($evenementServeurActe, "intervention");
    $identifiant = $this->addElement($intervention, "identifiant");
    $emetteur = $this->addElement($identifiant, "emetteur", "op$mbOp->operation_id");
    
    $mbOpDebut = mbGetValue(
      $mbOp->debut_op, 
      $mbOp->entree_salle, 
      $mbOp->time_operation
    );
    
    $debut = $this->addElement($intervention, "debut");
    $this->addElement($debut, "date", $mbOp->_ref_plageop->date);
    $this->addElement($debut, "heure", $mbOpDebut);
    
    $mbOpFin   = mbGetValue(
      $mbOp->fin_op, 
      $mbOp->sortie_salle, 
      mbAddTime($mbOp->temp_operation, $mbOp->time_operation)
    );
    
    $fin = $this->addElement($intervention, "fin");
    $this->addElement($fin, "date", $mbOp->_ref_plageop->date);
    $this->addElement($fin, "heure", $mbOpFin);
    
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
    $this->addTexte($intervention, "libelle", 80);
    
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

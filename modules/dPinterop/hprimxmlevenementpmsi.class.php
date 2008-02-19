<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $m, $g;

require_once($AppUI->getModuleClass("dPinterop", "mbxmldocument"));

if (!class_exists("CHPrimXMLDocument")) {
  return;
}

class CHPrimXMLEvenementPmsi extends CHPrimXMLDocument {
  function __construct() {
    parent::__construct("evenementPmsi");
    global $AppUI, $g;
        
    $evenementsPMSI = $this->addElement($this, "evenementsPMSI", null, "http://www.hprim.org/hprimXML");
    $this->addAttribute($evenementsPMSI, "version", "1.01");

    $enteteMessage = $this->addElement($evenementsPMSI, "enteteMessage");
    $this->addAttribute($enteteMessage, "modeTraitement", "test"); // A supprimer pour un utilisation réelle
    $this->addElement($enteteMessage, "identifiantMessage", "EP{$this->now}");
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
  
  function setFinalPrefix($mbSej) {
    $this->documentfinalprefix = "sej" . sprintf("%06d", $mbSej->_id); 
  }
  
  function generateFromSejour($mbSej) {
    $this->setFinalPrefix($mbSej);

    $evenementsPMSI = $this->documentElement;

    $evenementPMSI = $this->addElement($evenementsPMSI, "evenementPMSI");

    // Ajout du patient
    $mbPatient =& $mbSej->_ref_patient;
    
    $patient = $this->addElement($evenementPMSI, "patient");
    $identifiant = $this->addElement($patient, "identifiant");
    $this->addIdentifiantPart($identifiant, "emetteur", "pat$mbPatient->_id");
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
    $venue = $this->addElement($evenementPMSI, "venue");
    
    $identifiant = $this->addElement($venue, "identifiant");
    $this->addIdentifiantPart($identifiant, "emetteur", "sj$mbSej->_id");
    $this->addIdentifiantPart($identifiant, "recepteur", $mbSej->venue_SHS);
    
    // Entrée de séjour
    $mbEntree = mbGetValue($mbSej->entree_reelle, $mbSej->entree_prevue);
    $entree = $this->addElement($venue, "entree");
    $dateHeureOptionnelle = $this->addElement($entree, "dateHeureOptionnelle");
    $this->addDateHeure($dateHeureOptionnelle, $mbEntree);
    
    // Ajout du médecin prescripteur
    $mbPraticien =& $mbSej->_ref_praticien;
    
    $medecins = $this->addElement($venue, "medecins");
    $medecin = $this->addElement($medecins, "medecin");
    $this->addElement($medecin, "numeroAdeli", $mbPraticien->adeli);
    $this->addAttribute($medecin, "lien", "rsp");
    $this->addCodeLibelle($medecin, "identification", "prat$mbPraticien->user_id", $mbPraticien->_user_username);
    
    // Sortie de séjour
    $mbSortie = mbGetValue($mbSej->sortie_reelle, $mbSej->sortie_prevue);
    $sortie = $this->addElement($venue, "sortie");
    $dateHeureOptionnelle = $this->addElement($sortie, "dateHeureOptionnelle");
    $this->addDateHeure($dateHeureOptionnelle, $mbSortie);
    
    $placement = $this->addElement($venue, "Placement");
    $modePlacement = $this->addElement($placement, "modePlacement");
    $this->addAttribute($modePlacement, "modaliteHospitalisation", $mbSej->modalite);
    $datePlacement = $this->addElement($placement, "datePlacement");
    $this->addDateHeure($datePlacement, $mbEntree);
    
    // Ajout de la saisie délocalisée
    $saisie = $this->addElement($evenementPMSI, "saisieDelocalisee");
    $this->addAttribute($saisie, "action", "création");
    $this->addDateTimeElement($saisie, "dateAction");
    $dateHeureOptionnelle = $this->addElement($saisie, "dateHeureReference");
    $this->addDateHeure($dateHeureOptionnelle);
    // Identifiant (on utilise le séjour)
    $identifiant = $this->addElement($saisie, "identifiant");
    $this->addElement($identifiant, "emetteur", "diag$mbSej->_id");
    $this->addElement($identifiant, "recepteur", $mbSej->venue_SHS);
    // Unité médicale : vide pour l'instant car présent dans l'opération :
    // à passer dans le séjour (code_uf, libell_uf dans this->addUniteFonctionnelle())
    $uniteMedicale = $this->addElement($saisie, "uniteMedicale");
    $codeUniteMedicale = $this->addElement($uniteMedicale, "code");
    // Médecin responsable
    $medecinResponsable = $this->addElement($saisie, "medecinResponsable");
    $this->addElement($medecinResponsable, "numeroAdeli", $mbPraticien->adeli);
    $this->addAttribute($medecinResponsable, "lien", "rsp");
    $this->addCodeLibelle($medecinResponsable, "identification", "prat$mbPraticien->user_id", $mbPraticien->_user_username);
    // Diagnostics RUM
    $diagnosticsRum = $this->addElement($saisie, "diagnosticsRum");
    $diagnosticPrincipal = $this->addElement($diagnosticsRum, "diagnosticPrincipal");
    $this->addElement($diagnosticPrincipal, "codeCim10", $mbSej->DP);
    if($mbSej->DR) {
      $diagnosticRelie = $this->addElement($diagnosticsRum, "diagnosticRelie");
      $this->addElement($diagnosticRelie, "codeCim10", $mbSej->DR);
    }
    if(count($mbSej->_ref_dossier_medical->_codes_cim)) {
      $diagnosticsSignificatifs = $this->addElement($diagnosticsRum, "diagnosticsSignificatifs");
      foreach($mbSej->_ref_dossier_medical->_codes_cim as $curr_code) {
        $diagnosticSignificatif = $this->addElement($diagnosticsSignificatifs, "diagnosticSignificatif");
        $this->addElement($diagnosticSignificatif, "codeCim10", $curr_code);
      }
    }
    // Ajout de l'IGS2 : à faire
    $igs2 = $this->addElement($saisie, "igs2");
    
    // Traitement final
    $this->purgeEmptyElements();
  }
  
}

?>

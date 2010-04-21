<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hprimxml", "evenementsserveuractivitepmsi");

class CHPrimXMLEvenementPmsi extends CHPrimXMLEvenementsServeurActivitePmsi {
  function __construct() {
    $this->sous_type = "evenementPMSI";
    $this->evenement = "evt_pmsi";
		
    parent::__construct("evenementPmsi", "msgEvenementsPmsi");
  }
  
	function generateEnteteMessage() {
		$evenementsPMSI = $this->addElement($this, "evenementsPMSI", null, "http://www.hprim.org/hprimXML");
    $this->addAttribute($evenementsPMSI, "version", CAppUI::conf('hprimxml evt_serveuractes version'));
    
    $this->addEnteteMessage($evenementsPMSI);
  }

  function generateFromOperation($mbSej) {
    $evenementsPMSI = $this->documentElement;

    $evenementPMSI = $this->addElement($evenementsPMSI, "evenementPMSI");

    // Ajout du patient
    $mbPatient =& $mbSej->_ref_patient;
    $patient = $this->addElement($evenementPMSI, "patient");
    $this->addPatient($patient, $mbPatient, false, true);
    
    // Ajout de la venue, c'est-à-dire le séjour
    $venue = $this->addElement($evenementPMSI, "venue");
    $this->addVenue($venue, $mbSej);
    
    // Ajout de la saisie délocalisée
    $saisie = $this->addElement($evenementPMSI, "saisieDelocalisee");
    $this->addAttribute($saisie, "action", "création");
    $this->addDateTimeElement($saisie, "dateAction");
    $dateHeureOptionnelle = $this->addElement($saisie, "dateHeureReference");
    $this->addDateHeure($dateHeureOptionnelle);
    // Identifiant (on utilise le séjour)
    $identifiant = $this->addElement($saisie, "identifiant");
    $this->addElement($identifiant, "emetteur", "diag$mbSej->_id");
    $this->addElement($identifiant, "recepteur", $mbSej->_num_dossier);
    // Unité médicale : 
    // à passer dans de l'opération vers le séjour (code_uf, libelle_uf dans this->addUniteFonctionnelle())
    //$uniteMedicale = $this->addElement($saisie, "uniteMedicale");
    //$codeUniteMedicale = $this->addElement($uniteMedicale, "code");
    $mbOp = reset($mbSej->_ref_operations);
    $this->addUniteFonctionnelleResponsable($saisie, $mbOp);
    
    // Médecin responsable
    $medecinResponsable = $this->addElement($saisie, "medecinResponsable");
    $mbPraticien =& $mbSej->_ref_praticien;
    $this->addElement($medecinResponsable, "numeroAdeli", $mbPraticien->adeli);
    $this->addAttribute($medecinResponsable, "lien", "rsp");
    $this->addCodeLibelle($medecinResponsable, "identification", "prat$mbPraticien->user_id", $mbPraticien->_user_username);
		
    // Diagnostics RUM
    $diagnosticsRum = $this->addElement($saisie, "diagnosticsRum");
    $diagnosticPrincipal = $this->addElement($diagnosticsRum, "diagnosticPrincipal");
    $this->addElement($diagnosticPrincipal, "codeCim10", strtoupper($mbSej->DP));
    if($mbSej->DR) {
      $diagnosticRelie = $this->addElement($diagnosticsRum, "diagnosticRelie");
      $this->addElement($diagnosticRelie, "codeCim10", strtoupper($mbSej->DR));
    }
    if(count($mbSej->_ref_dossier_medical->_codes_cim)) {
      $diagnosticsSignificatifs = $this->addElement($diagnosticsRum, "diagnosticsSignificatifs");
      foreach($mbSej->_ref_dossier_medical->_codes_cim as $curr_code) {
        $diagnosticSignificatif = $this->addElement($diagnosticsSignificatifs, "diagnosticSignificatif");
        $this->addElement($diagnosticSignificatif, "codeCim10", strtoupper($curr_code));
      }
    }
    // Ajout de l'IGS2 : à faire
    $igs2 = $this->addElement($saisie, "igs2");

    // Traitement final
    $this->purgeEmptyElements();
  }
   
}
?>
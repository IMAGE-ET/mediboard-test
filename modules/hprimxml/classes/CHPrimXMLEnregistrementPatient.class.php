<?php

/**
 * Évènements enregistrement patient
 *
 * @category Hprimxml
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id: CHPrimXMLVenuePatient.class.php 20171 2013-08-14 16:02:07Z lryo $
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPrimXMLEnregistrementPatient
 */
class CHPrimXMLEnregistrementPatient extends CHPrimXMLEvenementsPatients { 
  public $actions = array(
    'création'     => "création",
    'remplacement' => "remplacement",
    'modification' => "modification",
  );

  /**
   * @see parent::__construct()
   */
  function __construct() {   
    $this->sous_type = "enregistrementPatient";
             
    parent::__construct();
  }

  /**
   * @see parent::generateFromOperation()
   */
  function generateFromOperation($mbPatient, $referent) {  
    $evenementsPatients = $this->documentElement;
    $evenementPatient   = $this->addElement($evenementsPatients, "evenementPatient");

    $enregistrementPatient = $this->addElement($evenementPatient, "enregistrementPatient");
    $actionConversion = array (
      "create" => "création",
      "store"  => "modification",
      "delete" => "suppression"
    );
    $action = (!$mbPatient->_ref_last_log) ? "modification" : $actionConversion[$mbPatient->_ref_last_log->type];

    $this->addAttribute($enregistrementPatient, "action", $action);
    
    $patient = $this->addElement($enregistrementPatient, "patient");
    // Ajout du patient   
    $this->addPatient($patient, $mbPatient, $referent);
        
    // Traitement final
    $this->purgeEmptyElements();
  }

  /**
   * @see parent::getContentsXML()
   */
  function getContentsXML() {
    $xpath = new CHPrimXPath($this);

    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $enregistrementPatient = $xpath->queryUniqueNode("hprim:enregistrementPatient", $evenementPatient);

    $data['action'] = $this->getActionEvenement("hprim:enregistrementPatient", $evenementPatient);

    $data['patient'] = $xpath->queryUniqueNode("hprim:patient", $enregistrementPatient);

    $data['idSourcePatient'] = $this->getIdSource($data['patient']);
    $data['idCiblePatient']  = $this->getIdCible($data['patient']);
    
    return $data;
  }

  /**
   * Check datas
   *
   * @param CHPrimXMLAcquittementsPatients $dom_acq    Acquittement
   * @param CPatient                       $newPatient Patient
   * @param array                          $data       Datas
   *
   * @return string
   */
  function check($dom_acq, $newPatient, $data) {
    $idSourcePatient = $data['idSourcePatient'];
    $idCiblePatient  = $data['idCiblePatient'];

    // Acquittement d'erreur : identifiants source et cible non fournis
    if (!$idCiblePatient && !$idSourcePatient) {
      return $this->_ref_echange_hprim->setAckError($dom_acq, "E005", null, $newPatient);
    }
  }
  
  /**
   * Recording a patient with an IPP in the system
   *
   * @param CHPrimXMLAcquittementsPatients $dom_acq     Acquittement
   * @param CPatient                       &$newPatient Patient
   * @param array                          $data        Datas
   *
   * @return CHPrimXMLAcquittementsPatients $msgAcq 
   **/
  function enregistrementPatient($dom_acq, &$newPatient, $data) {
    // Traitement du message des erreurs
    $codes = array();
    $commentaire = $avertissement = $msgID400 = $msgIPP = "";
    $_modif_patient = false;

    $echg_hprim = $this->_ref_echange_hprim;
    $sender = $echg_hprim->_ref_sender;
    $sender->loadConfigValues();
    $this->_ref_sender = $sender;

    if ($msg = $this->check($dom_acq, $newPatient, $data)) {
      return $msg;
    }

    $idSourcePatient = $data['idSourcePatient'];
    $idCiblePatient  = $data['idCiblePatient'];
    
    // Si CIP
    if (!CAppUI::conf('sip server')) {
      $IPP = CIdSante400::getMatch("CPatient", $sender->_tag_patient, $idSourcePatient);

      // idSource non connu
      if (!$IPP->_id) {
        // idCible fourni
        if ($idCiblePatient) {
          if ($newPatient->load($idCiblePatient)) {
            // Le patient trouvé est-il différent ?
            if ($commentaire = $this->checkSimilarPatient($newPatient, $data['patient'])) {
              return $echg_hprim->setAckError($dom_acq, "E016", $commentaire, $newPatient);
            }
        
            // Mapping du patient
            $newPatient = $this->mappingPatient($data['patient'], $newPatient);
          
            // On store le patient
            $msgPatient  = CEAIPatient::storePatient($newPatient, $sender);
            $commentaire = CEAIPatient::getComment($newPatient);
            
            $_code_IPP      = "I021";
            $_modif_patient = true; 
          }
          else {
            $_code_IPP = "I020";
          }
        }
        else {
          $_code_IPP = "I022";  
        }
        // Mapping du patient
        $newPatient = $this->mappingPatient($data['patient'], $newPatient);
              
        if (!$newPatient->_id) {
          // Patient retrouvé
          if ($newPatient->loadMatchingPatient()) {
            // Mapping du patient
            $newPatient = $this->mappingPatient($data['patient'], $newPatient);
            
            // On store le patient
            $msgPatient  = CEAIPatient::storePatient($newPatient, $sender);
            $commentaire = CEAIPatient::getComment($newPatient);

            $_code_IPP      = "A021";
            $_modif_patient = true;
          }
          else {
            // On store le patient
            $msgPatient  = CEAIPatient::storePatient($newPatient, $sender);
            $commentaire = CEAIPatient::getComment($newPatient);

          }
        }

        $msgIPP = CEAIPatient::storeIPP($IPP, $newPatient, $sender);
        
        $codes = array ($msgPatient ? ($_modif_patient ? "A003" : "A002") :
            ($_modif_patient ? "I002" : "I001"), $msgIPP ? "A005" : $_code_IPP);
        
        if ($msgPatient || $msgIPP) {
          $avertissement = $msgPatient." ".$msgIPP;
        }
        else {
          $commentaire .= "IPP créé : $IPP->id400.";
        }
      } 
      // idSource connu
      else {
        $newPatient->load($IPP->object_id);
        if ($commentaire = $this->checkSimilarPatient($newPatient, $data['patient'])) {
          return $echg_hprim->setAckError($dom_acq, "E016", $commentaire, $newPatient);
        }
            
        // Mapping du patient
        $newPatient = $this->mappingPatient($data['patient'], $newPatient);
                        
        // idCible non fourni
        if (!$idCiblePatient) {
          $_code_IPP = "I023"; 
        }
        else {
          $tmpPatient = new CPatient();
          // idCible connu
          if ($tmpPatient->load($idCiblePatient)) {
            if ($tmpPatient->_id != $IPP->object_id) {
              $commentaire  = "L'identifiant source fait référence au patient : $IPP->object_id ";
              $commentaire .= "et l'identifiant cible au patient : $tmpPatient->_id.";

              return $echg_hprim->setAckError($dom_acq, "E004", $commentaire, $newPatient);
            }
            $_code_IPP = "I024"; 
          }
          // idCible non connu
          else {
            $_code_IPP = "A020";
          }
        }
        
        // On store le patient
        $msgPatient = CEAIPatient::storePatient($newPatient, $sender);
        $commentaire = CEAIPatient::getComment($newPatient);

        $codes = array ($msgPatient ? "A003" : "I002", $_code_IPP);
        
        if ($msgPatient) {
          $avertissement = $msgPatient." ";
        }
      }
    }

    return $echg_hprim->setAck($dom_acq, $codes, $avertissement, $commentaire, $newPatient);
  }
}
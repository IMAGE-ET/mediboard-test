<?php

/**
 * Represents an HL7 ROL message segment (Role) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentROL 
 * ROL - Represents an HL7 ROL message segment (Role)
 */

class CHL7v2SegmentROL extends CHL7v2Segment {
  var $medecin = null;
  var $action  = "UC";
  var $role_id = null;
  
  function build(CHL7v2Event $event) {
    parent::build($event, "ROL");
        
    $medecin  = $this->medecin;

    $data = array();
        
    // ROL-1: Role Instance ID (EI) (optional)
    // The field is optional when used in ADT and Finance messages
    $data[] = null;
    
    // ROL-2: Action Code (ID)
    // Table - 0287
    // AD - ADD - Nouveau rôle du médecin
    // DE - DELETE - Suppression du rôle du médecin
    // UC - UNCHANGED - Notification du médecin à prendre en compte pour le rôle défini dans le contexte courant
    // UP - UPDATE - Mise à jour du rôle du médecin
    $data[] = $this->action;
     
    // ROL-3: Role-ROL (CE)
    // Table - 0443
    // AD   - Admitting - PV1.17 Médecin de la structure qui décide d'hospitaliser
    // AT   - Attending - PV1-7 Médecin responsable du patient pendant le séjour
    // CP   - Consulting Provider - Médecin consulté pour 2ème avis dans le cadre de la venue
    // FHCP - Family Health Care Professional - Médecin de famille. Utilisé dans les rares cas où il est distinct du médecin traitant
    // RP   - Referring Provider - PV1-8 Médecin adressant 
    // RT   - Referred to Provider - Médecin correspondant
    // ODRP - Officialy Declared Referring Physician - Médecin Traitant
    // SUBS - Substitute - Remplaçant du médecin traitant
    $data[] = array( 
      array (
        $this->role_id,
        null,
        null,
        null,
        null,
        null
      )
    );
     
    // ROL-4: Role Person (XCN) (repeating)
    $data[] = array(
      array (
        // XCN-1
        CValue::first($medecin->rpps, $medecin->adeli, $medecin->_id),
        // XCN-2
        $medecin->nom,
        // XCN-3
        $medecin->prenom,
        // XCN-4
        null,
        // XCN-5
        null,
        // XCN-6
        null,
        // XCN-7
        null,
        // XCN-8
        null,
        // XCN-9
        // Autorité d'affectation
        $this->getAssigningAuthority($medecin->rpps ? "RPPS" : ($medecin->adeli ? "ADELI" : "mediboard")),
        // XCN-10
        // Table - 0200
        // L - Legal Name - Nom de famille
        "L",
        // XCN-11
        null,
        // XCN-12
        null,
        // XCN-13
        // Table - 0203
        // ADELI - Numéro au répertoire ADELI du professionnel de santé
        // RPPS  - N° d'inscription au RPPS du professionnel de santé 
        // RI    - Ressource interne
        $medecin->rpps ? "RPPS" : ($medecin->adeli ? "ADELI" : "RI"),
        // XCN-14
        null,
        // XCN-15
        null,
        // XCN-16
        null,
        // XCN-17
        null,
        // XCN-18
        null,
        // XCN-19
        null,
        // XCN-20
        null,
        // XCN-21
        null,
        // XCN-22
        null,
        // XCN-23
        null,
      )
    );
    
    // ROL-5: Role Begin Date/Time (TS) (optional)
    $data[] = null;
    
    // ROL-6: Role End Date/Time (TS) (optional)
    $data[] = null;
    
    // ROL-7: Role Duration (CE) (optional)
    $data[] = null;
    
    // ROL-8: Role Action Reason (CE) (optional)
    $data[] = null;
    
    // ROL-9: Provider Type (CE) (optional repeating)
    $data[] = null;
    
    // ROL-10: Organization Unit Type (CE) (optional)
    $data[] = null;
    
    // ROL-11: Office/Home Address/Birthplace (XAD) (optional repeating)
    $data[] = null;
    
    // ROL-12: Phone (XTN) (optional repeating)
    $data[] = null;
    
    $this->fill($data);
  }
}

?>
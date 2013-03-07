<?php

/**
 * Represents an HL7 PID message segment (Patient Identification) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentPID 
 * PID - Represents an HL7 PID message segment (Patient Identification)
 */

class CHL7v2SegmentPID extends CHL7v2Segment {
  /**
   * @var string
   */
  public $name    = "PID";
  /**
   * @var null
   */
  public $set_id;
  
  /**
   * @var CPatient
   */
  public $patient;
  
  /**
   * @var CSejour
   */
  public $sejour;

  /**
   * Build PID segement
   *
   * @param CHL7v2Event $event Event
   *
   * @return null
   */
  function build(CHL7v2Event $event) {
    parent::build($event);
    
    $message  = $event->message;
    $receiver = $event->_receiver;
    $group    = $receiver->loadRefGroup();

    $patient  = $this->patient;
    
    $data = array();
    // PID-1: Set ID - PID (SI) (optional)
    $data[] = $this->set_id;
    
    // PID-2: Patient ID (CX) (optional)
    $data[] = null;
    
    // PID-3: Patient Identifier List (CX) (repeating)
    $data[] = $this->getPersonIdentifiers($patient, $group, $receiver);
    
    // PID-4: Alternate Patient ID - PID (CX) (optional repeating)
    $data[] = null;
    
    // PID-5: Patient Name (XPN) (repeating)
    $data[] = $this->getXPN($patient);
    
    // PID-6: Mother's Maiden Name (XPN) (optional repeating)
    $data[] = null;
    
    // PID-7: Date/Time of Birth (TS) (optional)
    if ($patient->naissance) {
      $data[] = CMbDT::isLunarDate($patient->naissance) ? null : $patient->naissance;
    }
    else {
      $data[] = null;
    }
    
    // PID-8: Administrative Sex (IS) (optional)
    // Table - 0001
    // F - Female
    // M - Male
    // O - Other
    // U - Unknown
    // A - Ambiguous  
    // N - Not applicable
    $data[] = CHL7v2TableEntry::mapTo("1", $patient->sexe);
    
    // PID-9: Patient Alias (XPN) (optional repeating)
    $data[] = null;
    
    // PID-10: Race (CE) (optional repeating)
    $data[] = null;
    
    // PID-11: Patient Address (XAD) (optional repeating)
    $address = array();
    
    if ($patient->adresse || $patient->ville || $patient->cp) {
      $linesAdress = explode("\n", $patient->adresse, 2);
      $address[] = array(
        CValue::read($linesAdress, 0),
        str_replace("\n", $message->componentSeparator, CValue::read($linesAdress, 1)),
        $patient->ville,
        null,
        $patient->cp,
        // Pays INSEE, récupération de l'alpha 3
        CPaysInsee::getAlpha3($patient->pays_insee),
        // Table - 0190
        // B   - Firm/Business 
        // BA  - Bad address 
        // BDL - Birth delivery location (address where birth occurred)  
        // BR  - Residence at birth (home address at time of birth)  
        // C   - Current Or Temporary
        // F   - Country Of Origin 
        // H   - Home 
        // L   - Legal Address 
        // M   - Mailing
        // N   - Birth (nee) (birth address, not otherwise specified)  
        // O   - Office
        // P   - Permanent 
        // RH  - Registry home
        "H",
      );
    }
    
    if ($patient->lieu_naissance || $patient->cp_naissance || $patient->pays_naissance_insee) {
      $address[] = array(
        null,
        null,
        $patient->lieu_naissance,
        null,
        $patient->cp_naissance,
        // Pays INSEE, récupération de l'alpha 3
        CPaysInsee::getAlpha3($patient->pays_naissance_insee),
        // Table - 0190
        // B   - Firm/Business 
        // BA  - Bad address 
        // BDL - Birth delivery location (address where birth occurred)  
        // BR  - Residence at birth (home address at time of birth)  
        // C   - Current Or Temporary
        // F   - Country Of Origin 
        // H   - Home 
        // L   - Legal Address 
        // M   - Mailing
        // N   - Birth (nee) (birth address, not otherwise specified)  
        // O   - Office
        // P   - Permanent 
        // RH  - Registry home
        "BDL",
      );
    }
    $data[] = $address;
    
    // PID-12: County Code (IS) (optional)
    $data[] = null;
    
    // PID-13: Phone Number - Home (XTN) (optional repeating)
    // Table - 0201
    // ASN - Answering Service Number
    // BPN - Beeper Number 
    // EMR - Emergency Number  
    // NET - Network (email) Address
    // ORN - Other Residence Number 
    // PRN - Primary Residence Number 
    // VHN - Vacation Home Number  
    // WPN - Work Number
    
    // Table - 0202
    // BP       - Beeper  
    // CP       - Cellular Phone  
    // FX       - Fax 
    // Internet - Internet Address: Use Only If Telecommunication Use Code Is NET 
    // MD       - Modem 
    // PH       - Telephone  
    // TDD      - Telecommunications Device for the Deaf  
    // TTY      - Teletypewriter
    $phones = array();
    if ($patient->tel) {
      $phones[] = $this->getXTN($receiver, $patient->tel, "PRN", "PH");
    }
    if ($patient->tel2) {
      // Pour le portable on met soit PRN ou ORN
      $phones[] = $this->getXTN($receiver, $patient->tel2, $receiver->_configs["build_cellular_phone"], "CP");
    }
    if ($patient->tel_autre) {
      $phones[] = $this->getXTN($receiver, $patient->tel_autre, "ORN", "PH");
    }
    if ($patient->email) {
      $phones[] = array(
        null,
        // Table - 0201
        "NET",
        // Table - 0202
        "Internet",
        $patient->email,
      );
    }
    $data[] =  $phones;
    
    // PID-14: Phone Number - Business (XTN) (optional repeating)
    $data[] = null;
    
    // PID-15: Primary Language (CE) (optional)
    $data[] = null;
    
    // PID-16: Marital Status (CE) (optional)
    $data[] = null;
    
    // PID-17: Religion (CE) (optional)
    $data[] = null;
    
    // PID-18: Patient Account Number (CX) (optional)
    if ($this->sejour && ($receiver->_configs["build_NDA"] == "PID_18")) {
      // Même traitement que pour l'IPP
      switch ($receiver->_configs["build_PID_34"]) {
        case 'actor':
          $assigning_authority = $this->getAssigningAuthority("actor", null, $receiver);
          break;
        
        default:
          $assigning_authority = $this->getAssigningAuthority("FINESS", $group->finess);
          break;
      } 
      
      $sejour = $this->sejour;
      $sejour->loadNDA($group->_id);
      $data[] = $sejour->_NDA ? array( 
                  array(
                    $sejour->_NDA,
                    null,
                    null,
                    // PID-3-4 Autorité d'affectation
                    $assigning_authority,
                    "AN"
                  )
                ) : null;
    }
    else {
      $data[] = null;
    }
    
    // PID-19: SSN Number - Patient (ST) (optional)
    $data[] = $patient->matricule;

    // PID-20: Driver's License Number - Patient (DLN) (optional)
    $data[] = null;
    
    // PID-21: Mother's Identifier (CX) (optional repeating)
    if ($this->sejour) {
      $naissance = new CNaissance();
      $naissance->sejour_enfant_id = $this->sejour->_id;
      $naissance->loadMatchingObject();

      if ($naissance->_id) {
        $sejour_maman = $naissance->loadRefSejourMaman();
        $sejour_maman->loadNDA($group->_id);
        
        $sejour_maman->loadRefPatient()->loadIPP($group->_id);
        $mother = $sejour_maman->_ref_patient;
        
        $identifiers = array();
        if ($mother->_IPP) {
          $identifiers[] = array(
            $mother->_IPP,
            null,
            null,
            // PID-3-4 Autorité d'affectation
            $this->getAssigningAuthority("FINESS", $group->finess),
            "PI"
          );
        }
        if ($sejour_maman->_NDA) {
          $identifiers[] = array(
            $sejour_maman->_NDA,
            null,
            null,
            // PID-3-4 Autorité d'affectation
            $this->getAssigningAuthority("FINESS", $group->finess),
            "AN"
          );
        }

        $data[] = $identifiers;
      }
      else {
        $data[] = null;
      }        
    }
    else {
      $data[] = null;
    }

    // PID-22: Ethnic Group (CE) (optional repeating)
    $data[] = null;
    
    // PID-23: Birth Place (ST) (optional)
    $data[] = null;
    
    // PID-24: Multiple Birth Indicator (ID) (optional)
    $data[] = null;
    
    // PID-25: Birth Order (NM) (optional)
    $data[] = $patient->rang_naissance;
    
    // PID-26: Citizenship (CE) (optional repeating)
    $data[] = null;
    
    // PID-27: Veterans Military Status (CE) (optional)
    $data[] = null;
    
    // PID-28: Nationality (CE) (optional)
    $data[] = null;
    
    // PID-29: Patient Death Date and Time (TS) (optional)
    $data[] = ($patient->deces) ? $patient->deces : null;
    
    // PID-30: Patient Death Indicator (ID) (optional)
    $data[] = ($patient->deces) ? "Y" : "N";
    
    // PID-31: Identity Unknown Indicator (ID) (optional)
    switch ($receiver->_configs["build_PID_31"]) {
      case 'avs':
        $data[] = $patient->avs;
        break;
      
      default:
        $data[] = null;
        break;
    }
    
    // PID-32: Identity Reliability Code (IS) (optional repeating)
    $data[] =  array (
      // Table - 0445
      // VIDE  - Identité non encore qualifiée
      // PROV  - Provisoire
      // VALI  - Validé
      // DOUB  - Doublon ou esclave
      // DESA  - Désactivé
      // DPOT  - Doublon potentiel
      // DOUA  - Doublon avéré
      // COLP  - Collision potentielle
      // COLV  - Collision validée
      // FILI  - Filiation
      // CACH  - Cachée
      // ANOM  - Anonyme
      // IDVER - Identité vérifiée par le patient
      // RECD  - Reçue d'un autre domaine
      // IDRA  - Identité rapprochée dans un autre domaine
      // USUR  - Usurpation
      // HOMD  - Homonyme detecté
      // HOMA  - Homonyme avéré
      is_numeric($patient->nom) ? "ANOM" : "VALI"
    );

    // PID-33: Last Update Date/Time (TS) (optional)
    $data[] =  $event->last_log->date;
    
    // PID-34: Last Update Facility (HD) (optional)
    $data[] = null;
    
    // PID-35: Species Code (CE) (optional)
    $data[] = null;
    
    // PID-36: Breed Code (CE) (optional)
    $data[] = null;
    
    // PID-37: Strain (ST) (optional)
    $data[] = null;
    
    // PID-38: Production Class Code (CE) (optional)
    $data[] = null;
    
    // PID-39: Tribal Citizenship (CWE) (optional repeating)
    $data[] = null;
          
    $this->fill($data);
  }

  /**
   * Fill other identifiers
   *
   * @param array         &$identifiers Identifiers
   * @param CPatient      $patient      Person
   * @param CInteropActor $actor        Interop actor
   *
   * @return null
   */
  function fillOtherIdentifiers(&$identifiers, CPatient $patient, CInteropActor $actor = null) {
    if (CValue::read($actor->_configs, "send_own_identifier")) {
      $identifiers[] = array(
        $patient->_id,
        null,
        null,
        // PID-3-4 Autorité d'affectation
        $this->getAssigningAuthority("mediboard"),
        "RI"
      );
    }

    if (!CValue::read($actor->_configs, "send_self_identifier")) {
      return;
    }

    if (!$idex_actor = $actor->getIdex($patient)->id400) {
      return;
    }

    $identifiers[] = array(
      $idex_actor,
      null,
      null,
      // PID-3-4 Autorité d'affectation
      $this->getAssigningAuthority("actor"),
    );
  }
}
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
  var $patient = null;
  var $sejour  = null;
  var $set_id  = null;
  
  function build(CHL7v2Event $event) {
    parent::build($event, "PID");
    
    $message  = $event->message;
    $receiver = $event->_receiver;
    $group    = $receiver->_ref_group;
    
    $patient  = new CPatient;
    $patient  = $this->patient;
    
    $data = array();
    // PID-1: Set ID - PID (SI) (optional)
    $data[] = $this->set_id;
    
    // PID-2: Patient ID (CX) (optional)
    $data[] = null;
    
    // PID-3: Patient Identifier List (CX) (repeating)
    // Table - 0203
    // RI - Resource identifier
    // PI - Patient internal identifier
    $identifiers = array();
    if ($patient->_IPP) {
      $identifiers[] = array(
        $patient->_IPP,
        null,
        null,
        // PID-3-4 Autorité d'affectation
        array(
          // Nom, FINESS 
          array (
            $group->_view,
            $group->finess 
          )
        ),
        "PI"
      );
    }
    $identifiers[] = array(
      $patient->_id,
      null,
      null,
      // PID-3-4 Autorité d'affectation
      array(
        // Nom de l'application
        "Mediboard",
        // L'OID de l'application
        "1.2.250.1.2.3.4",
        // Editeur de l'application
        "OpenXtrem"
      ),
      "RI"
    );
    if ($patient->INSC) {
      $identifiers[] = array(
      $patient->INSC,
      null,
      null,
      // PID-3-4 Autorité d'affectation
      array(
        // Nom de l'application
        null,
        // L'OID de l'application
        "1.2.250.1.213.1.4.2",
        // Editeur de l'application
        "ISO"
      ),
      "INS-C",
      null,
      $patient->INSC_date
    );
    }
    $data[] = $identifiers;
    
    // PID-4: Alternate Patient ID - PID (CX) (optional repeating)
    $data[] = null;
    
    // PID-5: Patient Name (XPN) (repeating)
    $patient_names = array();
    // Nom usuel
    $patient_usualname = array(
      $patient->nom,
      $patient->prenom,
      "$patient->prenom_2 $patient->prenom_3 $patient->prenom_4",
      null,
      $patient->civilite,
      null,
      // Table 0200
      // A - Alias Name
      // B - Name at Birth
      // C - Adopted Name
      // D - Display Name
      // I - Licensing Name
      // L - Legal Name
      // M - Maiden Name
      // N - Nickname /_Call me_ Name/Street Name
      // P - Name of Partner/Spouse (retained for backward compatibility only)
      // R - Registered Name (animals only)
      // S - Coded Pseudo-Name to ensure anonymity
      // T - Indigenous/Tribal/Community Name
      // U - Unspecified
      (is_numeric($patient->nom)) ? "S" : "L",
      // Table 465
      // A - Alphabetic (i.e., Default or some single-byte)
      // I - Ideographic (i.e., Kanji)  
      // P - Phonetic (i.e., ASCII, Katakana, Hiragana, etc.) 
      "A"
    );
    // Cas nom de jeune fille
    if ($patient->nom_jeune_fille) {
      $patient_birthname = $patient_usualname;
      $patient_birthname[0] = $patient->nom_jeune_fille;
      // Legal Name devient Display Name
      $patient_usualname[6] = "D"; 
    }
    $patient_names[] = $patient_usualname;
    if ($patient->nom_jeune_fille) {
      $patient_names[] = $patient_birthname;
    } 
    $data[] = $patient_names;
    
    // PID-6: Mother's Maiden Name (XPN) (optional repeating)
    $data[] = null;
    
    // PID-7: Date/Time of Birth (TS) (optional)
    $data[] = $patient->naissance;
    
    // PID-8: Administrative Sex (IS) (optional)
    // Table - 0001
    // F - Female
    // M - Male
    // O - Other
    // U - Unknown
    // A - Ambiguous  
    // N - Not applicable
    $data[] = strtoupper($patient->sexe);
    
    // PID-9: Patient Alias (XPN) (optional repeating)
    $data[] =  null;
    
    // PID-10: Race (CE) (optional repeating)
    $data[] =  null;
    
    // PID-11: Patient Address (XAD) (optional repeating)
    $address = array();
    
    $address[] = array(
      $patient->adresse,
      null,
      $patient->ville,
      null,
      $patient->cp,
      $patient->pays_insee,
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
    $address[] = array(
      null,
      null,
      $patient->lieu_naissance,
      null,
      $patient->cp_naissance,
      $patient->pays_naissance_insee,
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
      "BR",
    );
    $data[] =  $address;
    
    // PID-12: County Code (IS) (optional)
    $data[] =  null;
    
    // PID-13: Phone Number - Home (XTN) (optional repeating)
    $phones = array();
    if ($patient->tel) {
      $phones[] = array(
        $patient->tel,
        // Table - 0201
        // ASN - Answering Service Number
        // BPN - Beeper Number 
        // EMR - Emergency Number  
        // NET - Network (email) Address
        // ORN - Other Residence Number 
        // PRN - Primary Residence Number 
        // VHN - Vacation Home Number  
        // WPN - Work Number
        "PRN",
        // Table - 0202
        // BP       - Beeper  
        // CP       - Cellular Phone  
        // FX       - Fax 
        // Internet - Internet Address: Use Only If Telecommunication Use Code Is NET 
        // MD       - Modem 
        // PH       - Telephone  
        // TDD      - Telecommunications Device for the Deaf  
        // TTY      - Teletypewriter
        "PH"
      );
    }
    if ($patient->tel2) {
      $phones[] = array(
        $patient->tel2,
        // Table - 0201
        "ORN",
        // Table - 0202
        "CP"
      );
    }
    if ($patient->tel_autre) {
      $phones[] = array(
        $patient->tel_autre,
        // Table - 0201
        "ORN",
        // Table - 0202
        "PH"
      );
    }
    $data[] =  $phones;
    
    // PID-14: Phone Number - Business (XTN) (optional repeating)
    $data[] =  null;
    
    // PID-15: Primary Language (CE) (optional)
    $data[] =  null;
    
    // PID-16: Marital Status (CE) (optional)
    $data[] =  null;
    
    // PID-17: Religion (CE) (optional)
    $data[] =  null;
    
    // PID-18: Patient Account Number (CX) (optional)
    if ($this->sejour) {
      $data[] = array(
        $sejour->_NDA,
        null,
        null,
        // PID-3-4 Autorité d'affectation
        array(
          // Nom, FINESS 
          array (
            $group->_view,
            $group->finess 
          )
        ),
        "PI"
      );
    }
    
    // PID-19: SSN Number - Patient (ST) (optional)
    $data[] =  null;
    
    // PID-20: Driver's License Number - Patient (DLN) (optional)
    $data[] =  null;
    
    // PID-21: Mother's Identifier (CX) (optional repeating)
    $data[] =  null;
    
    // PID-22: Ethnic Group (CE) (optional repeating)
    $data[] =  null;
    
    // PID-23: Birth Place (ST) (optional)
    $data[] =  null;
    
    // PID-24: Multiple Birth Indicator (ID) (optional)
    $data[] =  null;
    
    // PID-25: Birth Order (NM) (optional)
    $data[] =  null;
    
    // PID-26: Citizenship (CE) (optional repeating)
    $data[] =  null;
    
    // PID-27: Veterans Military Status (CE) (optional)
    $data[] =  null;
    
    // PID-28: Nationality (CE) (optional)
    $data[] =  null;
    
    // PID-29: Patient Death Date and Time (TS) (optional)
    if ($patient->deces) {
      $data[] = $patient->deces;
    }
    
    // PID-30: Patient Death Indicator (ID) (optional)
    $data[] = ($patient->deces) ? "Y" : "N";
    
    // PID-31: Identity Unknown Indicator (ID) (optional)
    $data[] =  null;
    
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
    $data[] =  null;
    
    // PID-35: Species Code (CE) (optional)
    $data[] =  null;
    
    // PID-36: Breed Code (CE) (optional)
    $data[] =  null;
    
    // PID-37: Strain (ST) (optional)
    $data[] =  null;
    
    // PID-38: Production Class Code (CE) (optional)
    $data[] =  null;
    
    // PID-39: Tribal Citizenship (CWE) (optional repeating)
    $data[] =  null;
          
    $this->fill($data);
  }
}
?>
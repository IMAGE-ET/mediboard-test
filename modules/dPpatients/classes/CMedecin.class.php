<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * The CMedecin Class
 */
class CMedecin extends CPerson {
  // DB Table key
  public $medecin_id;

  // DB Fields
  public $nom;
  public $prenom;
  public $jeunefille;
  public $adresse;
  public $ville;
  public $cp;
  public $tel;
  public $fax;
  public $portable;
  public $email;
  public $disciplines;
  public $orientations;
  public $complementaires;
  public $type;
  public $adeli;
  public $rpps;
  public $email_apicrypt;
  public $last_ldap_checkout;

  // Object References
  public $_ref_patients;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'medecin';
    $spec->key   = 'medecin_id';
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["patients_traites"]        = "CPatient medecin_traitant";
    $backProps["patients_correspondants"] = "CCorrespondant medecin_id";
    $backProps["sejours_adresses"]        = "CSejour adresse_par_prat_id";
    $backProps["consultations_adresses"]  = "CConsultation adresse_par_prat_id";
    $backProps["echanges_hprim21"]        = "CEchangeHprim21 object_id";
    $backProps["correspondants_courrier"] = "CCorrespondantCourrier object_id";
    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    
    $medecin_strict = (CAppUI::conf("dPpatients CMedecin medecin_strict") == 1 ? ' notNull' : '');
    
    $props["nom"]                 = "str notNull confidential seekable";
    $props["prenom"]              = "str seekable";
    $props["jeunefille"]          = "str confidential";
    $props["adresse"]             = "text$medecin_strict confidential";
    $props["ville"]               = "str$medecin_strict confidential seekable";
    $props["cp"]                  = "numchar$medecin_strict maxLength|5 confidential";
    $props["tel"]                 = "phone confidential$medecin_strict";
    $props["fax"]                 = "phone confidential";
    $props["portable"]            = "phone confidential";
    $props["email"]               = "str confidential";
    $props["disciplines"]         = "text seekable";
    $props["orientations"]        = "text";
    $props["complementaires"]     = "text";
    $props["type"]                = "enum list|medecin|kine|sagefemme|infirmier|dentiste|podologue|".
                                    "pharmacie|maison_medicale|autre default|medecin";
    $props["adeli"]               = "numchar length|9 confidential mask|99S9S99999S9";
    $props["rpps"]                = "numchar length|11 confidential mask|99999999999 control|luhn";
    $props["email_apicrypt"]      = "email confidential";
    $props["last_ldap_checkout"]  = "date";
    
    return $props;
  }

  /**
   * Compte les patients attachés
   *
   * @return void
   */
  function countPatients() {
    $this->_count_patients_traites        = $this->countBackRefs("patients_traites");
    $this->_count_patients_correspondants = $this->countBackRefs("patients_correspondants");
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    $this->nom = CMbString::upper($this->nom);
    $this->prenom = CMbString::capitalize(CMbString::lower($this->prenom));

    $this->mapPerson();

    if ($this->type == 'medecin') {
      $this->_view = "Dr $this->nom $this->prenom";
    }
    else {
      $this->_view = "$this->nom $this->prenom";
      if ($this->type) {
        $this->_view .= " ({$this->_specs['type']->_locales[$this->type]})";
      } 
    }
  }

  /**
   * @see parent::updatePlainFields()
   */
  function updatePlainFields() {
    parent::updatePlainFields();

    if ($this->nom) {
      $this->nom = CMbString::upper($this->nom);
    }
    if ($this->prenom) {
      $this->prenom = CMbString::capitalize(CMbString::lower($this->prenom));
    }
  }

  /**
   * @see parent::loadRefs()
   */
  function loadRefs() {
    // Backward references
    $obj = new CPatient();
    $this->_ref_patients = $obj->loadList("medecin_traitant = '$this->medecin_id'");
  }

  /**
   * Charge les médecins identiques
   *
   * @param bool $strict_cp Stricte sur la recherche par code postal
   *
   * @return self[]
   */
  function loadExactSiblings($strict_cp = true) {
    $medecin = new CMedecin();
    $where           = array();
    $where["nom"]    = $this->_spec->ds->prepare(" = %", $this->nom);
    $where["prenom"] = $this->_spec->ds->prepare(" = %", $this->prenom);
    
    if (!$strict_cp) {
      $cp = substr($this->cp, 0, 2);
      $where["cp"] = " LIKE '{$cp}___'";
    }
    else {
      $where["cp"] = " = '$this->cp'";
    }
    
    $medecin->escapeValues();

    $siblings = $medecin->loadList($where);
    unset($siblings[$this->_id]);

    return $siblings;
  }

  /**
   * Exporte au format vCard
   *
   * @param CMbvCardExport $vcard Objet vCard
   *
   * @return void
   */
  function toVcard(CMbvCardExport $vcard) {
    $vcard->addName($this->prenom, $this->nom, ucfirst($this->civilite));
    $vcard->addPhoneNumber($this->tel     , 'WORK');
    $vcard->addPhoneNumber($this->portable, 'CELL');
    $vcard->addPhoneNumber($this->fax     , 'FAX');
    $vcard->addEmail($this->email);
    $vcard->addAddress($this->adresse, $this->ville, $this->cp, $this->pays, 'WORK');
  }

  /**
   * Map the class variable with CPerson variable
   *
   * @return void
   */
  function mapPerson() {
    $this->_p_city                = $this->ville;
    $this->_p_postal_code         = $this->cp;
    $this->_p_street_address      = $this->adresse;
    $this->_p_phone_number        = $this->tel;
    $this->_p_fax_number          = $this->fax;
    $this->_p_mobile_phone_number = $this->portable;
    $this->_p_email               = $this->email;
    $this->_p_first_name          = $this->prenom;
    $this->_p_last_name           = $this->nom;
    $this->_p_maiden_name         = $this->jeunefille;
  }
}
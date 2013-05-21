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
 * Correspondants du patient
 */
class CCorrespondantPatient extends CPerson {
  // DB Table key
  public $correspondant_patient_id;

  // DB Fields
  public $patient_id;
  public $relation;
  public $relation_autre;
  public $nom;
  public $nom_jeune_fille;
  public $prenom;
  public $naissance;
  public $adresse;
  public $cp;
  public $ville;
  public $tel;
  public $mob;
  public $fax;
  public $urssaf;
  public $parente;
  public $parente_autre;
  public $email;
  public $remarques;
  public $ean;
  public $ean_base;
  public $type_pec;
  public $assure_id;
  public $ean_id;
  public $date_debut;
  public $date_fin;
  public $num_assure;
  public $employeur;

  public $_eai_initiateur_group_id;

  // Form fields
  public $_duplicate;
  public $_is_obsolete = false;

  /** @var CPatient */
  public $_ref_patient;

  /**
   * Initialize object specification
   *
   * @return CMbObjectSpec the spec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "correspondant_patient";
    $spec->key   = "correspondant_patient_id";
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    $props["patient_id"] = "ref class|CPatient cascade";
    $props["relation"]   = "enum list|assurance|autre|confiance|employeur|inconnu|prevenir default|prevenir";
    $props["relation_autre"] = "str";
    $props["nom"]        = "str seekable confidential";
    $props["nom_jeune_fille"] = "str";
    $props["prenom"]     = "str";
    $props["naissance"]  = "birthDate mask|99/99/9999 format|$3-$2-$1";
    $props["adresse"]    = "text";
    $props["cp"]         = "numchar minLength|4 maxLength|5";
    $props["ville"]      = "str confidential";
    $props["tel"]        = "phone confidential";
    $props["mob"]        = "phone confidential";
    $props["fax"]        = "phone confidential";
    $props["urssaf"]     = "numchar length|11 confidential";
    $props["parente"]    = "enum list|ami|ascendant|autre|beau_fils|colateral|collegue|compagnon|conjoint|directeur|divers|employeur|".
      "employe|enfant|enfant_adoptif|entraineur|epoux|frere|grand_parent|mere|pere|petits_enfants|proche|proprietaire|soeur|tuteur";
    $props["parente_autre"] = "str";
    $props["email"]      = "email";
    $props["remarques"]  = "text";
    $props["ean"]        = "str maxLength|30";
    $props["ean_base"]   = "str maxLength|30";
    $props["type_pec"]   = "enum list|TG|TP|TS";
    $props["assure_id"]  = "str maxLength|30";
    $props["ean_id"]     = "str maxLength|5";
    $props["date_debut"] = "date";
    $props["date_fin"]   = "date";
    $props["num_assure"] = "str maxLength|30";
    $props["employeur"]  = "ref class|CCorrespondantPatient";

    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    $this->mapPerson();

    $this->_view = $this->relation ?
      CAppUI::tr("CCorrespondantPatient.relation.".$this->relation) :
      $this->relation_autre;

    $this->_longview = "$this->nom $this->prenom";

    if ($this->date_fin && $this->date_fin < CMbDT::date()) {
      $this->_is_obsolete = true;
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

    if ($this->nom_jeune_fille) {
      $this->nom_jeune_fille = CMbString::upper($this->nom_jeune_fille);
    }

    if ($this->prenom) {
      $this->prenom = CMbString::capitalize(CMbString::lower($this->prenom));
    }

    if (!$this->_id) {
      $this->date_debut = CMbDT::date();
    }

    if ($this->_duplicate) {
      $this->nom .= " (Copy)";

      $this->_id        = null;
      $this->date_debut = CMbDT::date();
      $this->date_fin   = "";

      $this->_duplicate = null;
    }
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    $backProps["correspondants_courrier"] = "CCorrespondantCourrier object_id";
    $backProps["fact_consult_maladie"]    = "CFactureCabinet assurance_maladie";
    $backProps["fact_consult_accident"]   = "CFactureCabinet assurance_accident";
    $backProps["fact_sejour_maladie"]     = "CFactureEtablissement assurance_maladie";
    $backProps["fact_sejour_accident"]    = "CFactureEtablissement assurance_accident";
    $backProps["employeur"]               = "CCorrespondantPatient employeur";

    return $backProps;
  }

  /**
   * Load patient
   *
   * @return CPatient
   */
  function loadRefPatient() {
    return $this->_ref_patient = $this->loadFwdRef("patient_id");
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
    $this->_p_mobile_phone_number = $this->mob;
    $this->_p_email               = $this->email;
    $this->_p_first_name          = $this->prenom;
    $this->_p_last_name           = $this->nom;
    $this->_p_birth_date          = $this->naissance;
    $this->_p_maiden_name         = $this->nom_jeune_fille;
  }
}
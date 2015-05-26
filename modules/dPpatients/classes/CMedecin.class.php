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

  // Owner
  public $function_id;

  // DB Fields
  public $nom;
  public $prenom;
  public $jeunefille;
  public $sexe;

  /** @var string Practitioner title */
  public $titre;

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

  // form fields
  public $_titre_long;

  /** @var string Current user starting formula */
  public $_starting_formula;

  /** @var string Current user closing formula */
  public $_closing_formula;

  // Object References
  public $_ref_patients;

  // Calculated fields
  public $_count_patients_traites;
  public $_count_patients_correspondants;
  public $_has_siblings;
  public $_confraternite;

  /** @var string Practitioner long view (with title in full text) */
  public $_longview;

  /** @var CFunctions */
  public $_ref_function;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec        = parent::getSpec();
    $spec->table = 'medecin';
    $spec->key   = 'medecin_id';

    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps                            = parent::getBackProps();
    $backProps["patients_traites"]        = "CPatient medecin_traitant";
    $backProps["patients_correspondants"] = "CCorrespondant medecin_id";
    $backProps["sejours_adresses"]        = "CSejour adresse_par_prat_id";
    $backProps["consultations_adresses"]  = "CConsultation adresse_par_prat_id";
    $backProps["echanges_hprim21"]        = "CEchangeHprim21 object_id";
    $backProps["echanges_hprimsante"]     = "CExchangeHprimSante object_id";
    $backProps["correspondants_courrier"] = "CCorrespondantCourrier object_id";

    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    $medecin_strict = (CAppUI::conf("dPpatients CMedecin medecin_strict") == 1 ? ' notNull' : '');

    $props["function_id"]        = "ref class|CFunctions";
    $props["nom"]                = "str notNull confidential seekable";
    $props["prenom"]             = "str seekable";
    $props["jeunefille"]         = "str confidential";
    $props["sexe"]               = "enum list|u|f|m default|u";
    $props["titre"]              = "enum list|m|mme|dr|pr";
    $props["adresse"]            = "text$medecin_strict confidential";
    $props["ville"]              = "str$medecin_strict confidential seekable";
    $props["cp"]                 = "numchar$medecin_strict maxLength|5 confidential";
    $props["tel"]                = "phone confidential$medecin_strict";
    $props["fax"]                = "phone confidential";
    $props["portable"]           = "phone confidential";
    $props["email"]              = "str confidential";
    $props["disciplines"]        = "text seekable";
    $props["orientations"]       = "text";
    $props["complementaires"]    = "text";
    $props["type"]               = "enum list|medecin|kine|sagefemme|infirmier|dentiste|podologue|" .
      "pharmacie|maison_medicale|autre default|medecin";
    $props["adeli"]              = "numchar length|9 confidential mask|99S9S99999S9";
    $props["rpps"]               = "numchar length|11 confidential mask|99999999999 control|luhn";
    $props["email_apicrypt"]     = "email confidential";
    $props["last_ldap_checkout"] = "date";

    $props["_starting_formula"] = "str";
    $props["_closing_formula"]  = "str";

    return $props;
  }

  /**
   * @see parent::store()
   */
  function store() {
    // Création d'un correspondant en mode cabinets distincts
    if (CAppUI::conf('dPpatients CPatient function_distinct') && !$this->_id) {
      $this->function_id = CMediusers::get()->function_id;
    }

    // sexe undefined
    if ($this->sexe == "u") {
      $this->guessSex();
    }

    return parent::store();
  }

  /**
   * guess sexe by firstname
   *
   * @return boolean true if sexe found, false if sexe not found
   */
  function guessSex() {
    $sex_found = CFirstNameAssociativeSex::getSexFor($this->prenom);
    if ($sex_found && $sex_found != "u") {
      $this->sexe = $sex_found;

      return true;
    }

    return false;
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

    $this->nom    = CMbString::upper($this->nom);
    $this->prenom = CMbString::capitalize(CMbString::lower($this->prenom));

    $this->mapPerson();

    $this->_shortview = "{$this->nom} {$this->prenom}";
    $this->_view      = "{$this->nom} {$this->prenom}";
    $this->_longview  = "{$this->nom} {$this->prenom}";

    if ($this->type == "medecin") {
      $this->_confraternite = $this->sexe == "f" ? "Chère consoeur" : "Cher confrère";

      if (!$this->titre) {
        $this->_view      = CAppUI::tr("CMedecin.titre.dr") . " {$this->nom} {$this->prenom}";
        $this->_longview  = CAppUI::tr("CMedecin.titre.dr-long") . " {$this->nom} {$this->prenom}";
      }
    }

    if ($this->titre) {
      $this->_view       = CAppUI::tr("CMedecin.titre.{$this->titre}") . " {$this->_view}";
      $this->_titre_long = CAppUI::tr("CMedecin.titre.{$this->titre}-long");
      $this->_longview   = "{$this->_titre_long} {$this->nom} {$this->prenom}";
    }

    if ($this->type && $this->type != 'medecin') {
      $this->_view     .= " ({$this->_specs['type']->_locales[$this->type]})";
      $this->_longview .= " ({$this->_specs['type']->_locales[$this->type]})";
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
    $obj                 = new CPatient();
    $this->_ref_patients = $obj->loadList("medecin_traitant = '$this->medecin_id'");
  }

  /**
   * Chargement de la fonction reliée
   *
   * @return CFunctions
   */
  function loadRefFunction() {
    return $this->_ref_function = $this->loadFwdRef("function_id", true);
  }

  /**
   * Charge les médecins identiques
   *
   * @param bool $strict_cp Stricte sur la recherche par code postal
   *
   * @return self[]
   */
  function loadExactSiblings($strict_cp = true) {
    $medecin         = new self();
    $where           = array();
    $where["nom"]    = $this->_spec->ds->prepare(" = %", $this->nom);
    $where["prenom"] = $this->_spec->ds->prepare(" = %", $this->prenom);

    if (!$strict_cp) {
      $cp          = substr($this->cp, 0, 2);
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
   * @see parent::getSexFieldName()
   */
  function getSexFieldName() {
    return 'sexe';
  }

  /**
   * Exporte au format vCard
   *
   * @param CMbvCardExport $vcard Objet vCard
   *
   * @return void
   */
  function toVcard(CMbvCardExport $vcard) {
    $vcard->addName($this->prenom, $this->nom, "");
    $vcard->addPhoneNumber($this->tel, 'WORK');
    $vcard->addPhoneNumber($this->portable, 'CELL');
    $vcard->addPhoneNumber($this->fax, 'FAX');
    $vcard->addEmail($this->email);
    $vcard->addAddress($this->adresse, $this->ville, $this->cp, "", 'WORK');
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
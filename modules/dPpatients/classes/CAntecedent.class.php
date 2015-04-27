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
 * Antecedent
 */
class CAntecedent extends CMbObject {
  // DB Table key
  public $antecedent_id;

  // DB fields
  public $type;
  public $appareil;
  public $date;
  public $rques;
  public $dossier_medical_id;
  public $annule;

  public $owner_id;
  public $creation_date;

  // Form Fields
  public $_search;
  public $_aides_all_depends;
  public $_idex_code;
  public $_idex_tag;

  // Distant fields
  public $_count_rques_aides;
  public $_count_rques_aides_appareil;

  /** @var CDossierMedical */
  public $_ref_dossier_medical;
  
  // Types
  static $types = array(
    'med', 'alle', 'trans', 'obst', 'deficience', 'chir', 'fam', 'anesth', 'gyn', 
    'cardio', 'pulm', 'stomato', 'plast', 'ophtalmo', 'digestif', 'gastro', 
    'stomie', 'uro', 'ortho', 'traumato', 'amput', 'neurochir', 'greffe', 'thrombo',
    'cutane', 'hemato', 'rhumato', 'neuropsy', 'infect', 'endocrino', 'carcino', 
    'orl', 'addiction', 'habitus', 'coag'
  );
  
  // Types that should not be types, mostly appareils
  static $non_types = array(
    'obst', 'gyn', 'cardio', 'stomato', 'digestif', 'gastro', 'stomie', 'neuropsy', 
    'endocrino', 'orl', 'uro', 'ortho', 'pulm',
  );
  
  // Appareils
  static $appareils = array(
    'cardiovasculaire', 'digestif', 'endocrinien', 'neuro_psychiatrique',
    'pulmonaire', 'uro_nephrologique', 'orl', 'gyneco_obstetrique', 'orthopedique',
    'ophtalmologique', 'locomoteur',
  );

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'antecedent';
    $spec->key   = 'antecedent_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["type"]               = "enum list|" . CAppUI::conf("patients CAntecedent types");
    $props["appareil"]           = "enum list|" . CAppUI::conf("patients CAntecedent appareils");
    $props["date"]               = "date progressive";
    $props["rques"]              = "text helped|type|appareil";
    $props["dossier_medical_id"] = "ref notNull class|CDossierMedical";
    $props["annule"]             = "bool";
    $props["owner_id"]           = "ref class|CMediusers";
    $props["creation_date"]      = "dateTime";
    $props["_search"]            = "str";

    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["identifiants"] = "CIdSante400 object_id cascade";
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->rques;
  }

  /**
   * Charge le dossier médical associé
   *
   * @return CDossierMedical
   */
  function loadRefDossierMedical() { 
    $this->_ref_dossier_medical = new CDossierMedical();
    return $this->_ref_dossier_medical->load($this->dossier_medical_id);
  }

  /**
   * @see parent::loadView()
   */
  function loadView(){
    parent::loadView();
    $this->loadLogs();
    $this->updateOwnerAndDates();
    $this->loadRefDossierMedical();
  }

  /**
   * Update owner and creation date from user logs
   *
   * @return void
   */
  function updateOwnerAndDates(){
    if (!$this->_id || $this->owner_id && $this->creation_date) {
      return;
    }

    if (empty($this->_ref_logs)) {
      $this->loadLogs();
    }

    $first_log = $this->_ref_first_log;

    $this->owner_id      = $first_log->user_id;
    $this->creation_date = $first_log->date;

    $this->rawStore();
  }

  /**
   * @see parent::store()
   */
  function store() {
    $this->completeField("type");
    if ($this->type == "alle") {
      $this->loadRefDossierMedical();
      $dossier_medical = $this->_ref_dossier_medical;
      if ($dossier_medical->object_class == "CPatient") {
        DSHM::remKeys("alertes-*-CPatient-".$dossier_medical->object_id);
      }
    }

    // Save owner and creation date
    if (!$this->_id) {
      $now = CMbDT::dateTime();
      $this->creation_date = $now;

      if (!$this->owner_id) {
        $this->owner_id = CMediusers::get()->_id;
      }
    }
    
    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }

    // DossierMedical store
    $this->checkCodeCim10();

    // Sauvegarde de l'identifiant externe (code composant de la BDM pour le cas des allergies)
    if ($this->_idex_code && $this->_idex_tag) {
      $idex = new CIdSante400();
      $idex->setObject($this);
      $idex->id400 = $this->_idex_code;
      $idex->tag   = $this->_idex_tag;
      $idex->store();
    }

    return null;
  }

  /**
   * @see parent::delete()
   */
  function delete() {
    $this->completeField("type", "dossier_medical_id");
    
    if ($this->type == "alle") {
      $this->loadRefDossierMedical();
      $dossier_medical = $this->_ref_dossier_medical;
      if ($dossier_medical->object_class == "CPatient") {
        DSHM::remKeys("alertes-*-CPatient-".$dossier_medical->object_id);
      }
    }
    
    return parent::delete();
  }

  /**
   * Vérifie et extrait les codes CIM des remarques pour les sauvegarder dans le dossier médical
   *
   * @return void
   */
  function checkCodeCim10(){
    preg_match_all('/\b[A-Z]\d{2}\.?\d{0,2}\b/i', $this->rques, $matches);

    foreach ($matches as $match_) {
      foreach ($match_ as &$match) {
        // Transformation du code CIM pour le tester
        $match = str_replace(".", "", $match);
        $match = strtoupper($match);
        
        // Chargement du code CIM 10
        $code_cim10 = CCodeCIM10::get($match);
    
        if ($code_cim10->libelle != "Code CIM inexistant") {
          // Cas du code valide, sauvegarde du code CIM
          $dossier_medical = new CDossierMedical();
          $dossier_medical->load($this->dossier_medical_id);
          
          // si le code n'est pas deja present, on le rajoute
          if (!array_key_exists($match, $dossier_medical->_ext_codes_cim)) {
            if ($dossier_medical->codes_cim != "") {
              $dossier_medical->codes_cim .= "|";
            }
            $dossier_medical->codes_cim .= $match;
            $dossier_medical->store();
          }
        }
      }
    }
  }

  /**
   * @see parent::check()
   */
  function check() {
    //Si on merge le dossier médical et que le type n'existe pas
    if (
        $this->_forwardRefMerging && in_array($this->type, CAntecedent::$non_types) || !in_array($this->type, CAntecedent::$types) ||
        $this->fieldModified("annule", "1") // On ne verifie pas le type si on annule un ATCD
    ) {
      return null;
    }
    return parent::check();
  }

  /**
   * @see parent::loadAides()
   */
  function loadAides(
      $user_id,
      $needle = null,
      $depend_value_1 = null,
      $depend_value_2 = null,
      $object_field = null,
      $strict = "true"
  ) {
    parent::loadAides($user_id, $needle, $depend_value_1, $depend_value_2);
    
    $rques_aides =& $this->_aides_all_depends["rques"];
    if (!isset($rques_aides)) {
      return;
    }

    $depend_field_1 = $this->_specs["rques"]->helped[0];
    $depend_values_1 = $this->_specs[$depend_field_1]->_list;
    asort($depend_values_1);
    $depend_values_1[] = "";
    foreach ($depend_values_1 as $depend_value_1) {
      $count =& $this->_count_rques_aides;
      $count[$depend_value_1] = 0;
      if (isset($rques_aides[$depend_value_1])) {
        foreach ($rques_aides[$depend_value_1] as $aides_by_depend_field_2) {
          $count[$depend_value_1] += count($aides_by_depend_field_2);
        }
      }
    }
  }
}

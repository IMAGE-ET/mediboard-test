<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage dPpatients
 *  @version $Revision$
 *  @author Fabien Ménager
 */

class CConstantesMedicales extends CMbObject {
  // DB Table key
  var $constantes_medicales_id = null;

  // DB Fields
  var $patient_id            = null;
  var $datetime              = null;
  var $context_class         = null;
  var $context_id            = null;
  
  // The other fields are built in the contructor
  /*
  var $poids                 = null;
  var $taille                = null;
  var $ta                    = null;
  var $pouls                 = null;
  var $spo2                  = null;
  var $temperature           = null;
  var $score_sensibilite     = null;
  var $score_motricite       = null;
  var $EVA                   = null;
  var $score_sedation        = null;
  var $frequence_respiratoire = null;
  var $glycemie              = null;
  var $redon                 = null;
  var $diurese               = null;
  var $injection             = null;
  */

  // Object References
  //    Single
  var $_ref_context          = null;
  var $_ref_patient          = null;
  
  // Forms fields
  /*
  var $_ta_systole           = null;
  var $_ta_diastole          = null;
  var $_inj                  = null;
  var $_inj_essai            = null;
  var $_imc                  = null;
  */
  var $_imc_valeur           = null;
  var $_vst                  = null;
  var $_new_constantes_medicales = null;
  
  static $list_constantes = array (
    "poids"             => array("unit" => "kg", "callback" => "calculImcVst"), 
    "taille"            => array("unit" => "cm", "callback" => "calculImcVst"),
    "pouls"             => array("unit" => "/min"), 
    "ta"                => array("unit" => "cm Hg", "formfields" => array("_ta_systole", "_ta_diastole")),
    "_vst"              => array("unit" => "ml"),
    "_imc"              => array("unit" => ""),
    "temperature"       => array("unit" => "°C"), 
    "spo2"              => array("unit" => "%"), 
    "score_sensibilite" => array("unit" => ""),
    "score_motricite"   => array("unit" => ""),
    "score_sedation"    => array("unit" => ""),
    "frequence_respiratoire"=> array("unit" => ""),
    "EVA"               => array("unit" => ""),
    "glycemie"          => array("unit" => "g/l"),
    "redon"             => array("unit" => "ml"),
    "diurese"           => array("unit" => "ml"),
    "injection"         => array("unit" => "kg", "formfields" => array("_inj", "_inj_essai")),
  );
  
  function __construct() {
    foreach(self::$list_constantes as $_constant => $_params) {
      $this->$_constant = null;
      if (isset($_params["formfields"])) {
        foreach ($_params["formfields"] as $_formfield) {
          $this->$_formfield = null;
        }
      }
    }
    return parent::__construct();
  }

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'constantes_medicales';
    $spec->key   = 'constantes_medicales_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs['patient_id']             = 'ref notNull class|CPatient';
    $specs['datetime']               = 'dateTime notNull';
    $specs['context_class']          = 'str';
    $specs['context_id']             = 'ref class|CMbObject meta|context_class cascade';
    $specs['poids']                  = 'float pos';
    $specs['taille']                 = 'float pos';
    $specs['ta']                     = 'str maxLength|10';
    $specs['pouls']                  = 'num pos';
    $specs['spo2']                   = 'float min|0 max|100';
    $specs['temperature']            = 'float min|20 max|50'; // Au cas ou il y aurait des malades très malades
    $specs['score_sensibilite']      = 'float min|0 max|5';
    $specs['score_motricite']        = 'float min|0 max|5';
    $specs['EVA']                    = 'float min|0 max|10';
    $specs['score_sedation']         = 'float';
    $specs['frequence_respiratoire'] = 'float pos';
		$specs['glycemie']               = 'float pos max|10';
	  $specs['redon']                  = 'float pos min|0 max|1000';
    $specs['diurese']                = 'float pos min|0';
    $specs['injection']              = 'str maxLength|10';
    $specs['_imc']                   = 'float pos';
    $specs['_vst']                   = 'float pos';
    $specs['_ta_systole']            = 'num pos max|50';
    $specs['_ta_diastole']           = 'num pos max|50';
		$specs['_inj']                   = 'num pos';
		$specs['_inj_essai']             = 'num pos moreEquals|_inj';
    return $specs;
  }

  function updateFormFields() {
    parent::updateFormFields();
    
    $this->loadRefPatient();
    
    // Calcul de l'Indice de Masse Corporelle
    if($this->poids && $this->taille) {
      $this->_imc = round($this->poids / ($this->taille * $this->taille * 0.0001), 2);
    }
    
    // Détermination valeur IMC
    if ($this->poids && $this->taille) {
      $seuils = ($this->_ref_patient->sexe != 'm') ? 
        array(19, 24): 
        array(20, 25);
      
      if ($this->_imc < $seuils[0]) {
        $this->_imc_valeur = 'Maigreur';
      } 
      elseif ($this->_imc > $seuils[1] && $this->_imc <= 30) {
        $this->_imc_valeur = 'Surpoids';
      }
      elseif ($this->_imc > 30 && $this->_imc <= 40) {
        $this->_imc_valeur = 'Obésité';
      }
      elseif ($this->_imc > 40) {
        $this->_imc_valeur = 'Obésité morbide';
      }
    }
    
    // Calcul du Volume Sanguin Total
    if ($this->poids) {
      $this->_vst = (($this->_ref_patient->sexe != 'm') ? 65 : 70) * $this->poids;
    }

    $_ta = explode('|', $this->ta);
    if ($this->ta && isset($_ta[0]) && isset($_ta[1])) {
      $this->_ta_systole  = $_ta[0];
      $this->_ta_diastole = $_ta[1];
    }
		
		$_injection = explode('|', $this->injection);
    if ($this->injection && isset($_injection[0]) && isset($_injection[1])) {
      $this->_inj  = $_injection[0];
      $this->_inj_essai = $_injection[1];
    }
		
  }
  
  function updateDBFields() {
    if (!empty($this->_ta_systole) && !empty($this->_ta_diastole)) {
      $this->ta = "$this->_ta_systole|$this->_ta_diastole";
    }
    if (!empty($this->_inj) && !empty($this->_inj_essai)) {
      $this->injection = "$this->_inj|$this->_inj_essai";
    }
  }
  
  function loadRefContext() {
    if ($this->context_class && $this->context_id) {
      $this->_ref_context = new $this->context_class;
      $this->_ref_context = $this->_ref_context->getCached($this->context_id);
    }
  }
  
  function loadRefPatient() {
    $this->_ref_patient = new CPatient;
    $this->_ref_patient = $this->_ref_patient->getCached($this->patient_id);
  }

  function loadRefsFwd() {
    $this->loadRefContext();
    $this->loadRefPatient();
  }
  
  function check() {
    if ($msg = parent::check()) {
      return $msg;
    }
    
    // Verifie si au moins une des valeurs est remplie
    $ok = false;
    foreach (CConstantesMedicales::$list_constantes as $const => $params) {
      $this->completeField($const);
      if ($this->$const !== "" && $this->$const !== null) {
        $ok = true;
        break;
      }
    }
    if (!$ok) return 'Au moins une des valeurs doit être renseignée';
  }
  
  function store () {
    if (!$this->_id && !$this->_new_constantes_medicales) {
      $this->updateDBFields();
      $constante = new CConstantesMedicales();
      $constante->patient_id    = $this->patient_id;
      $constante->context_class = $this->context_class;
      $constante->context_id    = $this->context_id;
      
      if ($constante->loadMatchingObject()) {
        foreach (CConstantesMedicales::$list_constantes as $type => $params) {
          if (empty($this->$type) && !empty($constante->$type)) {
            $this->$type = $constante->$type;
          }
        }
        $this->_id = $constante->_id;
      }
    }
    return parent::store();
  }
  
  static function getLatestFor($patient) {
    $patient_id = ($patient instanceof CPatient) ? $patient->_id : $patient;
    
    // Constante que l'on va construire
    $constante = new CConstantesMedicales();
    if(!$patient_id) {
      return array($constante, array());
    }
    $constante->patient_id = $patient_id;
    $constante->datetime = mbDateTime();
    $constante->loadRefPatient();
    
    // Liste des constantes enregistrés du patient
    $list = new CConstantesMedicales();
    $list->patient_id = $patient_id;
    $list = $list->loadMatchingList('datetime DESC');
    
    // Liste des dates des dernières valeurs
    $list_datetimes = array();
    
    foreach (CConstantesMedicales::$list_constantes as $type => $params) {
      $list_datetimes[$type] = null;
    }
    
    // Pour toutes les constantes existantes
    foreach ($list as $const) {
      if ($constante->context_class == null && $const->context_class != null) {
        $constante->context_class = $const->context_class;
        $constante->context_id = $const->context_id;
      }
      
      $continue = false;
      foreach (CConstantesMedicales::$list_constantes as  $type => $params) {
        if ($const->$type != null && $constante->$type == null) {
          $constante->$type = $const->$type;
          $list_datetimes[$type] = $const->datetime;
        }
        if ($constante->$type == null) {
          $continue = true;
        }
      }
      if (!$continue) break;
    }
    
    $constante->updateFormFields();
    return array($constante, $list_datetimes);
  }
}
?>
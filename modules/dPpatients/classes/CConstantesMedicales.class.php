<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage dPpatients
 *  @version $Revision$
 *  @author Fabien Ménager
 */

/**
 * Constantes médicales
 *
 * @property float $poids
 * @property float $taille
 * @property float $pouls
 * @property float $temperature
 * @property string $ta
 */
class CConstantesMedicales extends CMbObject {
  // DB Table key
  var $constantes_medicales_id = null;

  // DB Fields
  var $patient_id            = null;
  var $datetime              = null;
  var $context_class         = null;
  var $context_id            = null;
  var $comment               = null;

  // Object References
  //    Single
  var $_ref_context          = null;
  var $_ref_patient          = null;

  // Forms fields
  var $_imc_valeur           = null;
  var $_vst                  = null;
  var $_new_constantes_medicales = null;

  // Other fields
  var $_ref_user             = null;

  static $_specs_converted = false;
  static $_latest_values = array();

  static $list_constantes = array (
    "poids"             => array(
      "type" => "physio",
      "unit" => "kg",
      "callback" => "calculImcVst",
      "min" => "@-2", "max" => "@+2",
    ),
    "taille"            => array(
      "type" => "physio",
      "unit" => "cm",
      "callback" => "calculImcVst",
      "min" => "@-5", "max" => "@+5",
    ),
    "pouls"             => array(
      "type" => "physio",
      "unit" => "puls./min",
      "min" => 70, "max" => 120,
      "standard" => 90,
      "colors" => array("black")
    ),
    "ta"                => array(
      "type" => "physio",
      "unit" => "cmHg",
      "formfields" => array("_ta_systole", "_ta_diastole"),
      "min" => 2, "max" => 16,
      "standard" => 8,
      "colors" => array("#00A8F0", "#C0D800"),
      "conversion" => array("mmHg" => 10),
      "candles" => true,
    ),
    "ta_gauche"         => array(
      "type" => "physio",
      "unit" => "cmHg",
      "formfields" => array("_ta_gauche_systole", "_ta_gauche_diastole"),
      "min" => 2, "max" => 16,
      "standard" => 8,
      "colors" => array("#00A8F0", "#C0D800"),
      "conversion" => array("mmHg" => 10),
      "candles" => true,
    ),
    "ta_droit"          => array(
      "type" => "physio",
      "unit" => "cmHg",
      "formfields" => array("_ta_droit_systole", "_ta_droit_diastole"),
      "min" => 2, "max" => 16,
      "standard" => 8,
      "colors" => array("#00A8F0", "#C0D800"),
      "conversion" => array("mmHg" => 10),
      "candles" => true,
    ),
    "_vst"              => array(
      "type" => "physio",
      "unit" => "ml",
      "min" => 5000,
      "max" => 7000,
    ),
    "_imc"              => array(
      "type" => "physio",
      "unit" => "",
      "min" => 12, "max" => 40,
      "plot" => true,
    ),
    "temperature"       => array(
      "type" => "physio",
      "unit" => "°C",
      "min" => 36, "max" => 40,
      "standard" => 37.5,
      "colors" => array("orange")
    ),
    "spo2"              => array(
      "type" => "physio",
      "unit" => "%",
      "min" => 70, "max" => 100
    ),
    "score_sensibilite" => array(
      "type" => "physio",
      "unit" => "",
      "min" => 0, "max" => 5
    ),
    "score_motricite"   => array(
      "type" => "physio",
      "unit" => "",
      "min" => 0, "max" => 5
    ),
    "score_sedation"    => array(
      "type" => "physio",
      "unit" => "",
      "min" => 70, "max" => 100
    ),
    "frequence_respiratoire"=> array(
      "type" => "physio",
      "unit" => "",
      "min" => 0, "max" => 60
    ),
    "EVA"               => array(
      "type" => "physio",
      "unit" => "",
      "min" => 0, "max" => 10
    ),
    "glycemie"          => array(
      "type" => "biolo",
      "unit" => "g/l",
      "min" => 0, "max" => 4
    ),
    "hemoglobine_rapide" => array(
      "type" => "biolo",
      "unit" => "g/dl",
      "min" => 3, "max" => 25
    ),
    "PVC"               => array(
      "type" => "physio",
      "unit" => "cm H2O",
      "min" => 4, "max" => 16
    ),
    "perimetre_abdo"    => array(
      "type" => "physio",
      "unit" => "cm",
      "min" => 20, "max" => 200
    ),
    "perimetre_cranien" => array(
      "type" => "physio",
      "unit" => "cm",
      "min"  => 30, "max" => 60
    ),
    "perimetre_cuisse" => array(
      "type" => "physio",
      "unit" => "cm",
      "min" => 20, "max" => 100
    ),
    "perimetre_cou"    => array(
      "type" => "physio",
      "unit" => "cm",
      "min" => 20, "max" => 50
    ),
    "perimetre_thoracique"=>array(
      "type" => "physio",
      "unit" => "cm",
      "min" => 20, "max" => 150
    ),
    "hauteur_uterine" => array(
      "type" => "physio",
      "unit" => "cm",
      "min" => 0, "max" => 35
    ),
    "injection"         => array(
      "type" => "physio",
      "unit" => "",
      "formfields" => array("_inj", "_inj_essai"),
      "min" => 0, "max" => 10
    ),
    "gaz" => array(
      "type" => "physio",
      "unit" => "",
      "min" => 0, "max" => 1,
    ),
    "selles" => array(
      "type" => "physio",
      "unit" => "",
      "min" => 0, "max" => 3,
    ),

    // Douleur
    "douleur_en" => array(
      "type" => "physio",
      "unit" => "",
      "min" => 0, "max" => 10
    ),
    "douleur_doloplus" => array(
      "type" => "physio",
      "unit" => "",
      "min" => 0, "max" => 30
    ),
    "douleur_algoplus" => array(
      "type" => "physio",
      "unit" => "",
      "min" => 0, "max" => 5
    ),
    "ecpa_avant" => array(
      "type" => "physio",
      "unit" => "",
      "min" => 0, "max" => 16
    ),
    "ecpa_apres" => array(
      "type" => "physio",
      "unit" => "",
      "min" => 0, "max" => 16
    ),
    "_ecpa_total" => array(
      "type" => "physio",
      "unit" => "",
      "min" => 0, "max" => 32,
      "plot" => true,
    ),

    // Vision
    "vision_oeil_droit" => array(
      "type" => "physio",
      "unit" => "",
      "min" => 0, "max" => 10
    ),
    "vision_oeil_gauche" => array(
      "type" => "physio",
      "unit" => "",
      "min" => 0, "max" => 10
    ),

    "peak_flow" => array(
      "type" => "physio",
      "unit" => "L/min",
      "min" => 60, "max" => 900
    ),

    /// DRAINS ///
    "sng"           => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 1000,
      "cumul_reset_config" => "sng_cumul_reset_hour",
    ),
    "redon"             => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "redon_cumul_reset_hour",
    ),
    "redon_2"           => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "redon_cumul_reset_hour",
    ),
    "redon_3"           => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "redon_cumul_reset_hour",
    ),
    "redon_4"           => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "redon_cumul_reset_hour",
    ),
    "lame_1"           => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "lame_cumul_reset_hour",
    ),
    "lame_2"           => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "lame_cumul_reset_hour",
    ),
    "lame_3"           => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "lame_cumul_reset_hour",
    ),
    "drain_1"           => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "drain_cumul_reset_hour",
    ),
    "drain_2"           => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "drain_cumul_reset_hour",
    ),
    "drain_3"           => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "drain_cumul_reset_hour",
    ),
    "drain_thoracique_1" => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "drain_thoracique_cumul_reset_hour",
    ),
    "drain_thoracique_2" => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "drain_thoracique_cumul_reset_hour",
    ),
    "drain_thoracique_flow" => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 1000,
    ),
    "drain_pleural_1"   => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "drain_pleural_cumul_reset_hour",
    ),
    "drain_pleural_2"   => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "drain_pleural_cumul_reset_hour",
    ),
    "drain_mediastinal" => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "drain_mediastinal_cumul_reset_hour",
    ),
    "drain_shirley" => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "drain_mediastinal_cumul_reset_hour",
    ),

    // DIURESE ///////
    "_diurese"              => array( // Diurèse reelle, calculé
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 1000,
      "plot" => true,
      "color" => "#00A8F0",
      "cumul_reset_config" => "diuere_24_reset_hour",
      "formula" => array(
        "diurese"            => "+",  // Miction naturelle
        "sonde_ureterale_1"  => "+",
        "sonde_ureterale_2"  => "+",
        "sonde_nephro_1"     => "+",
        "sonde_nephro_2"     => "+",
        "sonde_vesicale"     => "+",
        "catheter_suspubien" => "+",
        "bricker"            => "+",
        "entree_lavage"      => "-",
      ),
      "alert_low" => array(0, "#ff3232"),
    ),

    // Ureteral
    "sonde_ureterale_1" => array( // gauche
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "sonde_ureterale_cumul_reset_hour",
    ),
    "sonde_ureterale_2" => array( // droite
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "sonde_ureterale_cumul_reset_hour",
    ),

    // Nephrostomie
    "sonde_nephro_1"    => array( // gauche
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "sonde_nephro_cumul_reset_hour",
    ),
    "sonde_nephro_2"    => array( // droite
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "sonde_nephro_cumul_reset_hour",
    ),

    "sonde_vesicale"    => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 200,
      "cumul_reset_config" => "sonde_vesicale_cumul_reset_hour",
    ),
    "catheter_suspubien" => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 200,
      "cumul_reset_config" => "sonde_vesicale_cumul_reset_hour",
    ),
    "bricker" => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 200,
      "cumul_reset_config" => "sonde_vesicale_cumul_reset_hour",
    ),
    "diurese"           => array( // Miction naturelle
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 1000,
      "cumul_reset_config" => "diuere_24_reset_hour",
    ),
    "entree_lavage" => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 200,
    ),
    // FIN DIURESE ////////
    "creatininemie" => array(
      "type" => "biolo",
      "unit" => "mg/l",
      "min" => 0, "max" => 30,
    ),
    "glasgow" => array(
      "type" => "physio",
      "unit" => "",
      "min" => 3, "max" => 15,
    ),
  );

  static $list_constantes_type = array(
    "physio" => array(),
    "drain" => array(),
    "biolo" => array(),
  );

  function __construct() {
    foreach (self::$list_constantes as $_constant => $_params) {
      $this->$_constant = null;

      // Champs "composites"
      if (isset($_params["formfields"])) {
        foreach ($_params["formfields"] as $_formfield) {
          $this->$_formfield = null;
        }
      }
    }

    parent::__construct();

    // Conversion des specs
    if (self::$_specs_converted) {
      return;
    }

    $func_min = create_function('$matches', 'return "min|".$matches[1]*10;');
    $func_max = create_function('$matches', 'return "max|".$matches[1]*10;');

    foreach (self::$list_constantes as $_constant => &$_params) {
      $unit = "mmHg";

      if (isset($_params["conversion"][$unit])) {
        if (in_array($_constant, array("ta", "ta_gauche", "ta_droit"))) {
          if (CAppUI::conf("dPpatients CConstantesMedicales unite_ta") == "cmHg") {
            continue;
          }
        }

        $conv = $_params["conversion"][$unit];

        if (isset($_params["formfields"])) {
          foreach ($_params["formfields"] as $_formfield) {
            $spec = $this->_specs[$_formfield];
            $this->_specs[$_formfield]->prop = preg_replace_callback("/min\|([0-9]+)/", $func_min, $spec);
            $this->_specs[$_formfield]->prop = preg_replace_callback("/max\|([0-9]+)/", $func_max, $spec);

            if (isset($spec->min)) {
              $spec->min *= $conv;
            }
            if (isset($spec->max)) {
              $spec->max *= $conv;
            }
          }
        }
        else {
          $spec = $this->_specs[$_constant];
          $this->_specs[$_formfield]->prop = preg_replace_callback("/min\|([0-9]+)/", $func_min, $spec);
          $this->_specs[$_formfield]->prop = preg_replace_callback("/max\|([0-9]+)/", $func_max, $spec);

          if (isset($spec->min)) {
            $spec->min *= $conv;
          }
          if (isset($spec->max)) {
            $spec->max *= $conv;
          }
        }
      }
    }

    self::$_specs_converted = true;
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
    $specs['context_class']          = 'str'; // CConsultation|CSejour|CPatient (import)
    $specs['context_id']             = 'ref class|CMbObject meta|context_class cascade';
    $specs['comment']                = 'text';

    $specs['poids']                  = 'float pos';
    $specs['taille']                 = 'float pos';

    $specs['ta']                     = 'str maxLength|10';
    $specs['_ta_systole']            = 'num pos max|50';
    $specs['_ta_diastole']           = 'num pos max|50';

    $specs['ta_gauche']              = 'str maxLength|10';
    $specs['_ta_gauche_systole']     = 'num pos max|50';
    $specs['_ta_gauche_diastole']    = 'num pos max|50';

    $specs['ta_droit']               = 'str maxLength|10';
    $specs['_ta_droit_systole']      = 'num pos max|50';
    $specs['_ta_droit_diastole']     = 'num pos max|50';

    $specs['pouls']                  = 'num pos';
    $specs['spo2']                   = 'float min|0 max|100';
    $specs['temperature']            = 'float min|20 max|50'; // Au cas ou il y aurait des malades très malades
    $specs['score_sensibilite']      = 'float min|0 max|5';
    $specs['score_motricite']        = 'float min|0 max|5';
    $specs['EVA']                    = 'float min|0 max|10';
    $specs['score_sedation']         = 'float';
    $specs['frequence_respiratoire'] = 'float pos';
    $specs['glycemie']               = 'float pos max|10';
    $specs['hemoglobine_rapide']     = 'float';
    $specs['PVC']                    = 'float min|0';
    $specs['perimetre_abdo']         = 'float min|0';
    $specs['perimetre_cranien']      = 'float min|0';
    $specs['perimetre_cuisse']       = 'float min|0';
    $specs['perimetre_cou']          = 'float min|0';
    $specs['perimetre_thoracique']   = 'float min|0';
    $specs['hauteur_uterine']        = 'float min|0';
    $specs['peak_flow']              = 'float min|0';
    $specs['_imc']                   = 'float pos';
    $specs['_vst']                   = 'float pos';

    $specs['injection']              = 'str maxLength|10';
    $specs['_inj']                   = 'num pos';
    $specs['_inj_essai']             = 'num pos moreEquals|_inj';

    $specs['gaz']                    = 'num min|0';
    $specs['selles']                 = 'num min|0';

    // Douleur
    $specs['douleur_en']             = 'float min|0 max|10';
    $specs['douleur_doloplus']       = 'num min|0 max|30';
    $specs['douleur_algoplus']       = 'num min|0 max|5';
    $specs['ecpa_avant']             = 'num min|0 max|16';
    $specs['ecpa_apres']             = 'num min|0 max|16';
    $specs['_ecpa_total']            = 'num min|0 max|32';

    // Vision
    $specs['vision_oeil_droit']      = 'num min|0 max|10';
    $specs['vision_oeil_gauche']     = 'num min|0 max|10';

    $specs['redon']                  = 'float min|0';
    $specs['redon_2']                = 'float min|0';
    $specs['redon_3']                = 'float min|0';
    $specs['redon_4']                = 'float min|0';
    $specs['diurese']                = 'float min|0'; // Miction naturelle
    $specs['_diurese']               = 'float min|0'; // Vraie diurèse (calculée)
    $specs['sng']                    = 'float min|0';
    $specs['lame_1']                 = 'float min|0';
    $specs['lame_2']                 = 'float min|0';
    $specs['lame_3']                 = 'float min|0';
    $specs['drain_1']                = 'float min|0';
    $specs['drain_2']                = 'float min|0';
    $specs['drain_3']                = 'float min|0';
    $specs['drain_thoracique_1']     = 'float min|0';
    $specs['drain_thoracique_2']     = 'float min|0';
    $specs['drain_thoracique_flow']  = 'float min|0';
    $specs['drain_pleural_1']        = 'float min|0';
    $specs['drain_pleural_2']        = 'float min|0';
    $specs['drain_mediastinal']      = 'float min|0';
    $specs['drain_shirley']          = 'float min|0';
    $specs['sonde_ureterale_1']      = 'float min|0';
    $specs['sonde_ureterale_2']      = 'float min|0';
    $specs['sonde_nephro_1']         = 'float min|0';
    $specs['sonde_nephro_2']         = 'float min|0';
    $specs['sonde_vesicale']         = 'float min|0';
    $specs['catheter_suspubien']     = 'float min|0';
    $specs['bricker']                = 'float min|0';
    $specs['entree_lavage']          = 'float min|0';
    $specs['creatininemie']          = 'float min|0';
    $specs['glasgow']                = 'float min|0';
    return $specs;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["administrations"]   = "CAdministration constantes_medicales_id";
    return $backProps;
  }

  function updateFormFields() {
    parent::updateFormFields();

    $this->loadRefPatient();

    // Calcul de l'Indice de Masse Corporelle
    if ($this->poids && $this->taille) {
      $this->_imc = round($this->poids / ($this->taille * $this->taille * 0.0001), 2);
    }

    // Afficher le champ diurèse dans le formulaire si une des valeurs n'est pas vide
    // FIXME Utiliser "cumul_in"
    foreach (self::$list_constantes["_diurese"]["formula"] as $_field => $_sign) {
      if ($this->{$_field} && $this->_diurese == null) {
        $this->_diurese = " ";
        break;
      }
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

    $unite_ta = CAppUI::conf("dPpatients CConstantesMedicales unite_ta");

    $_ta = explode('|', $this->ta);
    if ($this->ta && isset($_ta[0]) && isset($_ta[1])) {
      $this->_ta_systole  = $_ta[0];
      $this->_ta_diastole = $_ta[1];
      if ($unite_ta == "mmHg") {
        $this->_ta_systole *= 10;
        $this->_ta_diastole *= 10;
      }
    }

    $_ta_gauche = explode('|', $this->ta_gauche);
    if ($this->ta_gauche && isset($_ta_gauche[0]) && isset($_ta_gauche[1])) {
      $this->_ta_gauche_systole  = $_ta_gauche[0];
      $this->_ta_gauche_diastole = $_ta_gauche[1];
      if ($unite_ta == "mmHg") {
        $this->_ta_gauche_systole *= 10;
        $this->_ta_gauche_diastole *= 10;
      }
    }

    $_ta_droit = explode('|', $this->ta_droit);
    if ($this->ta_droit && isset($_ta_droit[0]) && isset($_ta_droit[1])) {
      $this->_ta_droit_systole  = $_ta_droit[0];
      $this->_ta_droit_diastole = $_ta_droit[1];
      if ($unite_ta == "mmHg") {
        $this->_ta_droit_systole *= 10;
        $this->_ta_droit_diastole *= 10;
      }
    }

    $_injection = explode('|', $this->injection);
    if ($this->injection && isset($_injection[0]) && isset($_injection[1])) {
      $this->_inj  = $_injection[0];
      $this->_inj_essai = $_injection[1];
    }

    // Calcul de l'ECPA total
    $this->_ecpa_total = null;
    if ($this->ecpa_avant !== null) {
      $this->_ecpa_total += $this->ecpa_avant;
    }
    if ($this->ecpa_apres !== null) {
      $this->_ecpa_total += $this->ecpa_apres;
    }
  }

  function updatePlainFields() {
    // TODO: Utiliser les specs

    $unite_ta = CAppUI::conf("dPpatients CConstantesMedicales unite_ta");

    if (!empty($this->_ta_systole) && !empty($this->_ta_diastole)) {
      if ($unite_ta ==  "mmHg") {
        $this->_ta_systole /= 10;
        $this->_ta_diastole /= 10;
      }
      $this->ta = "$this->_ta_systole|$this->_ta_diastole";
    }
    if ($this->_ta_systole === '' && $this->_ta_diastole === '') {
      $this->ta = '';
    }

    if (!empty($this->_ta_gauche_systole) && !empty($this->_ta_gauche_diastole)) {
      if ($unite_ta == "mmHg") {
        $this->_ta_gauche_systole /= 10;
        $this->_ta_gauche_diastole /= 10;
      }
      $this->ta_gauche = "$this->_ta_gauche_systole|$this->_ta_gauche_diastole";
    }
    if ($this->_ta_gauche_systole === '' && $this->_ta_gauche_diastole === '') {
      $this->ta_gauche = '';
    }

    if (!empty($this->_ta_droit_systole) && !empty($this->_ta_droit_diastole)) {
      if ($unite_ta ==  "mmHg") {
        $this->_ta_droit_systole /= 10;
        $this->_ta_droit_diastole /= 10;
      }
      $this->ta_droit = "$this->_ta_droit_systole|$this->_ta_droit_diastole";
    }
    if ($this->_ta_droit_systole === '' && $this->_ta_droit_diastole === '') {
      $this->ta_droit = '';
    }

    if (!empty($this->_inj) && !empty($this->_inj_essai)) {
      $this->injection = "$this->_inj|$this->_inj_essai";
    }
    if ($this->_inj === '' && $this->_inj_essai === '') {
      $this->injection = '';
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

  function loadRefUser() {
    $first_log = $this->loadFirstLog();
    $this->_ref_user = $first_log->loadRefUser();
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
    if (!$ok) {
      return CAppUI::tr("CConstantesMedicales-min_one_constant");
    }
  }

  function store () {
    // S'il ne reste plus qu'un seul champ et que sa valeur est passée à vide,
    // alors on supprime la constante.
    if (($this->_id && ($this->fieldModified("taille") || $this->fieldModified("poids"))) || (!$this->_id && ($this->taille || $this->poids))) {
      $this->completeField("patient_id");
      SHM::remKeys("bcb-alertes-*-CPatient-".$this->patient_id);
    }

    if ($this->_id) {
      $ok = false;
      foreach (CConstantesMedicales::$list_constantes as $const => $params) {
        $this->completeField($const);
        if ($this->$const !== "" && $this->$const !== null) {
          $ok = true;
          break;
        }
      }
      if (!$ok) {
        return parent::delete();
      }
    }

    if (!$this->_id && !$this->_new_constantes_medicales) {
      $this->updatePlainFields();
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

  function delete() {
    $this->completeField("taille", "poids", "patient_id");
    if ($this->taille || $this->poids) {
      SHM::remKeys("bcb-alertes-*-CPatient-".$this->patient_id);
    }
    return parent::delete();
  }

  /**
   * Get the latest constantes values
   *
   * @param int|CPatient $patient   The patient to load the constantes for
   * @param string       $datetime  The reference datetime
   * @param array        $selection A selection of constantes to load
   *
   * @return array The constantes values and dates
   */
  static function getLatestFor($patient, $datetime = null, $selection = array()) {
    $patient_id = ($patient instanceof CPatient) ? $patient->_id : $patient;

    if (isset(self::$_latest_values[$patient_id][$datetime])) {
      return self::$_latest_values[$patient_id][$datetime];
    }

    if (empty($selection)) {
      $list_constantes = CConstantesMedicales::$list_constantes;
    }
    else {
      $list_constantes = array_intersect_key(CConstantesMedicales::$list_constantes, array_flip($selection));
    }

    // Constante que l'on va construire
    $constante = new CConstantesMedicales();
    if (!$patient_id) {
      return array($constante, array());
    }

    $constante->patient_id = $patient_id;
    $constante->datetime = mbDateTime();
    $constante->loadRefPatient();

    $where = array(
      "patient_id" => "= '$patient_id'",
    );

    if ($datetime) {
      $where["datetime"] = "<= '$datetime'";
    }

    $list_datetimes = array();
    foreach ($list_constantes as $type => $params) {
      $list_datetimes[$type] = null;

      if ($type[0] == "_") {
        continue;
      }

      $_where = $where;
      $_where[$type] = "IS NOT NULL";
      $_list = $constante->loadList($_where, "datetime DESC", 1);

      if (count($_list)) {
        $_const = reset($_list);
        $constante->$type = $_const->$type;
        $list_datetimes[$type] = $_const->datetime;
      }
    }

    // Cumul de la diurese
    if ($datetime) {
      foreach ($list_constantes as $_name => $_params) {
        if (isset($_params["cumul_for"]) || isset($_params["formula"])) {
          $day_defore = mbDateTime("-24 hours", $datetime);

          // cumul simple sur le meme champ
          if (isset($_params["cumul_for"])) {
            $cumul_for = $_params["cumul_for"];

            $_where = $where;
            $_where[$cumul_for] = "IS NOT NULL";
            $_where[] = "datetime >= '$day_defore'";
            $_list = $constante->loadList($_where);

            foreach ($_list as $_const) {
              $constante->$_name += $_const->$cumul_for;
            }
          }

          // cumul de plusieurs champs (avec formule)
          else {
            $formula = $_params["formula"];

            foreach ($formula as $_field => $_sign) {
              $_where = $where;
              $_where[$_field] = "IS NOT NULL";
              $_where[] = "datetime >= '$day_defore'";

              $_list = $constante->loadList($_where);

              foreach ($_list as $_const) {
                if ($_sign === "+") {
                  $constante->$_name += $_const->$_field;
                }
                else {
                  $constante->$_name -= $_const->$_field;
                }
              }
            }
          }
        }
      }
    }

    $constante->updateFormFields();

    // Don't cache partial loadings
    if (empty($selection)) {
      self::$_latest_values[$patient_id][$datetime] = array($constante, $list_datetimes);
    }

    return array($constante, $list_datetimes);
  }

  static function getColor($value, $params, $default_color = "#4DA74D") {
    $color = CValue::read($params, "color", $default_color);

    // Low value alert
    if (isset($params["alert_low"])) {
      list($_low, $_low_color) = $params["alert_low"];

      if ($value < $_low) {
        $color = $_low_color;
      }
    }

    // High value alert
    if (isset($params["alert_high"])) {
      list($_high, $_high_color) = $params["alert_high"];

      if ($value > $_high) {
        $color = $_high_color;
      }
    }

    return $color;
  }

  static function buildGrid($list, $full = true, $only_with_value = false) {
    $grid = array();
    $selection = array_keys(CConstantesMedicales::$list_constantes);
    $cumuls_day = array();

    if (!$full) {
      $conf_constantes = explode("|", CConstantesMedicales::getConfig("important_constantes"));
      $selection = $conf_constantes;

      foreach ($list as $_constante_medicale) {
        foreach (CConstantesMedicales::$list_constantes as $_name => $_params) {
          if ($_constante_medicale->$_name != '' && !empty($_params["cumul_in"])) {
            $selection = array_merge($selection, $_params["cumul_in"]);
          }
        }
      }

      $selection = array_unique($selection);
    }

    if ($only_with_value) {
      $selection = array();
    }

    $names = $selection;

    foreach ($list as $_constante_medicale) {
      if (!isset($grid["$_constante_medicale->datetime $_constante_medicale->_id"])) {
        $grid["$_constante_medicale->datetime $_constante_medicale->_id"] = array(
          "comment" => $_constante_medicale->comment,
          "values"  => array(),
        );
      }

      foreach (CConstantesMedicales::$list_constantes as $_name => $_params) {
        if (in_array($_name, $selection) || $_constante_medicale->$_name != '') {
          $value = null;

          // cumul
          if (isset($_params["cumul_for"]) || isset($_params["formula"])) {
            $reset_hour = self::getResetHour($_name);
            $day_24h = mbTransformTime("-$reset_hour hours", $_constante_medicale->datetime, '%y-%m-%d');

            if (!isset($cumuls_day[$_name][$day_24h])) {
              $cumuls_day[$_name][$day_24h] = array(
                "id"    => $_constante_medicale->_id,
                "datetime" => $_constante_medicale->datetime,
                "value" => null,
                "span"  => 0,
                "pair"  => (@count($cumuls_day[$_name]) % 2 ? "odd" : "even"),
                "day"   => mbTransformTime($day_24h, null, "%a"),
              );
            }

            // cumul simple sur le meme champ
            if (isset($_params["cumul_for"])) {
              $cumul_for  = $_params["cumul_for"];

              if ($_constante_medicale->$cumul_for !== null) {
                $cumuls_day[$_name][$day_24h]["value"] += $_constante_medicale->$cumul_for;
              }
            }

            // cumul de plusieurs champs (avec formule)
            else {
              $formula  = $_params["formula"];

              foreach ($formula as $_field => $_sign) {
                $_value = $_constante_medicale->$_field;

                if ($_constante_medicale->$_field !== null) {
                  if ($_sign === "+") {
                    $cumuls_day[$_name][$day_24h]["value"] += $_value;
                  }
                  else {
                    $cumuls_day[$_name][$day_24h]["value"] -= $_value;
                  }
                }
              }
            }

            $cumuls_day[$_name][$day_24h]["span"]++;

            $value = "__empty__";
          }

          // valeur normale
          else {
            $spec = self::$list_constantes[$_name];
            $value = $_constante_medicale->$_name;

            if (isset($spec["formfields"])) {
              $arr = array();
              foreach ($spec["formfields"] as $ff) {
                if ($_constante_medicale->$ff != "") {
                  $arr[] = $_constante_medicale->$ff;
                }
              }
              $value = implode(" / ", $arr);
            }
          }

          $grid["$_constante_medicale->datetime $_constante_medicale->_id"]["values"][$_name] = $value;

          if (!in_array($_name, $names)) {
            $names[] = $_name;
          }
        }
      }
    }

    foreach ($cumuls_day as $_name => &$_days) {
      $_params = CConstantesMedicales::$list_constantes[$_name];

      foreach ($_days as &$_values) {
        $_color = CConstantesMedicales::getColor($_values["value"], $_params, null);
        $_values["color"] = $_color;

        $grid[$_values["datetime"]." ".$_values["id"]]["values"][$_name] = $_values;
      }
    }

    $names = self::sortConstNames($names);

    return array(
      $names, "names" => $names,
      $grid,  "grid"  => $grid,
    );
  }

  static function sortConstNames($names) {
    $new_names = array();

    foreach (self::$list_constantes as $key => $params) {
      if (in_array($key, $names)) {
        $new_names[] = $key;
      }
    }

    return $new_names;
  }

  static function getRelated($selection, CPatient $patient, CMbObject $context = null, $date_min = null, $date_max = null, $limit = null) {
    $where = array(
      "patient_id" => " = '$patient->_id'"
    );

    if ($context) {
      $where["context_class"] = " = '$context->_class'";
      $where["context_id"]    = " = '$context->_id'";
    }

    $whereOr = array();
    foreach ($selection as $name) {
      if ($name[0] === "_") {
        continue;
      }

      $whereOr[] = "`$name` IS NOT NULL";
    }
    $where[] = implode(" OR ", $whereOr);

    if ($date_min) {
      $where[] = "datetime >= '$date_min'";
    }

    if ($date_max) {
      $where[] = "datetime <= '$date_max'";
    }

    $constantes = new self;
    return array_reverse($constantes->loadList($where, "datetime DESC", $limit), true);
  }

  static function initParams(){
    // make a copy of the array as it will be modified
    $list_constantes = CConstantesMedicales::$list_constantes;

    foreach ($list_constantes as $_constant => &$_params) {
      self::$list_constantes_type[$_params["type"]][$_constant] = &$_params;

      // Conversion des unités
      // FIXME Vraiment pas logique !!
      $unit = "mmHg";

      if (isset($_params["conversion"][$unit])) {
        if (in_array($_constant, array("ta", "ta_gauche", "ta_droit"))) {
          if (CAppUI::conf("dPpatients CConstantesMedicales unite_ta") == "cmHg") {
            continue;
          }
        }

        $conv = $_params["conversion"][$unit];

        $_params_ref = &CConstantesMedicales::$list_constantes[$_constant];
        $_params_ref["unit"]      = $unit;
        $_params_ref["standard"] *= $conv;
        $_params_ref["min"]      *= $conv;
        $_params_ref["max"]      *= $conv;
      }

      // Champs de cumuls
      if (isset($_params["cumul_reset_config"])) {
        if (!isset(CConstantesMedicales::$list_constantes[$_constant]["cumul_in"])) {
          CConstantesMedicales::$list_constantes[$_constant]["cumul_in"] = array();
        }

        if (empty($_params["formula"])) {
          CMbArray::insertAfterKey(
            CConstantesMedicales::$list_constantes, $_constant, "_{$_constant}_cumul",
            array(
              "cumul_for" => $_constant,
              "unit"      => $_params["unit"],
            )
          );

          CConstantesMedicales::$list_constantes[$_constant]["cumul_in"][] = "_{$_constant}_cumul";
        }
        else {
          foreach ($_params["formula"] as $_const => $_sign) {
            CConstantesMedicales::$list_constantes[$_const]["cumul_in"][] = $_constant;
          }
        }
      }
    }
  }

  /**
   * Get service or group specific configuration value
   *
   * @param string $name     Configuration name
   * @param int    $group_id Group ID
   *
   * @return mixed
   */
  static function getConfig($name, $group_id = null) {
    $service_id = null;
    if (isset($_SESSION["soins"]["service_id"])) {
      $service_id = $_SESSION["soins"]["service_id"];
    }
    elseif (isset($_SESSION["ecap"]["service_id"])) {
      $service_id = $_SESSION["ecap"]["service_id"];
    }

    $guid = null;
    if ($service_id) {
      $guid = "CService-$service_id";
    }
    elseif ($group_id) {
      $guid = "CGroups-$group_id";
    }

    return CAppUI::conf("dPpatients CConstantesMedicales $name", $guid);
  }

  /**
   * Get reset hour
   *
   * @param string $name     Reset name
   * @param int    $group_id Group id
   *
   * @return mixed
   */
  static function getResetHour($name, $group_id = null) {
    $list = CConstantesMedicales::$list_constantes;

    if (isset($list[$name]["cumul_reset_config"])) {
      $confname = $list[$name]["cumul_reset_config"];
    }
    else {
      $confname = $list[$list[$name]["cumul_for"]]["cumul_reset_config"];
    }

    return self::getConfig($confname, $group_id);
  }
}

CConstantesMedicales::initParams();

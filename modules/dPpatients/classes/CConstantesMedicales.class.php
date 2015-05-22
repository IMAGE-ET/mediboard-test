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
 * Constantes médicales
 *
 * @property float $poids
 * @property float $taille
 * @property float $pouls
 * @property float $temperature
 * @property float $_imc
 * @property float $_diurese
 * @property float $ecpa_avant
 * @property float $ecpa_apres
 * @property float $_ecpa_total
 * @property string $ta
 */
class CConstantesMedicales extends CMbObject {
  const CONV_ROUND_UP   = 3;
  const CONV_ROUND_DOWN = 2;

  public $constantes_medicales_id;

  // DB Fields
  public $user_id;
  public $creation_date;
  public $patient_id;
  public $datetime;
  public $context_class;
  public $context_id;
  public $comment;

  /** @var CConsultation|CSejour|CPatient */
  public $_ref_context;

  /** @var CPatient */
  public $_ref_patient;

  /** @var CMediusers */
  public $_ref_user;

  // Forms fields
  public $_poids_g;
  public $_imc_valeur;
  public $_poids_ideal;
  public $_vst;
  public $_tam;
  public $_urine_effective;
  public $_new_constantes_medicales;
  public $_unite_ta;
  public $_unite_glycemie;
  public $_unite_cetonemie;
  public $_unite_hemoglobine;

  public $_valued_cst = array();

  static $_specs_converted = false;
  static $_latest_values = array();
  static $_computed_constants_compounds = array();

  static $list_constantes = array (
    "poids"             => array(
      "type" => "physio",
      "unit" => "kg",
      "unit_iso" => "kg",
      "callback" => "calculImcVst",
      "min" => "@-2", "max" => "@+2",
    ),
    "_poids_g"          => array(
      "type" => "physio",
      "unit" => "g",
      "unit_iso" => "g",
      "plot" => true,
      "edit" => true,
      "min" => "@-200", "max" => "@+200"
    ),
    "taille"            => array(
      "type" => "physio",
      "unit" => "cm",
      "unit_iso" => "cm",
      "callback" => "calculImcVst",
      "min" => "@-5", "max" => "@+5",
    ),
    "pouls"             => array(
      "type" => "physio",
      "unit" => "puls./min",
      "min" => 20, "max" => 220,
      "norm_min" => 90,
      "colors" => array("black")
    ),
    "ta"                => array(
      "type" => "physio",
      "unit" => "cmHg",
      "unit_iso" => "cm",
      "formfields" => array("_ta_systole", "_ta_diastole"),
      "min" => 2, "max" => 16,
      "norm_min" => 8, "norm_max" => 14,
      "colors" => array("#00A8F0", "#C0D800"),
      "conversion" => array("mmHg" => 10),
      "candles" => true,
      "unit_config" => "unite_ta",
      "orig_unit"   => "cmHg"
    ),
    '_tam' => array(
      'type' => 'physio',
      'unit' => 'cmHg',
      'formfields' => array('_tam'),
      'min' => 2, 'max' => 16,
      'norm_min' => 8, 'norm_max' => 16,
      'conversion' => array('mmHg' => 10),
      'unit_config' => 'unite_ta',
      'orig_unit' => 'cmHg',
      "readonly" => true
    ),
    "ta_gauche"         => array(
      "type" => "physio",
      "unit" => "cmHg",
      "formfields" => array("_ta_gauche_systole", "_ta_gauche_diastole"),
      "min" => 2, "max" => 16,
      "norm_min" => 8, "norm_max" => 14,
      "colors" => array("#00A8F0", "#C0D800"),
      "conversion" => array("mmHg" => 10),
      "candles" => true,
      "unit_config" => "unite_ta",
      "orig_unit"   => "cmHg"
    ),
    "ta_droit"          => array(
      "type" => "physio",
      "unit" => "cmHg",
      "formfields" => array("_ta_droit_systole", "_ta_droit_diastole"),
      "min" => 2, "max" => 16,
      "norm_min" => 8, "norm_max" => 14,
      "colors" => array("#00A8F0", "#C0D800"),
      "conversion" => array("mmHg" => 10),
      "candles" => true,
      "unit_config" => "unite_ta",
      "orig_unit"   => "cmHg"
    ),
    "ta_couche"          => array(
      "type" => "physio",
      "unit" => "cmHg",
      "formfields" => array("_ta_couche_systole", "_ta_couche_diastole"),
      "min" => 2, "max" => 16,
      "norm_min" => 8, "norm_max" => 14,
      "colors" => array("#00A8F0", "#C0D800"),
      "conversion" => array("mmHg" => 10),
      "candles" => true,
      "unit_config" => "unite_ta",
      "orig_unit"   => "cmHg"
    ),
    "ta_assis"          => array(
      "type" => "physio",
      "unit" => "cmHg",
      "formfields" => array("_ta_assis_systole", "_ta_assis_diastole"),
      "min" => 2, "max" => 16,
      "norm_min" => 8, "norm_max" => 14,
      "colors" => array("#00A8F0", "#C0D800"),
      "conversion" => array("mmHg" => 10),
      "candles" => true,
      "unit_config" => "unite_ta",
      "orig_unit"   => "cmHg"
    ),
    "ta_debout"          => array(
      "type" => "physio",
      "unit" => "cmHg",
      "formfields" => array("_ta_debout_systole", "_ta_debout_diastole"),
      "min" => 2, "max" => 16,
      "norm_min" => 8, "norm_max" => 14,
      "colors" => array("#00A8F0", "#C0D800"),
      "conversion" => array("mmHg" => 10),
      "candles" => true,
      "unit_config" => "unite_ta",
      "orig_unit"   => "cmHg"
    ),
    "_vst"              => array(
      "type" => "physio",
      "unit" => "ml",
      "min" => 5000,
      "max" => 7000,
      "readonly" => true
    ),
    "_imc"              => array(
      "type" => "physio",
      "unit" => "",
      "min" => 12, "max" => 40,
      "plot" => true,
      "readonly" => true
    ),
    "_poids_ideal"              => array(
      "type" => "physio",
      "unit" => "kg",
      "min" => 0, "max" => 150,
      "plot" => true,
      "readonly" => true
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
    "sens_membre_inf_d" => array(
      "type" => "physio",
      "unit" => "",
      "min" => 0, "max" => 5
    ),
    "sens_membre_inf_g" => array(
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
    "contraction_uterine"=> array(
      "type" => "physio",
      "unit" => "/10min",
      "min" => 0, "max" => 10
    ),
    "bruit_foetal"=> array(
      "type" => "physio",
      "unit" => "bpm",
      "min" => 0, "max" => 220
    ),

    "glycemie"          => array(
      "type" => "biolo",
      "unit" => "g/l",
      "min" => 0, "max" => 4,
      "conversion" => array("mmol/l" => 5.56), // 1 g/l => 5.56 mmol/l
      "unit_config" => "unite_glycemie",
      "orig_unit"   => "g/l",
      "formfields" => array("_glycemie"),
    ),
    "cetonemie"         => array(
      "type" => "biolo",
      "unit" => "g/l",
      "min" => 0, "max" => 4,
      "conversion" => array("mmol/l" => 17.2), // 1 g/l => 17.2 mmol/l
      "unit_config" => "unite_cetonemie",
      "orig_unit"   => "g/l",
      "formfields" => array("_cetonemie"),
    ),
    "hemoglobine_rapide" => array(
      "type" => "biolo",
      "unit" => "g/dl",
      "min" => 3, "max" => 25,
      "conversion" => array('g/l' => 10),
      'unit_config' => 'unite_hemoglobine',
      'orig_unit' => 'g/dl',
      'formfields' => array('_hemoglobine_rapide')
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
    "perimetre_hanches"    => array(
      "type" => "physio",
      "unit" => "cm",
      "min" => 45, "max" => 200
    ),
    "perimetre_brachial"    => array(
      "type" => "physio",
      "unit" => "cm",
      "min" => 0, "max" => 300
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
    'perimetre_taille' => array(
      'type' => 'physio',
      'unit' => 'cm',
      'min' => 20, 'max' => 300
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
    "douleur_evs" => array(
      "type" => "physio",
      "unit" => "",
      "min" => 0, "max" => 4,
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
    "redon_5"           => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "redon_cumul_reset_hour",
    ),
    "redon_6"           => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "redon_cumul_reset_hour",
    ),
    "redon_7"           => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "redon_cumul_reset_hour",
    ),
    "redon_8"           => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "redon_cumul_reset_hour",
    ),
    "redon_accordeon_1"   => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 50,
      "cumul_reset_config" => "redon_accordeon_cumul_reset_hour",
    ),
    "redon_accordeon_2"   => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 50,
      "cumul_reset_config" => "redon_accordeon_cumul_reset_hour",
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
    "drain_thoracique_3" => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "drain_thoracique_cumul_reset_hour",
    ),
    "drain_thoracique_4" => array(
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
    "drain_dve"     => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 500,
      "cumul_reset_config" => "drain_dve_cumul_reset_hour",
    ),
    "drain_kher"    => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 1000,
      "cumul_reset_config" => "drain_kher_cumul_reset_hour",
    ),
    "drain_crins"   => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "drain_crins_cumul_reset_hour",
    ),
    "drain_sinus"   => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "drain_sinus_cumul_reset_hour",
    ),
    "drain_orifice_1" => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 1000,
      "cumul_reset_config" => "drain_orifice_cumul_reset_hour",
    ),
    "drain_orifice_2" => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 1000,
      "cumul_reset_config" => "drain_orifice_cumul_reset_hour",
    ),
    "drain_orifice_3" => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 1000,
      "cumul_reset_config" => "drain_orifice_cumul_reset_hour",
    ),
    "drain_orifice_4" => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 1000,
      "cumul_reset_config" => "drain_orifice_cumul_reset_hour",
    ),
    "drain_ileostomie" => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 3000,
      "cumul_reset_config" => "drain_ileostomie_cumul_reset_hour",
    ),
    "drain_colostomie" => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 3000,
      "cumul_reset_config" => "drain_colostomie_cumul_reset_hour",
    ),
    "drain_gastrostomie" => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 3000,
      "cumul_reset_config" => "drain_gastrostomie_cumul_reset_hour",
    ),
    "drain_jejunostomie" => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 3000,
      "cumul_reset_config" => "drain_jejunostomie_cumul_reset_hour",
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
    "sonde_rectale"   => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 3000,
      "cumul_reset_config" => "sonde_rectale_cumul_reset_hour",
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
    "ph_sanguin" => array(
      "type" => "biolo",
      "unit" => "",
      "min" => 5, "max" => 10,
    ),
    "lactates" => array(
      "type" => "biolo",
      "unit" => "mmol/L",
      "min" => 0, "max" => 20,
    ),
    "glasgow" => array(
      "type" => "physio",
      "unit" => "",
      "min" => 3, "max" => 15,
    ),
    'hemo_glycquee' => array(
      'type' => 'biolo',
      'unit' => '%',
      'min'  => 0, 'max' => 50,
    ),
    'clair_creatinine' => array(
      'type' => 'biolo',
      'unit' => 'ml/min',
      'min' => 0,
      'max' => 250,
    ),
    'plaquettes' => array(
      'type' => 'biolo',
      'unit' => 'g/l',
      'min' => 0,
      'max' => 1000,
    ),
    'triglycerides' => array(
      'type' => 'biolo',
      'unit' => 'g/l',
      'min' => 0,
      'max' => 4,
    ),
    'ldlc' => array(
      'type' => 'biolo',
      'unit' => 'g/l',
      'min' => 0,
      'max' => 4,
    ),
    'hdlc' => array(
      'type' => 'biolo',
      'unit' => 'g/l',
      'min' => 0,
      'max' => 2,
    ),
    'potassium' => array(
      'type' => 'biolo',
      'unit' => 'mmol/l',
      'min' => 0,
      'max' => 50,
    ),
    'sodium' => array(
      'type' => 'biolo',
      'unit' => 'mmol/l',
      'min' => 0,
      'max' => 500,
    ),
    'cpk' => array(
      'type' => 'biolo',
      'unit' => 'ui/l',
      'min' => 0,
      'max' => 500,
    ),
    'asat' => array(
      'type' => 'biolo',
      'unit' => 'ui/l',
      'min' => 0,
      'max' => 100,
    ),
    'alat' => array(
      'type' => 'biolo',
      'unit' => 'ui/l',
      'min' => 0,
      'max' => 100,
    ),
    'gammagt' => array(
      'type' => 'biolo',
      'unit' => 'ui/l',
      'min' => 0,
      'max' => 100,
    ),
    'ipsc' => array(
      'type' => 'physio',
      'min' => 0,
      'max' => 1.5,
      "unit" => "mmHg",
    ),
    'broadman' => array(
      'type' => 'physio',
      'unit' => '',
      'min' => 0,
      'max' => 10,
    ),
    'tonus_d' => array(
      'type' => 'physio',
      'unit' => '',
      'min' => 0, 'max' => 2
    ),
    'tonus_g' => array(
      'type' => 'physio',
      'unit' => '',
      'min' => 0, 'max' => 2
    ),
    'motricite_d' => array(
      'type' => 'physio',
      'unit' => '',
      'min' => 0, 'max' => 2
    ),
    'motricite_g' => array(
      'type' => 'physio',
      'unit' => '',
      'min' => 0, 'max' => 2
    ),
    'motricite_inf_d' => array(
      'type' => 'physio',
      'unit' => '',
      'min' => 0, 'max' => 2
    ),
    'motricite_inf_g' => array(
      'type' => 'physio',
      'unit' => '',
      'min' => 0, 'max' => 2
    ),
    '_urine_effective' => array(
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 1000,
      "plot" => true,
      "color" => "#00A8F0",
      "cumul_reset_config" => "urine_effective_24_reset_hour",
      "formula" => array(
        "sonde_vesicale"     => "+",
        "entree_lavage"      => "-",
      )
    ),
    'echelle_confort' => array(
      'type' => 'physio',
      'unit' => '',
      'min' => 0, 'max' => 10
    ),
    'pres_artere_invasive' => array(
      'type' => 'physio',
      'unit' => 'mmHg',
      'min' => 20, 'max' => 200
    ),
    'capnometrie' => array(
      'type' => 'biolo',
      'unit' => '%',
      'min' => 0, 'max' => 100
    ),
    'sortie_lavage' => array(
      'type' => 'drain',
      'unit' => 'ml',
      'min' => 0, 'max' => 200,
    ),
    'coloration' => array(
      'type' => 'physio',
      'unit' => '%',
      'min' => 0, 'max' => 4
    ),
    'perimetre_taille' => array(
      'type' => 'physio',
      'unit' => 'cm',
      'min'  => 30, 'max' => 60
    )
  );

  /** @var bool Used for making the params conversion (min, max and standard) only once */
  static $unit_conversion = false;

  static $list_constantes_type = array(
    "physio" => array(),
    "drain" => array(),
    "biolo" => array(),
  );

  /**
   * Constructeur de la classe, créé dynamiquement tous les champs
   */
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

    $group = CGroups::loadCurrent();

    foreach (self::$list_constantes as $_params) {
      if (empty($_params["unit_config"])) {
        continue;
      }

      $unit_config = $_params["unit_config"];
      $unit = CAppUI::conf("dPpatients CConstantesMedicales $unit_config", $group);

      if ($unit == $_params["orig_unit"]) {
        continue;
      }

      if (isset($_params["formfields"]) && isset($_params["conversion"])) {
        $conv = $_params["conversion"][$unit];

        $func_min = create_function('$matches', "return 'min|'.\$matches[1]*$conv;");
        $func_max = create_function('$matches', "return 'max|'.\$matches[1]*$conv;");

        foreach ($_params["formfields"] as $_formfield) {
          $spec = $this->_specs[$_formfield];
          $spec->prop = preg_replace_callback("/min\|([0-9]+)/", $func_min, $spec->prop);
          $spec->prop = preg_replace_callback("/max\|([0-9]+)/", $func_max, $spec->prop);

          if (isset($spec->min)) {
            $spec->min *= $conv;
          }
          if (isset($spec->max)) {
            $spec->max *= $conv;
          }
        }
      }
      else {
        trigger_error("Un champ avec conversion d'unité doit avoir au moins un 'formfield'");
      }
    }

    self::$_specs_converted = true;
  }

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'constantes_medicales';
    $spec->key   = 'constantes_medicales_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props['user_id']                = 'ref class|CMediusers';
    $props['creation_date']          = 'dateTime';
    $props['patient_id']             = 'ref notNull class|CPatient';
    $props['datetime']               = 'dateTime notNull';
    $props['context_class']          = 'enum list|CConsultation|CSejour|CPatient';
    $props['context_id']             = 'ref class|CMbObject meta|context_class cascade';
    $props['comment']                = 'text';

    $props['poids']                  = 'float pos max|500';
    $props['_poids_g']               = 'num pos min|300 max|500000';
    $props['taille']                 = 'float pos min|20 max|300';

    $props['ta']                     = 'str maxLength|10';
    $props['_ta_systole']            = 'num pos max|50';
    $props['_ta_diastole']           = 'num pos max|50';

    $props['_tam']                   = 'str maxLength|10';

    $props['ta_gauche']              = 'str maxLength|10';
    $props['_ta_gauche_systole']     = 'num pos max|50';
    $props['_ta_gauche_diastole']    = 'num pos max|50';

    $props['ta_droit']               = 'str maxLength|10';
    $props['_ta_droit_systole']      = 'num pos max|50';
    $props['_ta_droit_diastole']     = 'num pos max|50';

    $props['ta_couche']              = 'str maxLength|10';
    $props['_ta_couche_systole']     = 'num pos max|50';
    $props['_ta_couche_diastole']    = 'num pos max|50';

    $props['ta_assis']               = 'str maxLength|10';
    $props['_ta_assis_systole']      = 'num pos max|50';
    $props['_ta_assis_diastole']     = 'num pos max|50';

    $props['ta_debout']              = 'str maxLength|10';
    $props['_ta_debout_systole']     = 'num pos max|50';
    $props['_ta_debout_diastole']    = 'num pos max|50';

    $props['_unite_ta']              = 'enum list|cmHg|mmHg';

    $props['pouls']                  = 'num pos';
    $props['spo2']                   = 'float min|0 max|100';
    $props['temperature']            = 'float min|20 max|50'; // Au cas ou il y aurait des malades très malades
    $props['score_sensibilite']      = 'float min|0 max|5';
    $props['sens_membre_inf_d']      = 'float min|0 max|5';
    $props['sens_membre_inf_g']      = 'float min|0 max|5';
    $props['score_motricite']        = 'float min|0 max|5';
    $props['EVA']                    = 'float min|0 max|10';
    $props['score_sedation']         = 'float';
    $props['frequence_respiratoire'] = 'float pos';
    $props['contraction_uterine']    = 'float min|0 max|10';
    $props['bruit_foetal']           = 'float min|0 max|220';

    $props['glycemie']               = 'float min|0 max|10';
    $props['_glycemie']              = $props['glycemie'];
    $props['_unite_glycemie']        = 'enum list|g/l|mmol/l';

    $props['cetonemie']              = 'float min|0 max|10';
    $props['_cetonemie']             = $props['cetonemie'];
    $props['_unite_cetonemie']       = 'enum list|g/l|mmol/l';

    $props['hemoglobine_rapide']     = 'float';
    $props['_hemoglobine_rapide']    = 'float';
    $props['_unite_hemoglobine']     = 'enum list|g/dl|g/l';

    $props['PVC']                    = 'float min|0';
    $props['perimetre_abdo']         = 'float min|0';
    $props['perimetre_hanches']      = 'float min|0';
    $props['perimetre_brachial']     = 'float min|0';
    $props['perimetre_cranien']      = 'float min|0';
    $props['perimetre_cuisse']       = 'float min|0';
    $props['perimetre_cou']          = 'float min|0';
    $props['perimetre_thoracique']   = 'float min|0';
    $props['perimetre_taille']       = 'num min|0';
    $props['hauteur_uterine']        = 'float min|0';
    $props['peak_flow']              = 'float min|0';
    $props['_imc']                   = 'float pos';
    $props['_poids_ideal']           = 'float pos';
    $props['_vst']                   = 'float pos';

    $props['injection']              = 'str maxLength|10';
    $props['_inj']                   = 'num pos';
    $props['_inj_essai']             = 'num pos moreEquals|_inj';

    $props['gaz']                    = 'num min|0';
    $props['selles']                 = 'num min|0';

    // Douleur
    $props['douleur_en']             = 'float min|0 max|10';
    $props['douleur_doloplus']       = 'num min|0 max|30';
    $props['douleur_algoplus']       = 'num min|0 max|5';
    $props['douleur_evs']            = 'num min|0 max|4';
    $props['ecpa_avant']             = 'num min|0 max|16';
    $props['ecpa_apres']             = 'num min|0 max|16';
    $props['_ecpa_total']            = 'num min|0 max|32';

    // Vision
    $props['vision_oeil_droit']      = 'num min|0 max|10';
    $props['vision_oeil_gauche']     = 'num min|0 max|10';

    $props['redon']                  = 'float min|0';
    $props['redon_2']                = 'float min|0';
    $props['redon_3']                = 'float min|0';
    $props['redon_4']                = 'float min|0';
    $props['redon_5']                = 'float min|0';
    $props['redon_6']                = 'float min|0';
    $props['redon_7']                = 'float min|0';
    $props['redon_8']                = 'float min|0';
    $props['redon_accordeon_1']      = 'float min|0';
    $props['redon_accordeon_2']      = 'float min|0';
    $props['diurese']                = 'float min|0'; // Miction naturelle
    $props['_diurese']               = 'float min|0'; // Vraie diurèse (calculée)
    $props['sng']                    = 'float min|0';
    $props['lame_1']                 = 'float min|0';
    $props['lame_2']                 = 'float min|0';
    $props['lame_3']                 = 'float min|0';
    $props['drain_1']                = 'float min|0';
    $props['drain_2']                = 'float min|0';
    $props['drain_3']                = 'float min|0';
    $props['drain_thoracique_1']     = 'float min|0';
    $props['drain_thoracique_2']     = 'float min|0';
    $props['drain_thoracique_3']     = 'float min|0';
    $props['drain_thoracique_4']     = 'float min|0';
    $props['drain_thoracique_flow']  = 'float min|0';
    $props['drain_pleural_1']        = 'float min|0';
    $props['drain_pleural_2']        = 'float min|0';
    $props['drain_mediastinal']      = 'float min|0';
    $props['drain_shirley']          = 'float min|0';
    $props['drain_dve']              = 'float min|0';
    $props['drain_kher']             = 'float min|0';
    $props['drain_crins']            = 'float min|0';
    $props['drain_sinus']            = 'float min|0';
    $props['drain_orifice_1']        = 'float min|0';
    $props['drain_orifice_2']        = 'float min|0';
    $props['drain_orifice_3']        = 'float min|0';
    $props['drain_orifice_4']        = 'float min|0';
    $props['drain_ileostomie']       = 'float min|0';
    $props['drain_colostomie']       = 'float min|0';
    $props['drain_gastrostomie']     = 'float min|0';
    $props['drain_jejunostomie']     = 'float min|0';
    $props['sonde_ureterale_1']      = 'float min|0';
    $props['sonde_ureterale_2']      = 'float min|0';
    $props['sonde_nephro_1']         = 'float min|0';
    $props['sonde_nephro_2']         = 'float min|0';
    $props['sonde_vesicale']         = 'float min|0';
    $props['sonde_rectale']          = 'float min|0';
    $props['catheter_suspubien']     = 'float min|0';
    $props['bricker']                = 'float min|0';
    $props['entree_lavage']          = 'float min|0';
    $props['creatininemie']          = 'float min|0';
    $props['ph_sanguin']             = 'float min|0';
    $props['lactates']               = 'float min|0';
    $props['glasgow']                = 'float min|0';
    $props['hemo_glycquee']          = 'float min|0';
    if (CAppUi::conf('ref_pays') == 2) {
      $props['saturation_air']         = 'float min|0';
    }
    $props['clair_creatinine']       = 'float min|0';
    $props['plaquettes']             = 'num min|0';
    $props['triglycerides']          = 'float min|0';
    $props['ldlc']                   = 'float min|0';
    $props['hdlc']                   = 'float min|0';
    $props['potassium']              = 'float min|0';
    $props['sodium']                 = 'float min|0';
    $props['cpk']                    = 'num min|0';
    $props['asat']                   = 'num min|0';
    $props['alat']                   = 'num min|0';
    $props['gammagt']                = 'num min|0';
    $props['ipsc']                   = 'float min|0';
    $props['broadman']               = 'num min|0';
    $props['tonus_d']                = 'float min|0 max|2';
    $props['tonus_g']                = 'float min|0 max|2';
    $props['motricite_d']            = 'float min|0 max|2';
    $props['motricite_g']            = 'float min|0 max|2';
    $props['motricite_inf_d']        = 'float min|0 max|2';
    $props['motricite_inf_g']        = 'float min|0 max|2';
    $props['_urine_effective']       = 'float min|0';
    $props['echelle_confort']        = 'num min|0 max|10';
    $props['pres_artere_invasive']   = 'num min|20 max|200';
    $props['capnometrie']            = 'num min|0 max|100';
    $props['sortie_lavage']          = 'num min|20 max|200';
    $props['coloration']             = 'num min|0 max|4';
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["administrations"]   = "CAdministration constantes_medicales_id";
    return $backProps;
  }

  /**
   * Get conversion ratio for a constant
   *
   * @param string $field Constante name
   * @param string $unit  Constante target unit
   *
   * @return float
   */
  static function getConv($field, $unit) {
    $conv = 1.0;
    $params = self::$list_constantes[$field];

    if ($unit) {
      if ($unit != $params["orig_unit"]) {
        $conv = $params["conversion"][$unit];
      }
    }

    return $conv;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    static $unite_config = array();

    parent::updateFormFields();

    $this->loadRefPatient();

    $this->getIMC();

    // Calcul du poids en grammes
    if ($this->poids) {
      $this->_poids_g = $this->poids * 1000;
    }

    // Afficher le champ diurèse dans le formulaire si une des valeurs n'est pas vide
    // FIXME Utiliser "cumul_in"
    foreach (self::$list_constantes["_diurese"]["formula"] as $_field => $_sign) {
      if ($this->{$_field} && $this->_diurese == null) {
        $this->_diurese = " ";
        break;
      }
    }

    // Afficher le champ urine effective dans le formulaire si une des valeurs n'est pas vide
    foreach (self::$list_constantes["_urine_effective"]["formula"] as $_field => $_sign) {
      if ($this->{$_field} && $this->_urine_effective == null) {
        $this->_urine_effective = " ";
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

    // Calcul du poids ideal
    if ($this->taille) {
      $factor = 4;
      if ($this->_ref_patient->sexe == 'f') {
        $factor = 2;
      }
      $this->_poids_ideal = $this->taille - 100 - (($this->taille - 150) / $factor);
    }

    // Calcul du Volume Sanguin Total
    if ($this->poids) {
      $this->_vst = (($this->_ref_patient->sexe != 'm') ? 65 : 70) * $this->poids;
    }

    // Calcul de la Tension Artérielle Moyenne
    if ($this->ta) {
      list($_ta_systole, $_ta_diastole) = explode('|', $this->ta);
      $this->_tam = round(($_ta_systole + (2 * $_ta_diastole)) / 3, 2);
    }

    if (empty($unite_config)) {
      $group = CGroups::loadCurrent();

      $unite_config["unite_ta"]          = CAppUI::conf('dPpatients CConstantesMedicales unite_ta',          $group);
      $unite_config["unite_glycemie"]    = CAppUI::conf('dPpatients CConstantesMedicales unite_glycemie',    $group);
      $unite_config["unite_cetonemie"]   = CAppUI::conf('dPpatients CConstantesMedicales unite_cetonemie',   $group);
      $unite_config["unite_hemoglobine"] = CAppUI::conf('dPpatients CConstantesMedicales unite_hemoglobine', $group);
    }

    $this->_unite_ta          = $unite_config["unite_ta"];
    $this->_unite_glycemie    = $unite_config["unite_glycemie"];
    $this->_unite_cetonemie   = $unite_config["unite_cetonemie"];
    $this->_unite_hemoglobine = $unite_config["unite_hemoglobine"];

    foreach (self::$list_constantes as $_constant => &$_params) {
      // Conversion des unités
      if (isset($_params["unit_config"]) && !self::$unit_conversion) {
        $_unit_config = '_' . $_params["unit_config"];
        $unit = $this->$_unit_config;

        $_params_ref = &CConstantesMedicales::$list_constantes[$_constant];
        //$_params_ref["orig_unit"] = $_params_ref["unit"];
        if ($unit != $_params_ref["orig_unit"]) {
          $conv = $_params["conversion"][$unit];

          $_params_ref["unit"]      = $unit;
          $_params_ref["min"]      *= $conv;
          $_params_ref["max"]      *= $conv;

          if (isset($_params_ref["norm_min"])) {
            $_params_ref["norm_min"] *= $conv;
          }
          if (isset($_params_ref["norm_max"])) {
            $_params_ref["norm_max"] *= $conv;
          }
        }
      }

      if ($this->$_constant === null || $this->$_constant === "") {
        continue;
      }

      if (isset($_params["formfields"])) {
        $conv = 1.0;
        if (isset($_params["unit_config"])) {
          $form_field_unite = '_' . $_params["unit_config"];
          $conv = self::getConv($_constant, $this->$form_field_unite);
        }

        $_parts = explode("|", $this->$_constant);

        foreach ($_params["formfields"] as $_i => $_formfield) {
          $this->$_formfield = $_parts[$_i];

          if ($conv != 1.0) {
            $this->$_formfield = round($this->$_formfield * $conv, self::CONV_ROUND_DOWN);
          }
        }
      }
    }
    /* Permet d'éviter que les conversions des min et max soit effectuée plusieurs fois */
    self::$unit_conversion = true;

    // Calcul de l'ECPA total
    $this->_ecpa_total = null;
    if ($this->ecpa_avant !== null) {
      $this->_ecpa_total += $this->ecpa_avant;
    }
    if ($this->ecpa_apres !== null) {
      $this->_ecpa_total += $this->ecpa_apres;
    }
  }

  /**
   * @see parent::updatePlainFields()
   */
  function updatePlainFields() {
    $group = CGroups::loadCurrent();

    if ($this->_poids_g) {
      $this->poids = round($this->_poids_g / 1000, 2);
    }

    foreach (self::$list_constantes as $_constant => &$_params) {
      // If field is a
      if ($this->$_constant === null && empty($_params["formfields"])) {
        continue;
      }

      if (isset($_params["formfields"])) {
        $conv = 1.0;
        if (isset($_params['conversion'])) {
          $form_field_unite = '_' . $_params["unit_config"];
          $_unite = $this->$form_field_unite;

          // Si le champ n'a pas de valeur, on regarde en config
          if (!$_unite) {
            $_unite = CAppUI::conf('dPpatients CConstantesMedicales '.$_params["unit_config"], $group);
          }

          $conv = self::getConv($_constant, $_unite);
        }

        $_parts = array();
        $_empty = true;

        foreach ($_params["formfields"] as $_formfield) {
          if (empty($this->$_formfield) && !is_numeric($this->$_formfield)) {
            break;
          }

          $_empty = false;

          $_value = $this->$_formfield;
          $_value = CMbFieldSpec::checkNumeric($_value, false);

          if ($conv != 1.0) {
            $_value = round($_value / $conv, self::CONV_ROUND_UP);
          }

          $_parts[] = $_value;
        }

        // no value at all
        if ($_empty) {
          $this->$_constant = "";
        }

        // not enough values
        elseif (count($_parts) != count($_params["formfields"])) {
          $this->$_constant = "";
        }

        // it's ok
        else {
          $this->$_constant = implode("|", $_parts);
        }
      }
    }

    parent::updatePlainFields();
  }

  /**
   * Load context
   *
   * @return CConsultation|CSejour|CPatient
   */
  function loadRefContext() {
    if ($this->context_class && $this->context_id) {
      $this->_ref_context = new $this->context_class;
      $this->_ref_context = $this->_ref_context->getCached($this->context_id);
    }
  }

  /**
   * Get patient
   *
   * @return CPatient
   */
  function loadRefPatient() {
    $this->_ref_patient = new CPatient;
    $this->_ref_patient = $this->_ref_patient->getCached($this->patient_id);
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    $this->loadRefContext();
    $this->loadRefPatient();
  }

  /**
   * Charge l'utilisateur qui a enregistré la première fois la constante
   *
   * @return CMediusers
   */
  function loadRefUser() {
    if (!$this->user_id) {
      $first_log = $this->loadFirstLog();
      $this->_ref_user = $first_log->loadRefUser();
      $this->_ref_user->loadRefMediuser()->loadRefFunction();
      $this->user_id = $this->_ref_user->_id;
      $this->store();
      return $this->_ref_user = $this->_ref_user->_ref_mediuser;
    }
    $this->_ref_user = $this->loadFwdRef("user_id", true);
    $this->_ref_user->loadRefFunction();
    return $this->_ref_user;
  }

  function getCreationDate() {
    if (!$this->creation_date) {
      $log = $this->loadCreationLog();
      $this->creation_date = $log->date;
      $this->store();
    }

    return $this->creation_date;
  }

  /**
   * @see parent::check()
   */
  function check() {
    if ($msg = parent::check()) {
      return $msg;
    }

    /* Check if the object has been properly loaded,
     to avoid blocking the merges of a context (CSejour, COperation, CPatient, CConsultation) */
    if ($this->_id && $this->datetime) {
      // Verifie si au moins une des valeurs est remplie
      $ok = false;
      foreach (CConstantesMedicales::$list_constantes as $const => $params) {
        if ($const[0] == '_') {
          continue;
        }

        $this->completeField($const);
        if (array_key_exists('formfields', $params)) {
          foreach ($params['formfields'] as $_formfield) {
            if ($this->$_formfield !== "" && $this->$_formfield !== null) {
              $ok = true;
              break 2;
            }
          }
        }
        elseif ($this->$const !== "" && $this->$const !== null) {
          $ok = true;
          break;
        }
      }
      if (!$ok) {
        return CAppUI::tr("CConstantesMedicales-min_one_constant");
      }
    }

    return null;
  }

  /**
   * @see parent::store()
   */
  function store() {
    // S'il ne reste plus qu'un seul champ et que sa valeur est passée à vide,
    // alors on supprime la constante.
    if (
        $this->_id && ($this->fieldModified("taille") || $this->fieldModified("poids")) ||
        !$this->_id && ($this->taille || $this->poids)
    ) {
      $this->completeField("patient_id");
      DSHM::remKeys("alertes-*-CPatient-".$this->patient_id);
    }

    if ($this->_id) {
      $this->loadOldObject();

      /* Pour permettre la suppression du poids et de _poids_g */
      if (!$this->_poids_g && $this->_old->poids) {
        $this->poids = $this->_poids_g;
      }
      if (!$this->poids && $this->_old->poids) {
        $this->_poids_g = $this->poids;
      }
      if ($this->poids != $this->_old->poids && $this->_poids_g != $this->poids * 1000) {
        $this->_poids_g = $this->poids * 1000;
      }
    }

    if (!$this->_id) {
      if (!$this->user_id) {
        $this->user_id = CMediusers::get()->_id;
      }

      if (!$this->creation_date) {
        $this->creation_date = CMbDT::dateTime();
      }

      if ($this->datetime == 'now') {
        $this->datetime = $this->creation_date;
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

  /**
   * @see parent::delete()
   */
  function delete() {
    $this->completeField("taille", "poids", "patient_id");
    if ($this->taille || $this->poids) {
      DSHM::remKeys("alertes-*-CPatient-".$this->patient_id);
    }
    return parent::delete();
  }

  /**
   * Apply the formula of the given constant and return the result
   *
   * @param string $constant_name The name of the constant
   *
   * @return float|integer
   */
  function applyFormula($constant_name) {
    $value = null;
    foreach (CConstantesMedicales::$list_constantes[$constant_name]['formula'] as $_field => $_sign) {
      if (!is_null($this->$_field) && $this->$_field !== "") {
        if ($_sign === '+') {
          $value += $this->$_field;
        }
        else {
          $value -= $this->$_field;
        }
      }
    }

    return $value;
  }

  /**
   * Récupération des constantes ayant une valeur
   *
   * @return array
   */
  function getValuedConstantes() {
    $this->_valued_cst = array();
    foreach(self::$list_constantes as $name => $cst) {
      if($this->$name !== null) {
        $this->_valued_cst[$name] = array(
          "value"       => $this->$name,
          "description" => $cst
        );
      }
    }
  }

  /**
   * Compute the IMC
   *
   * @return void
   */
  function getIMC() {
    $this->setComputedConstantsCompounds();

    $poids = null;
    $taille = null;
    if ($this->poids && $this->taille) {
      $poids = $this->poids;
      $taille = $this->taille;
    }
    elseif (($this->poids && !$this->taille) || (!$this->poids && $this->taille)) {
      if (!$this->poids) {
        $taille = $this->taille;
        $poids = $this->getComputedConstantCompound('poids');
      }
      elseif (!$this->taille) {
        $poids = $this->poids;
        $taille = $this->getComputedConstantCompound('taille');
      }
    }
    if ($poids && $taille) {
      $this->_imc = round($poids / ($taille * $taille * 0.0001), 2);
    }
  }

  /**
   * Set the compounds for the computed constants, who needs to be set even if there is only one compound valued
   * For now, only the compounds of the IMC are added
   *
   * @return void
   */
  function setComputedConstantsCompounds() {
    if ($this->_id) {
      $compounds = array('taille', 'poids');
      if (!array_key_exists($this->patient_id, self::$_computed_constants_compounds)) {
        self::$_computed_constants_compounds[$this->patient_id] = array();
        foreach ($compounds as $_compound) {
          self::$_computed_constants_compounds[$this->patient_id][$_compound] = array();
        }
      }

      /* Recuperation de la valeur dans latest values */
      if (array_key_exists($this->patient_id, self::$_latest_values) && array_key_exists('', self::$_latest_values[$this->patient_id])) {
        foreach ($compounds as $_compound) {
          if (isset(self::$_latest_values[$this->patient_id][''][0]->$_compound)) {
            $datetime = self::$_latest_values[$this->patient_id][''][1][$_compound];
            $value = self::$_latest_values[$this->patient_id][''][0]->$_compound;
            if (!array_key_exists($datetime, self::$_computed_constants_compounds[$this->patient_id][$_compound])) {
              self::$_computed_constants_compounds[$this->patient_id][$_compound][$datetime] = $value;
            }
          }
        }
      }

      foreach ($compounds as $_compound) {
        if (isset($this->$_compound)) {
          if (!array_key_exists($this->datetime, self::$_computed_constants_compounds[$this->patient_id][$_compound])) {
            self::$_computed_constants_compounds[$this->patient_id][$_compound][$this->datetime] = $this->$_compound;
          }
        }

        ksort(self::$_computed_constants_compounds[$this->patient_id][$_compound]);
      }
    }
  }

  /**
   * Return the value of the last constant set before the datetime of the current constant
   *
   * @param string $compound The compound name
   *
   * @return null|integer
   */
  function getComputedConstantCompound($compound) {
    $value = null;

    if (isset(self::$_computed_constants_compounds[$this->patient_id][$compound]))
      foreach (self::$_computed_constants_compounds[$this->patient_id][$compound] as $_datetime => $_value) {
        if ($_datetime >= $this->datetime) {
          break;
        }

        $value = $_value;
      }

    return $value;
  }

  /**
   * Get the latest constantes values
   *
   * @param int|CPatient $patient   The patient to load the constantes for
   * @param string       $datetime  The reference datetime
   * @param array        $selection A selection of constantes to load
   * @param CMbObject    $context   The context
   * @param boolean      $use_cache Force the function to return the latest_values is already set
   *
   * @return array The constantes values and dates
   */
  static function getLatestFor($patient, $datetime = null, $selection = array(), $context = null, $use_cache = true) {
    $patient_id = ($patient instanceof CPatient) ? $patient->_id : $patient;

    if (isset(self::$_latest_values[$patient_id][$datetime]) && $use_cache === true) {
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
    $constante->datetime = CMbDT::dateTime();
    $constante->loadRefPatient();

    $where = array(
      "patient_id" => "= '$patient_id'",
    );

    if ($context) {
      $where["context_class"] = " = '$context->_class'";
      $where["context_id"] = " = '$context->_id'";
    }

    if ($datetime) {
      $where["datetime"] = "<= '$datetime'";
    }

    if (count($selection)) {
      $ors = array();
      foreach($selection as $_item) {
        $ors[] = "$_item IS NOT NULL";
      }
      $where[] = implode(" OR ", $ors);
    }

    $count = $constante->countList($where);

    // Load all constants instead of checking every type to reduce number of SQL queries
    /** @var self[] $all_list */
    $all_list = array();
    if ($count <= 30) {
      $all_list = $constante->loadList($where, "datetime DESC");
    }

    $list_datetimes = array();
    foreach ($list_constantes as $type => $params) {
      $list_datetimes[$type] = null;

      if ($type[0] == "_") {
        continue;
      }

      // Load them, if any ...
      if ($count > 0) {
        // Load them all and dispatch
        if ($count <= 30) {
          foreach ($all_list as $_const) {
            $_value = $_const->$type;
            if ($_value != null) {
              $constante->$type = $_value;
              $list_datetimes[$type] = $_const->datetime;
              break;
            }
          }
        }

        // Or pick them one by one
        else {
          $_where = $where;
          $_where[$type] = "IS NOT NULL";
          $_list = $constante->loadList($_where, "datetime DESC", 1);
          if (count($_list)) {
            $_const = reset($_list);
            $constante->$type = $_const->$type;
            $list_datetimes[$type] = $_const->datetime;
          }
        }
      }
    }

    // Cumul de la diurese
    if ($datetime) {
      foreach ($list_constantes as $_name => $_params) {
        if (isset($_params["cumul_reset_config"]) || isset($_params["formula"])) {
          $day_defore = CMbDT::dateTime("-24 hours", $datetime);

          if (isset($_params["cumul_reset_config"]) && !isset($_params['formula'])) {
            $cumul_field = '_' . $_name . '_cumul';
            $reset_hour = str_pad(self::getHostConfig($_params["cumul_reset_config"], $context), 2, '0', STR_PAD_LEFT);
            $cumul_begin = '';
            $cumul_end = '';

            if ($datetime >= CMbDT::format($datetime, "%Y-%m-%d $reset_hour:00:00")) {
              $cumul_begin = CMbDT::format($datetime, "%Y-%m-%d $reset_hour:00:00");
              $cumul_end = CMbDT::format(CMbDT::date('+1 DAY', $datetime), "%Y-%m-%d $reset_hour:00:00");
            }
            else {
              $cumul_begin = CMbDT::format(CMbDT::date('-1 DAY', $datetime), "%Y-%m-%d $reset_hour:00:00");
              $cumul_end = CMbDT::format($datetime, "%Y-%m-%d $reset_hour:00:00");
            }

            $query = new CRequest();
            $query->addSelect("SUM(`$_name`)");
            $query->addTable('constantes_medicales');
            $query->addWhere(array("`datetime` >= '$cumul_begin'", "`datetime` <= '$cumul_end'", "`$_name` IS NOT NULL", "`patient_id` = $patient_id"));
            $ds = CSQLDataSource::get('std');
            $constante->$cumul_field = $ds->loadResult($query->makeSelect());
          }
          else {
            // cumul de plusieurs champs (avec formule)
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

  /**
   * Détermine la couleur à afficher en fonction des seuils d'alerte définis dans les paramètres
   *
   * @param float  $value         La valeur à vérifier
   * @param array  $params        Les paramètres concernés
   * @param string $default_color Couleur par défaut
   *
   * @return string
   */
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

  /**
   * Build constantes grid
   *
   * @param self[] $list            The list of CConstantesMedicales objects to build the grid from
   * @param bool   $full            Display the full list of constantes
   * @param bool   $only_with_value Only display not null values
   *
   * @return array
   */
  static function buildGrid($list, $full = true, $only_with_value = false) {
    $grid = array();
    $selection = array_keys(CConstantesMedicales::$list_constantes);
    $cumuls_day = array();
    $reset_hours = array();
    $cumul_names = array();

    if (!$full) {
      $conf_constantes = array_filter(CConstantesMedicales::getRanksFor());
      $selection = array_keys($conf_constantes);

      foreach ($list as $_constante_medicale) {
        foreach (CConstantesMedicales::$list_constantes as $_name => $_params) {
          if ($_constante_medicale->$_name != '' && !empty($_params["cumul_in"])) {
            $selection = array_merge($selection, $_params["cumul_in"]);
            $cumul_names = array_merge($selection, $_params["cumul_in"]);
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
        if (in_array($_name, $selection) || in_array($_name, $cumul_names) || $_constante_medicale->$_name != '') {
          $value = null;
          if (isset($_params["cumul_for"]) || isset($_params["formula"])) {
            // cumul
            if (!isset($reset_hours[$_name])) {
              $reset_hours[$_name] = self::getResetHour($_name);
            }
            $reset_hour = $reset_hours[$_name];

            $day_24h = CMbDT::transform("-$reset_hour hours", $_constante_medicale->datetime, '%y-%m-%d');

            if (!isset($cumuls_day[$_name][$day_24h])) {
              $cumuls_day[$_name][$day_24h] = array(
                "id"    => $_constante_medicale->_id,
                "datetime" => $_constante_medicale->datetime,
                "value" => null,
                "span"  => 0,
                "pair"  => (@count($cumuls_day[$_name]) % 2 ? "odd" : "even"),
                "day"   => CMbDT::transform($day_24h, null, "%a"),
              );
            }

            if (isset($_params["cumul_for"])) {
              // cumul simple sur le meme champ
              $cumul_for  = $_params["cumul_for"];

              if ($_constante_medicale->$cumul_for !== null) {
                $cumuls_day[$_name][$day_24h]["value"] += $_constante_medicale->$cumul_for;
              }
            }
            else {
              // cumul de plusieurs champs (avec formule)
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
          else {
            // valeur normale
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

  /**
   * Build constantes grid
   *
   * @param self  $constante       The CConstantesMedicales object containing the latest values
   * @param array $dates           An array containing the date of the
   * @param bool  $full            Display the full list of constantes
   * @param bool  $only_with_value Only display not null values
   *
   * @return array
   */
  static function buildGridLatest($constante, $dates, $full = true, $only_with_value = false) {
    $dates = CMbArray::flip($dates);
    if (array_key_exists('', $dates)) {
      unset($dates['']);
    }

    $grid = array();
    $selection = array_keys(CConstantesMedicales::$list_constantes);
    $cumuls_day = array();
    $reset_hours = array();
    $cumul_names = array();

    if (!$full) {
      $conf_constantes = array_filter(CConstantesMedicales::getRanksFor());
      $selection = array_keys($conf_constantes);

      foreach (CConstantesMedicales::$list_constantes as $_name => $_params) {
        if ($constante->$_name != '' && !empty($_params["cumul_in"])) {
          $selection = array_merge($selection, $_params["cumul_in"]);
          $cumul_names = array_merge($selection, $_params["cumul_in"]);
        }
      }

      $selection = array_unique($selection);
    }

    if ($only_with_value) {
      $selection = array();
    }

    $names = $selection;

    foreach ($dates as $_date => $_constants) {
      if (!isset($grid["$_date"])) {
        $grid["$_date"] = array(
          'comment' => '',
          "values"  => array()
        );
      }

      foreach ($_constants as $_name) {
        $_params = CConstantesMedicales::$list_constantes[$_name];
        if (in_array($_name, $selection) || in_array($_name, $cumul_names) || $constante->$_name != '') {
          $value = null;
          if (isset($_params["cumul_for"]) || isset($_params["formula"])) {
            // cumul
            if (!isset($reset_hours[$_name])) {
              $reset_hours[$_name] = self::getResetHour($_name);
            }
            $reset_hour = $reset_hours[$_name];

            $day_24h = CMbDT::transform("-$reset_hour hours", $_date, '%y-%m-%d');

            if (!isset($cumuls_day[$_name][$day_24h])) {
              $cumuls_day[$_name][$day_24h] = array(
                "id"    => $constante->_id,
                "datetime" => $_date,
                "value" => null,
                "span"  => 0,
                "pair"  => (@count($cumuls_day[$_name]) % 2 ? "odd" : "even"),
                "day"   => CMbDT::transform($day_24h, null, "%a"),
              );
            }

            if (isset($_params["cumul_for"])) {
              // cumul simple sur le meme champ
              $cumul_for  = $_params["cumul_for"];

              if ($constante->$cumul_for !== null) {
                $cumuls_day[$_name][$day_24h]["value"] += $constante->$cumul_for;
              }
            }
            else {
              // cumul de plusieurs champs (avec formule)
              $formula  = $_params["formula"];

              foreach ($formula as $_field => $_sign) {
                $_value = $constante->$_field;

                if ($constante->$_field !== null) {
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
          else {
            // valeur normale
            $spec = self::$list_constantes[$_name];
            $value = $constante->$_name;

            if (isset($spec["formfields"])) {
              $arr = array();
              foreach ($spec["formfields"] as $ff) {
                if ($constante->$ff != "") {
                  $arr[] = $constante->$ff;
                }
              }
              $value = implode(" / ", $arr);
            }
          }

          $grid["$_date"]["values"][$_name] = $value;

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

  /**
   * Sort constant names
   *
   * @param string[]  $names Constants to sort
   * @param CMbObject $host  Host object (to get the configuration from)
   *
   * @return array
   */
  static function sortConstNames($names, CMbObject $host = null) {
    $new_names = array();
    $constants = self::getConstantsByRank(false, $host);
    foreach ($constants["all"] as $_constants) {
      foreach ($_constants as $_constant) {
        if (in_array($_constant, $names)) {

          $new_names[] = $_constant;
          if (isset(self::$list_constantes[$_constant]["cumul_in"])) {
            $new_names = array_merge($new_names, self::$list_constantes[$_constant]["cumul_in"]);
          }
        }
      }
    }

    return array_unique($new_names);
  }

  /**
   * Get related constant values
   *
   * @param string[]  $selection Constants selection
   * @param CPatient  $patient   Related patient
   * @param CMbObject $context   Related context
   * @param null      $date_min  Minimal date
   * @param null      $date_max  Maximal date
   * @param null      $limit     Limit count
   *
   * @return self[]
   */
  static function getRelated(
      $selection, CPatient $patient, CMbObject $context = null, $date_min = null, $date_max = null, $limit = null
  ) {
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

  /**
   * Intialize params
   *
   * @return void
   */
  static function initParams() {
    if (CAppUi::conf('ref_pays') == 2) {
      self::$list_constantes['saturation_air'] = array('type' => 'physio', 'unit' => '%', 'min'  => 50, 'max' => 100);
    }
    // make a copy of the array as it will be modified
    $list_constantes = CConstantesMedicales::$list_constantes;

    foreach ($list_constantes as $_constant => &$_params) {
      self::$list_constantes_type[$_params["type"]][$_constant] = &$_params;

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
   * Get the config from a host
   *
   * @param string                                          $name The config name
   * @param CMbObject|CGroups|CService|CConsultation|string $host The host object
   *
   * @return mixed
   */
  static function getHostConfig($name, $host) {
    $host = self::guessHost($host);

    if (
        in_array($name, array('selection', 'show_cat_tabs', 'stacked_graphs')) &&
        ($host instanceof CFunctions || $host instanceof CBlocOperatoire)
    ) {
      if ($name === 'selection') {
        if ($host instanceof CFunctions) {
          $name = 'selection_cabinet';
        }
        elseif ($host instanceof CBlocOperatoire) {
          $name = 'selection_bloc';
        }
      }

      return CAppUI::conf("dPpatients CConstantesMedicales $name", $host);
    }

    $group_id = null;
    $service_id = null;
    $function_id = null;

    // Etablissement
    if ($host instanceof CGroups) {
      $group_id = $host->_id;
    }

    // Service
    if ($host instanceof CService) {
      $service_id = $host->_id;
      $group_id   = $host->group_id;
    }

    // Cabinet
    if ($host instanceof CFunctions) {
      $function_id = $host->_id;
      $group_id = $host->group_id;
    }

    return self::getConfig($name, $group_id, $service_id, $function_id);
  }

  /**
   * Find the host from a context object
   *
   * @param CMbObject|string $context The context (séjour, rpu, service, etablissement)
   *
   * @return CGroups|CService|CFunctions|string
   */
  static function guessHost($context) {
    if ($context === "global") {
      return "global";
    }

    // Etablissement, service ou cabinet (deja un HOST)
    if (
        $context instanceof CGroups ||
        $context instanceof CService ||
        $context instanceof CFunctions ||
        $context instanceof CBlocOperatoire
    ) {
      return $context;
    }

    // Séjour d'urgence
    if ($context instanceof CSejour && $context->type == "urg") {
      $rpu = $context->loadRefRPU();
      if ($rpu && $rpu->_id) {
        $context = $rpu;
      }
    }

    // Sejour
    if ($context instanceof CSejour) {
      $affectation = $context->loadRefCurrAffectation();
      if (!$affectation->_id) {
        $affectation = $context->loadRefFirstAffectation();
      }

      return $affectation->loadRefService();
    }

    // Urgences
    if ($context instanceof CRPU) {
      /** @var CService $service */
      $service = null;

      if ($context->box_id) {
        return $context->loadRefBox()->loadRefService();
      }

      $sejour = $context->loadRefSejour();
      $affectation = $sejour->loadRefCurrAffectation();
      if (!$affectation->_id) {
        $affectation = $sejour->loadRefFirstAffectation();
      }

      $service = $affectation->loadRefService();

      if ($service && $service->_id) {
        return $service;
      }

      // Recherche du premier service d'urgences actif
      $group_id = CGroups::loadCurrent()->_id;
      $where = array(
        "group_id"  => "= '$group_id'",
        "urgence"   => "= '1'",
        "cancelled" => "= '0'",
      );
      $service = new CService();
      $service->loadObject($where, "nom");

      return $service;
    }

    // Utiliser le contexte de la consultation dans la cas des dossiers d'anesth
    if ($context instanceof CConsultAnesth) {
      $context = $context->loadRefConsultation();
    }

    // Utiliser le contexte du cabinet dans le cas des consultations
    if ($context instanceof CConsultation) {
      return $context->loadRefPlageConsult()->loadRefChir()->loadRefFunction();
    }

    return CGroups::loadCurrent();
  }

  /**
   * Get service or group specific configuration value
   *
   * @param string $name        Configuration name
   * @param int    $group_id    Group ID
   * @param int    $service_id  Service ID
   * @param int    $function_id Function ID
   *
   * @return mixed
   */
  static function getConfig($name, $group_id = null, $service_id = null, $function_id = null) {
    if (!$service_id) {
      if (isset($_SESSION["soins"]["service_id"])) {
        $service_id = $_SESSION["soins"]["service_id"];
      }
      elseif (isset($_SESSION["ecap"]["service_id"])) {
        $service_id = $_SESSION["ecap"]["service_id"];
      }
    }

    $guid = "global";
    if ($service_id && is_numeric($service_id)) {
      $guid = "CService-$service_id";
    }
    elseif ($function_id && is_numeric($function_id)) {
      $guid = "CFunctions-$function_id";
    }
    elseif ($group_id && is_numeric($group_id)) {
      $guid = "CGroups-$group_id";
    }

    return CAppUI::conf("dPpatients CConstantesMedicales $name", $guid);
  }

  /**
   * Get the constants's ranks, for the graphs or the form
   *
   * @param string           $type graph or form
   * @param CMbObject|string $host Host from which we'll get the configuration
   *
   * @return array
   */
  static function getRanksFor($type = 'form', $host = null) {
    if ($host) {
      $configs = CConstantesMedicales::getHostConfig('selection', $host);
    }
    else {
      $configs = CConstantesMedicales::getConfig('selection');
    }

    $id = $type == 'graph' ? 1 : 0;
    foreach ($configs as $_constant => $_config) {
      $_config = explode('|', $_config);
      $configs[$_constant] = $_config[$id];
    }

    return $configs;
  }

  /**
   * Return the constants, ordered by rank
   *
   * @param string           $type           'form' or 'graph'
   * @param boolean          $order_by_types If false, the constants won't be ordered by types,
   *                                         even if the config show_cat_tabs is set to true
   * @param CMbObject|string $host           Host from which we'll get the configuration
   *
   * @return array
   */
  static function getConstantsByRank($type = 'form', $order_by_types = true, $host = null) {
    $selection = self::getRanksFor($type, $host);

    $list_constants = CConstantesMedicales::$list_constantes;

    // Keep only valid constant names
    $selection = array_intersect_key($selection, $list_constants);

    $selection = CMbArray::flip($selection);
    ksort($selection);

    $result = array();
    if ($order_by_types) {
      foreach ($selection as $_rank => $_constants) {
        foreach ($_constants as $_constant) {
          $_type = $list_constants[$_constant]["type"];

          if (!array_key_exists($_type, $result)) {
            $result["$_type"] = array();
          }
          if (!array_key_exists($_rank, $result["$_type"])) {
            $result["$_type"][$_rank] = array();
          }

          $result["$_type"][$_rank][] = $_constant;
        }
      }
    }
    else {
      $result["all"] = $selection;
    }

    foreach ($result as $_type => $_ranks) {
      if (array_key_exists(0, $result[$_type])) {
        $unselected_constants = $result[$_type][0];
        unset($result[$_type][0]);
        $result[$_type]["hidden"] = $unselected_constants;
      }

      if (array_key_exists(-1, $result[$_type])) {
        unset($result[$_type][-1]);
      }
    }

    return $result;
  }

  /**
   * Return the selected constants in an formatted array (see getConstantsByRank to see the format)
   *
   * @param array            $selection The constant you want to select
   * @param string           $type      'form' or 'graph'
   * @param CMbObject|string $host      Host from which we'll get the configuration
   *
   * @return array
   */
  static function selectConstants($selection, $type = 'form', $host = null) {
    if ($host) {
      $show_cat_tabs = CConstantesMedicales::getHostConfig("show_cat_tabs", $host);
    }
    else {
      $show_cat_tabs = CConstantesMedicales::getConfig("show_cat_tabs");
    }
    $constants_by_rank = self::getRanksFor($type, $host);
    $list_constants = CConstantesMedicales::$list_constantes;

      // Keep only valid constant names
    $constants_by_rank = array_intersect_key($constants_by_rank, $list_constants);

    $constants_by_rank = CMbArray::flip($constants_by_rank);
    ksort($constants_by_rank);

    $result = array();
    foreach ($constants_by_rank as $_rank => $_constants) {
      if ($_rank === -1) {
        continue;
      }
      foreach ($_constants as $_constant) {
        if (strpos($_constant, "_") === 0) {
          continue;
        }

        if ($show_cat_tabs) {
          $_type = $list_constants[$_constant]["type"];

          if (!array_key_exists($_type, $result)) {
            $result[$_type] = array();
          }

          if (!in_array($_constant, $selection)) {
            $rank = 'hidden';
          }
          elseif ($_rank == 0) {
            $rank = 1000;
          }
          else {
            $rank = $_rank;
          }
          if (!array_key_exists($rank, $result[$_type])) {
            $result[$_type][$rank] = array();
          }

          $result[$_type][$rank][] = $_constant;
        }
        else {
          if (!array_key_exists('all', $result)) {
            $result['all'] = array();
          }

          if (!in_array($_constant, $selection)) {
            $rank = 'hidden';
          }
          else {
            $rank = $_rank;
          }

          if (!array_key_exists($rank, $result['all'])) {
            $result['all'][$rank] = array();
          }

          $result['all'][$rank][] = $_constant;
        }
      }
    }
    if (!$show_cat_tabs) {
      ksort($result['all']);
    }
    else {
      foreach ($result as $cat => $constants) {
        if (is_array($result[$cat])) {
          ksort($result[$cat]);
        }
      }
    }

    return $result;
  }

  /**
   * Get reset hour
   *
   * @param string $name       Reset name
   * @param int    $group_id   Group ID
   * @param int    $service_id Service ID
   *
   * @return mixed
   */
  static function getResetHour($name, $group_id = null, $service_id = null) {
    $list = CConstantesMedicales::$list_constantes;

    if (!$group_id) {
      $group_id = CGroups::loadCurrent()->_id;
    }

    if (isset($list[$name]["cumul_reset_config"])) {
      $confname = $list[$name]["cumul_reset_config"];
    }
    else {
      $confname = $list[$list[$name]["cumul_for"]]["cumul_reset_config"];
    }

    return self::getConfig($confname, $group_id, $service_id);
  }
}

CConstantesMedicales::initParams();

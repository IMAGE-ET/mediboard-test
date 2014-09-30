<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

$selection = array(
  "poids"                   => "custom tpl|inc_config_constant components|form|graph|color|mode|min|max|norm_min|norm_max default|1|0|0066FF|float|2|2|0|0",
  "taille"                  => "custom tpl|inc_config_constant components|form|graph|color|mode|min|max|norm_min|norm_max default|2|0|0066FF|float|5|5|0|0",
  "pouls"                   => "custom tpl|inc_config_constant components|form|graph|color|mode|min|max|norm_min|norm_max default|3|1|FF0000|fixed|70|120|0|0",
  "temperature"             => "custom tpl|inc_config_constant components|form|graph|color|mode|min|max|norm_min|norm_max default|4|1|0066FF|fixed|36|40|37.5|0",
  "ta"                      => "custom tpl|inc_config_constant components|form|graph|color|mode|min|max|norm_min|norm_max default|5|1|000000|fixed|2|16|8|12",
  "EVA"                     => "custom tpl|inc_config_constant components|form|graph|color|mode|min|max|norm_min|norm_max default|6|1|FF00FF|fixed|0|10|0|0",
  "frequence_respiratoire"  => "custom tpl|inc_config_constant components|form|graph|color|mode|min|max|norm_min|norm_max default|7|1|009900|fixed|0|60|0|0"
);

foreach (CConstantesMedicales::$list_constantes as $_constante => $_params) {
  if (!isset($_params["cumul_for"]) && !in_array($_constante, array('poids', 'taille', 'pouls', 'temperature', 'ta', 'EVA', 'frequence_respiratoire'))) {
    $mode = 'fixed';
    $min = $_params['min'];
    $max = $_params['max'];
    $norm_min = 0;
    $norm_max = 0;
    if (strpos($_params['min'], '@') !== false || strpos($_params['max'], '@') !== false) {
      $mode = 'float';
      $min = str_replace('@-', '', $_params['min']);
      $max = str_replace('@+', '', $_params['max']);
    }
    if (array_key_exists('norm_min', $_params)) {
      $norm_min = $_params['norm_min'];
    }
    if (array_key_exists('norm_max', $_params)) {
      $norm_max = $_params['norm_max'];
    }
    $selection[$_constante] = "custom tpl|inc_config_constant components|form|graph|color|mode|min|max|norm_min|norm_max default|0|0|0066FF|$mode|$min|$max|$norm_min|$norm_max";
  }
}

CConfiguration::register(
  array(
    'CGroups' => array(
      'dPpatients' => array(
        'CPatient' => array(
          'mode_identito_vigilance'   => "enum list|light|medium|strict localize default|light",
          'nom_jeune_fille_mandatory' => "bool default|0",
          'allow_anonymous_patient'   => "bool default|0",
          'anonymous_naissance'       => "str default|1970-01-01",
          'anonymous_sexe'            => "enum list|m|f default|m",
          'manage_identity_status'    => "bool default|0",
          'auto_selected_patient'     => "bool default|0",
        ),
        'CConstantesMedicales' => array(
          'unite_ta'        => 'enum list|cmHg|mmHg default|' . CAppUI::conf('dPpatients CConstantesMedicales unite_ta'),
          'unite_glycemie'  => 'enum list|g/l|mmol/l default|' . CAppUI::conf('dPpatients CConstantesMedicales unite_glycemie'),
          'unite_cetonemie' => 'enum list|g/l|mmol/l default|' . CAppUI::conf('dPpatients CConstantesMedicales unite_cetonemie'),
        )
      )
    ),
    "CService CGroups.group_id" => array(
      "dPpatients" => array(
        "CConstantesMedicales" => array(
          "show_cat_tabs"                       => "bool default|0",
          "show_enable_all_button"              => "bool default|1",
          "constants_modif_timeout"             => "num min|0 max|48 default|12",
          "stacked_graphs"                      => "bool default|0",
          'graphs_display_mode'                 => 'custom tpl|inc_config_graphs_display_mode components|mode|time default|classic|8',
          "diuere_24_reset_hour"                => "num min|0 max|23 default|8",
          "redon_cumul_reset_hour"              => "num min|0 max|23 default|8",
          "redon_accordeon_cumul_reset_hour"    => "num min|0 max|23 default|8",
          "sng_cumul_reset_hour"                => "num min|0 max|23 default|8",
          "lame_cumul_reset_hour"               => "num min|0 max|23 default|8",
          "drain_cumul_reset_hour"              => "num min|0 max|23 default|8",
          "drain_thoracique_cumul_reset_hour"   => "num min|0 max|23 default|8",
          "drain_pleural_cumul_reset_hour"      => "num min|0 max|23 default|8",
          "drain_mediastinal_cumul_reset_hour"  => "num min|0 max|23 default|8",
          "drain_dve_cumul_reset_hour"          => "num min|0 max|23 default|8",
          "drain_kher_cumul_reset_hour"         => "num min|0 max|23 default|8",
          "drain_crins_cumul_reset_hour"        => "num min|0 max|23 default|8",
          "drain_sinus_cumul_reset_hour"        => "num min|0 max|23 default|8",
          "drain_orifice_cumul_reset_hour"      => "num min|0 max|23 default|8",
          "drain_ileostomie_cumul_reset_hour"   => "num min|0 max|23 default|8",
          "drain_colostomie_cumul_reset_hour"   => "num min|0 max|23 default|8",
          "drain_gastrostomie_cumul_reset_hour" => "num min|0 max|23 default|8",
          "drain_jejunostomie_cumul_reset_hour" => "num min|0 max|23 default|8",
          "sonde_ureterale_cumul_reset_hour"    => "num min|0 max|23 default|8",
          "sonde_nephro_cumul_reset_hour"       => "num min|0 max|23 default|8",
          "sonde_vesicale_cumul_reset_hour"     => "num min|0 max|23 default|8",
          "sonde_rectale_cumul_reset_hour"      => "num min|0 max|23 default|8",

          "selection" => $selection
        ),
      ),
    ),

    "CFunctions CGroups.group_id" => array(
      "dPpatients" => array(
        "CConstantesMedicales" => array(
          "show_cat_tabs"     => "bool default|0",
          "stacked_graphs"    => "bool default|0",
          "selection_cabinet" => $selection
        ),
      ),
    ),
  )
);

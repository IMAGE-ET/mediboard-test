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

$list = array();
foreach (CConstantesMedicales::$list_constantes as $_const => $_params) {
  if (!isset($_params["cumul_for"])) {
    $list[] = $_const;
  }
}

$selection = array(
  "poids"                   => "custom tpl|inc_config_constant components|form|graph|color default|1|0|0066FF",
  "taille"                  => "custom tpl|inc_config_constant components|form|graph|color default|2|0|0066FF",
  "pouls"                   => "custom tpl|inc_config_constant components|form|graph|color default|3|1|FF0000",
  "temperature"             => "custom tpl|inc_config_constant components|form|graph|color default|4|1|0066FF",
  "ta"                      => "custom tpl|inc_config_constant components|form|graph|color default|5|1|000000",
  "EVA"                     => "custom tpl|inc_config_constant components|form|graph|color default|6|1|FF00FF",
  "frequence_respiratoire"  => "custom tpl|inc_config_constant components|form|graph|color default|7|1|009900"
);

CMbArray::removeValue("poids",                  $list);
CMbArray::removeValue("taille",                 $list);
CMbArray::removeValue("pouls",                  $list);
CMbArray::removeValue("temperature",            $list);
CMbArray::removeValue("ta",                     $list);
CMbArray::removeValue("EVA",                    $list);
CMbArray::removeValue("frequence_respiratoire", $list);

foreach ($list as $_constante) {
  $selection[$_constante] = "custom tpl|inc_config_constant components|form|graph|color default|0|0|0066FF";
}

CConfiguration::register(
  array(
    "CService CGroups.group_id" => array(
      "dPpatients" => array(
        "CConstantesMedicales" => array(
          "show_cat_tabs"                      => "bool default|0",
          "show_enable_all_button"             => "bool default|1",
          "constants_modif_timeout"            => "num min|0 max|48 default|12",
          "stacked_graphs"                     => "bool default|0",
          "diuere_24_reset_hour"               => "num min|0 max|23 default|8",
          "redon_cumul_reset_hour"             => "num min|0 max|23 default|8",
          "redon_accordeon_cumul_reset_hour"   => "num min|0 max|23 default|8",
          "sng_cumul_reset_hour"               => "num min|0 max|23 default|8",
          "lame_cumul_reset_hour"              => "num min|0 max|23 default|8",
          "drain_cumul_reset_hour"             => "num min|0 max|23 default|8",
          "drain_thoracique_cumul_reset_hour"  => "num min|0 max|23 default|8",
          "drain_pleural_cumul_reset_hour"     => "num min|0 max|23 default|8",
          "drain_mediastinal_cumul_reset_hour" => "num min|0 max|23 default|8",
          "drain_dve_cumul_reset_hour"         => "num min|0 max|23 default|8",
          "drain_kher_cumul_reset_hour"        => "num min|0 max|23 default|8",
          "drain_crins_cumul_reset_hour"       => "num min|0 max|23 default|8",
          "drain_sinus_cumul_reset_hour"       => "num min|0 max|23 default|8",
          "drain_orifice_cumul_reset_hour"     => "num min|0 max|23 default|8",
          "drain_ileostomie_cumul_reset_hour"  => "num min|0 max|23 default|8",
          "drain_colostomie_cumul_reset_hour"  => "num min|0 max|23 default|8",
          "drain_gastrostomie_cumul_reset_hour" => "num min|0 max|23 default|8",
          "drain_jejunostomie_cumul_reset_hour" => "num min|0 max|23 default|8",
          "sonde_ureterale_cumul_reset_hour"   => "num min|0 max|23 default|8",
          "sonde_nephro_cumul_reset_hour"      => "num min|0 max|23 default|8",
          "sonde_vesicale_cumul_reset_hour"    => "num min|0 max|23 default|8",
          "sonde_rectale_cumul_reset_hour"     => "num min|0 max|23 default|8",

          "selection" => $selection
        ),
      ),
    ),

    "CFunctions CGroups.group_id" => array(
      "dPpatients" => array(
        "CConstantesMedicales" => array(
          "selection_cabinet" => $selection
        ),
      ),
    ),
  )
);

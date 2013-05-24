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

global $locales;

$list_all = CConstantesMedicales::$list_constantes;

foreach ($list_all as $name => $params) {
  $tr = CAppUI::tr("CConstantesMedicales-$name");
  $locales["config-dPpatients-CConstantesMedicales-selection-$name"]         = $tr;
  $locales["config-dPpatients-CConstantesMedicales-selection_cabinet-$name"] = $tr;
}

$list = array();
foreach ($list_all as $_const => $_params) {
  if (!isset($_params["cumul_for"])) {
    $list[] = $_const;
  }
}

$selection = array(
  "poids"       => "num min|0 default|1",
  "pouls"       => "num min|0 default|2",
  "ta_gauche"   => "num min|0 default|3",
  "temperature" => "num min|0 default|4",
);

CMbArray::removeValue("poids",       $list);
CMbArray::removeValue("pouls",       $list);
CMbArray::removeValue("ta_gauche",   $list);
CMbArray::removeValue("temperature", $list);

foreach ($list as $_constante) {
  $selection[$_constante] = "num min|0 default|0";
}

CConfiguration::register(
  array(
  "CService CGroups.group_id" => array(
    "dPpatients" => array(
      "CConstantesMedicales" => array(
        "show_cat_tabs"                      => "bool default|0",
        "show_enable_all_button"             => "bool default|1",
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
));

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
  $locales["config-dPpatients-CConstantesMedicales-important_constantes.$name"] = CAppUI::tr("CConstantesMedicales-$name");
}

$list = array();
foreach ($list_all as $_const => $_params) {
  if (!isset($_params["cumul_for"])) {
    $list[] = $_const;
  }
}

$list = implode("|", $list);

CConfiguration::register(array(
  "CService CGroups.group_id" => array(
    "dPpatients" => array(
      "CConstantesMedicales" => array(
        "show_cat_tabs"                      => "bool default|0",
        "show_enable_all_button"             => "bool default|1",

        "diuere_24_reset_hour"               => "num min|0 max|23 default|8",
        "redon_cumul_reset_hour"             => "num min|0 max|23 default|8",
        "sng_cumul_reset_hour"               => "num min|0 max|23 default|8",
        "lame_cumul_reset_hour"              => "num min|0 max|23 default|8",
        "drain_cumul_reset_hour"             => "num min|0 max|23 default|8",
        "drain_thoracique_cumul_reset_hour"  => "num min|0 max|23 default|8",
        "drain_pleural_cumul_reset_hour"     => "num min|0 max|23 default|8",
        "drain_mediastinal_cumul_reset_hour" => "num min|0 max|23 default|8",
        "sonde_ureterale_cumul_reset_hour"   => "num min|0 max|23 default|8",
        "sonde_nephro_cumul_reset_hour"      => "num min|0 max|23 default|8",
        "sonde_vesicale_cumul_reset_hour"    => "num min|0 max|23 default|8",

        "important_constantes"               => "set default|poids|pouls|ta_gauche|temperature list|$list",
      ),
    ),
  ),
));

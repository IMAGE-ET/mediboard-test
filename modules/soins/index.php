<?php
/**
 * $Id$
 *
 * @category Soins
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$module = CModule::getInstalled(basename(__DIR__));

if (CAppUI::pref("vue_sejours") == "standard") {
  $module->registerTab("vw_idx_sejour"        , TAB_READ);
}
else {
  $module->registerTab("vw_sejours"           , TAB_READ);
}

$current_group = CGroups::loadCurrent();

if (CModule::getActive('dPprescription')) {
  $module->registerTab("vw_pancarte_service"  , TAB_READ);
  $module->registerTab("vw_bilan_prescription", TAB_READ);
  $module->registerTab("vw_plan_soins_service", TAB_READ);
  if (CAppUI::conf("soins Other show_charge_soins", $current_group)) {
    $module->registerTab("vw_ressources_soins"  , TAB_READ);
  }
  //$module->registerTab("vw_dossier_sejour"    , TAB_READ);
}

if (
    CModule::getActive('pharmacie') &&
    CAppUI::conf("pharmacie enable_v2") &&
    CAppUI::conf("pharmacie Display show_dispensation_dossier_soins", $current_group)
) {
  $module->registerTab("vw_dispensation", TAB_READ);
}

if (isset(CModule::$active["dPstock"])) {
  $module->registerTab("vw_stocks_service"  , TAB_EDIT);
}
